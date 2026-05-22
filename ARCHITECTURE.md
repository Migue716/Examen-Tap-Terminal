# Arquitectura — Tap Terminal

Documentación de la arquitectura del sistema full stack del examen de admisión (Área de Desarrollo).

---

## Modos de ejecución

| Modo | Cuándo usar | MongoDB | Correo |
|------|-------------|---------|--------|
| **Local** (recomendado) | Desarrollo diario en Windows | `127.0.0.1:27017` | Mailpit nativo → `127.0.0.1:1025` |
| **Docker** (opcional) | Entorno aislado / equipo sin PHP local | Host `:27018` → contenedor | SMTP → `mailpit:1025` |

---

## Stack tecnológico

| Capa | Tecnología |
|------|------------|
| Frontend | Angular 19, TypeScript, Angular Material |
| API | Laravel 11, PHP 8.2 |
| Autenticación | Laravel Sanctum (Bearer token) |
| Base de datos | MongoDB 7 |
| Correo (desarrollo) | Mailpit (binario o Docker) + `PasswordResetMail` (HTML) |
| Explorador DB (Docker) | Mongo Express |
| Exportaciones | DomPDF (PDF), Maatwebsite Excel |

---

## Arranque local (3 procesos)

```mermaid
flowchart LR
    T1["Terminal 1<br/>start-mailpit.ps1"]
    T2["Terminal 2<br/>php artisan serve"]
    T3["Terminal 3<br/>npm start"]
    T1 --> MP[Mailpit :8025]
    T2 --> API[Laravel :8000]
    T3 --> UI[Angular :4200]
    UI --> API
    API --> MP
    API --> DB[(MongoDB :27017)]
```

| Paso | Comando | URL |
|------|---------|-----|
| 1 | `.\scripts\start-mailpit.ps1` | http://localhost:8025 |
| 2 | `cd backend` → `php artisan serve` | http://localhost:8000 |
| 3 | `cd frontend` → `npm start` | http://localhost:4200 |

---

## Vista de despliegue — local

```mermaid
flowchart TB
    subgraph Host["PC del desarrollador"]
        Browser["Navegador"]
        Compass["MongoDB Compass"]
        FE["Angular :4200"]
        BE["Laravel :8000"]
        MP["Mailpit :8025<br/>tools/mailpit/mailpit.exe"]
        Mongo[(MongoDB Server :27017)]
    end

    Browser --> FE
    Browser --> MP
    FE -->|"REST + Bearer"| BE
    BE -->|"SMTP :1025"| MP
    BE --> Mongo
    Compass --> Mongo
```

### Puertos (local)

| Componente | URL / conexión |
|------------|----------------|
| Frontend | http://localhost:4200 |
| API + Swagger | http://localhost:8000 · `/api/documentation` |
| Mailpit | http://localhost:8025 · `.\scripts\start-mailpit.ps1` |
| MongoDB | `mongodb://127.0.0.1:27017/tapterminal` |

---

## Vista de despliegue — Docker (opcional)

```mermaid
flowchart TB
    subgraph Cliente
        Browser[":4200"]
        MEX_UI["Mongo Express :8081"]
        Mail_UI["Mailpit :8025"]
    end

    subgraph Docker
        FE["frontend"]
        BE["backend"]
        DB[(mongodb)]
        MEX["mongo-express"]
        MP["mailpit"]
    end

    Browser --> FE --> BE
    BE --> DB
    BE --> MP
    MEX --> DB
    MEX_UI --> MEX
    Mail_UI --> MP
```

| Componente | Host |
|------------|------|
| MongoDB (contenedor) | `127.0.0.1:27018` (evita conflicto con Mongo local en 27017) |
| Mongo Express | http://localhost:8081 — `admin` / `tapterminal` |
| Mailpit | http://localhost:8025 |

---

## Vista lógica por capas

```mermaid
flowchart LR
    subgraph Presentación
        UI["Angular Material<br/>Login · Shell · CRUD"]
        INT["Interceptor + Guards"]
        TH["ThemeService"]
    end

    subgraph API["API REST /api"]
        AUTH["AuthController"]
        CRUD["Product · User · Profile"]
        EXP["ExportController"]
        MW["auth:sanctum + section"]
    end

    subgraph Dominio
        SVC["CodeGenerator · AuditLog"]
        MAIL["PasswordResetMail"]
    end

    subgraph Datos
        MONGO[(MongoDB tapterminal)]
        TOK["personal_access_tokens"]
        AUD["audit_logs"]
    end

    UI --> INT --> API
    AUTH --> MAIL
    CRUD --> MW --> SVC --> MONGO
    AUTH --> TOK
    SVC --> AUD
    EXP --> MONGO
```

---

## Flujo de autenticación

```mermaid
sequenceDiagram
    participant U as Usuario
    participant A as Angular :4200
    participant L as Laravel :8000
    participant M as MongoDB
    participant P as Mailpit :8025

    U->>A: Login
    A->>L: POST /api/auth/login
    L->>M: Validar Hash + perfiles
    L->>M: Token Sanctum
    L-->>A: token + user
    A->>A: localStorage tap_token

    U->>A: Recuperar contraseña
    A->>L: POST /api/auth/forgot-password
    L->>M: Contraseña temporal
    L->>P: PasswordResetMail (HTML)
    U->>P: Ver correo en UI
```

---

## Recuperación de contraseña

1. Angular → `POST /api/auth/forgot-password` con `username` (email).
2. Laravel genera contraseña temporal y actualiza `users.password` (Hash).
3. Envía `App\Mail\PasswordResetMail` (vistas `emails/password-reset*.blade.php`).
4. Mailpit recibe SMTP en `127.0.0.1:1025`; UI en http://localhost:8025.

---

## Modelo de datos (MongoDB)

Base: **`tapterminal`**

| Colección | Contenido |
|-----------|-----------|
| `users` | Usuarios, credenciales, `profile_ids`, `is_admin` |
| `profiles` | Roles y `section_ids` |
| `sections` | Módulos (`productos`, `usuarios`, `perfiles`), `can_write` |
| `products` | Catálogo (código PRD, precio 0–999) |
| `personal_access_tokens` | Tokens Sanctum (`_id` string) |
| `audit_logs` | Bitácora create / update / delete |
| `counters` | Secuencias PRD / USR / PFL |

```mermaid
erDiagram
    USERS ||--o{ PROFILES : profile_ids
    PROFILES ||--o{ SECTIONS : section_ids
    USERS ||--o{ AUDIT_LOGS : user_id
```

---

## RBAC por secciones

Módulos: `productos`, `usuarios`, `perfiles`.

- Middleware `section:{modulo}` — lectura.
- Middleware `section:{modulo},write` — alta/edición/baja.
- `is_admin = true` — acceso total.

---

## Estructura del repositorio

```
Examen Tap Terminal/
├── frontend/src/app/
│   ├── auth/              # login, recuperar contraseña
│   ├── core/              # AuthService, interceptor, theme
│   ├── layout/            # shell
│   ├── products|users|profiles/
├── backend/
│   ├── app/Mail/PasswordResetMail.php
│   ├── app/Services/      # AuditLog, CodeGenerator
│   ├── resources/views/emails/
│   ├── config/mail.php
│   ├── .env.example
│   └── .env.docker.example
├── scripts/
│   ├── setup-windows.ps1
│   ├── start-mailpit.ps1  # Mailpit sin Docker
│   └── start-local.ps1
├── tools/mailpit/         # binario (gitignored)
├── docker-compose.yml
├── ARCHITECTURE.md
└── README.md
```

---

## Endpoints principales

| Método | Ruta | Auth |
|--------|------|------|
| POST | `/api/auth/login` | No |
| POST | `/api/auth/forgot-password` | No |
| POST | `/api/auth/logout` | Sí |
| GET | `/api/auth/me` | Sí |
| GET | `/api/sections` | Sí |
| CRUD | `/api/products`, `/users`, `/profiles` | Sí + sección |
| GET | `/api/*-export/{pdf\|excel}` | Sí + sección |

---

## Variables de entorno

### Local (`backend/.env`)

```env
MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DATABASE=tapterminal
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
FRONTEND_URL=http://localhost:4200
```

### Docker (`docker-compose.yml` → backend)

```env
MONGODB_URI=mongodb://mongodb:27017
MAIL_HOST=mailpit
MAIL_PORT=1025
```

---

## Diagrama ASCII

```
┌─────────────────────────────────────────┐
│  Angular 19          localhost:4200     │
└────────────────────┬────────────────────┘
                     │ Bearer JSON
┌────────────────────▼────────────────────┐
│  Laravel 11          localhost:8000     │
│  Sanctum · RBAC · PDF/Excel · audit     │
└─────────┬──────────────────┬────────────┘
          │ SMTP :1025       │
          ▼                  ▼
┌─────────────────┐  ┌──────────────────────┐
│ Mailpit :8025   │  │ MongoDB :27017       │
│ (sin Docker)    │  │ DB: tapterminal      │
└─────────────────┘  └──────────────────────┘
```

---

## Referencias

- Instalación y arranque: [README.md](./README.md)
- Postman: `postman/TapTerminal.postman_collection.json`
- Mailpit: https://mailpit.axllent.org/docs/install/

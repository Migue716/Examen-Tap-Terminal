# Arquitectura — Tap Terminal

Documentación de la arquitectura del sistema full stack del examen de admisión (Área de Desarrollo).

## Stack tecnológico

| Capa | Tecnología |
|------|------------|
| Frontend | Angular 19, TypeScript, Angular Material |
| API | Laravel 11, PHP 8.2 |
| Autenticación | Laravel Sanctum (Bearer token) |
| Base de datos | MongoDB 7 |
| Correo (desarrollo) | Mailpit (SMTP + UI web) |
| Explorador DB (desarrollo) | Mongo Express |
| Exportaciones | DomPDF (PDF), Maatwebsite Excel |
| Contenedores | Docker Compose |

---

## Servicios Docker Compose

| Servicio | Imagen | Puerto(s) host | Función |
|----------|--------|------------------|---------|
| `frontend` | node:22 | 4200 | SPA Angular |
| `backend` | build `./backend` | 8000 | API Laravel + Swagger |
| `mongodb` | mongo:7 | **27018** → 27017 | Base de datos del proyecto |
| `mongo-express` | mongo-express | 8081 | UI web de MongoDB |
| `mailpit` | axllent/mailpit | 8025 (UI), 1025 (SMTP) | Correos de desarrollo |

Volumen persistente: `mongodb_data` (datos de MongoDB).

---

## Vista de despliegue

```mermaid
flowchart TB
    subgraph Cliente["Cliente (navegador / herramientas)"]
        Browser["Angular<br/>:4200"]
        Swagger["Swagger<br/>:8000/api/documentation"]
        MEX_UI["Mongo Express<br/>:8081"]
        Mail_UI["Mailpit<br/>:8025"]
        Compass["Compass / mongosh<br/>:27018"]
    end

    subgraph Docker["Docker Compose — red interna"]
        FE["frontend"]
        BE["backend<br/>Laravel 11"]
        DB[(mongodb<br/>alias: mongo)]
        MEX["mongo-express"]
        MP["mailpit"]
    end

    Browser --> FE
    Swagger --> BE
    MEX_UI --> MEX
    Mail_UI --> MP
    Compass -->|"127.0.0.1:27018"| DB
    FE -->|"REST + Bearer"| BE
    BE -->|"mongodb://mongodb:27017"| DB
    BE -->|"SMTP :1025"| MP
    MEX -->|"mongodb://mongo:27017"| DB
```

### Puertos y URLs

| Componente | URL / conexión | Rol |
|------------|----------------|-----|
| Frontend | http://localhost:4200 | Panel de administración |
| API + Swagger | http://localhost:8000 · http://localhost:8000/api/documentation | REST y documentación |
| MongoDB (desde el PC) | `mongodb://127.0.0.1:27018/tapterminal` | Compass, scripts locales |
| MongoDB (desde contenedores) | `mongodb://mongodb:27017` o `mongodb://mongo:27017` | Backend, Mongo Express |
| Mongo Express | http://localhost:8081 — `admin` / `tapterminal` | Explorar colecciones en el navegador |
| Mailpit | http://localhost:8025 | Ver correos de recuperar contraseña |

> **Puerto 27018:** Mongo de Docker se publica en **27018** en el host para no chocar con MongoDB instalado en Windows, que suele usar **27017** en `127.0.0.1`.

---

## Acceso a MongoDB (Docker vs local)

En muchos equipos coexisten dos instancias:

| Instancia | Cómo detectarla | Conexión desde Compass |
|-----------|-----------------|------------------------|
| MongoDB de **Windows** | Servicio `mongod.exe`, puerto `127.0.0.1:27017` | `mongodb://127.0.0.1:27017` — **no** es la del proyecto |
| MongoDB de **Docker** | `docker compose ps mongodb` | `mongodb://127.0.0.1:27018/tapterminal` |

**Mongo Express** siempre usa la red Docker (`mongo:27017`), por eso en http://localhost:8081/db/tapterminal ves los datos correctos aunque Compass en 27017 muestre otra cosa.

Base de datos del proyecto: **`tapterminal`**.

---

## Vista lógica por capas

```mermaid
flowchart LR
    subgraph Presentación
        UI["Angular Material<br/>Login · Shell · CRUD"]
        INT["HTTP Interceptor<br/>+ Auth Guard"]
        TH["ThemeService<br/>modo claro/oscuro"]
    end

    subgraph API["API REST /api"]
        AUTH["AuthController<br/>login · logout · me · forgot-password"]
        CRUD["Product · User · Profile<br/>Controllers"]
        EXP["ExportController<br/>PDF · Excel"]
        MW1["auth:sanctum"]
        MW2["section:module<br/>RBAC por sección"]
    end

    subgraph Dominio
        SVC["Services<br/>CodeGenerator · AuditLog"]
        MAIL["PasswordResetMail<br/>plantilla HTML"]
        MDL["Models<br/>User · Profile · Section · Product"]
    end

    subgraph Datos
        MONGO[(MongoDB)]
        TOK["personal_access_tokens"]
        AUD["audit_logs"]
    end

    UI --> INT --> API
    UI --> TH
    AUTH --> MW1
    AUTH --> MAIL
    CRUD --> MW1 --> MW2
    MW2 --> SVC --> MDL --> MONGO
    AUTH --> TOK
    SVC --> AUD
    EXP --> MDL
```

---

## Flujo de autenticación (Sanctum)

```mermaid
sequenceDiagram
    participant U as Usuario
    participant A as Angular
    participant L as Laravel API
    participant M as MongoDB
    participant P as Mailpit

    U->>A: Correo + contraseña
    A->>L: POST /api/auth/login
    L->>M: Buscar user + Hash::check
    L->>M: Crear token Sanctum
    L-->>A: token + user (sections, write_sections)
    A->>A: localStorage tap_token

    Note over A,L: Rutas protegidas
    A->>L: Authorization Bearer {token}
    L->>M: Validar token
    L-->>A: JSON

    U->>A: Recuperar contraseña
    A->>L: POST /api/auth/forgot-password
    L->>M: Contraseña temporal (Hash)
    L->>P: PasswordResetMail (HTML + texto)
    L-->>A: Mensaje de éxito
    U->>P: Revisar correo en :8025
```

### Recuperación de contraseña

1. Genera contraseña temporal (`Str::password`).
2. La guarda cifrada en `users`.
3. Envía `PasswordResetMail` (vista `emails/password-reset.blade.php`).
4. En Docker el SMTP apunta a **mailpit** (`MAIL_HOST=mailpit`, puerto `1025`).

Tras usar recuperar contraseña, el seed deja de coincidir con la clave anterior; restaurar con `php artisan db:seed` o leer la clave en Mailpit.

---

## Modelo de datos (MongoDB)

```mermaid
erDiagram
    USERS ||--o{ PROFILES : "profile_ids"
    PROFILES ||--o{ SECTIONS : "section_ids"
    USERS ||--o{ AUDIT_LOGS : "user_id"

    USERS {
        string _id
        string code
        string username
        string password
        boolean is_admin
        array profile_ids
    }
    PROFILES {
        string _id
        string code
        string name
        array section_ids
    }
    SECTIONS {
        string _id
        string code
        string module
        boolean can_write
    }
    PRODUCTS {
        string _id
        string code
        string name
        string brand
        int price
    }
    AUDIT_LOGS {
        string entity
        string action
        object previous_data
        object current_data
    }
```

### Colecciones principales

| Colección | Contenido |
|-----------|-----------|
| `users` | Usuarios, credenciales, perfiles asignados |
| `profiles` | Roles y permisos por sección |
| `sections` | Módulos (`productos`, `usuarios`, `perfiles`) y lectura/escritura |
| `products` | Catálogo de productos |
| `personal_access_tokens` | Tokens Sanctum (MongoDB `_id` como string) |
| `audit_logs` | Bitácora create / update / delete |
| `counters` | Secuencias para códigos automáticos (PRD, USR, PFL) |

---

## Módulos funcionales y permisos (RBAC)

```mermaid
flowchart TB
    ADMIN["is_admin = true<br/>Acceso total"]

    subgraph Módulos
        P["productos<br/>SEC-PRD-R / SEC-PRD-W"]
        U["usuarios<br/>SEC-USR-R / SEC-USR-W"]
        PF["perfiles<br/>SEC-PFL-R / SEC-PFL-W"]
    end

    USER["Usuario + Perfiles"]
    USER --> P
    USER --> U
    USER --> PF
    ADMIN --> P
    ADMIN --> U
    ADMIN --> PF

    P --> API_P["/api/products<br/>+ export pdf|excel"]
    U --> API_U["/api/users<br/>+ export pdf|excel"]
    PF --> API_F["/api/profiles<br/>+ export pdf|excel"]
```

El middleware `section` valida acceso al módulo. Las rutas con sufijo `,write` exigen sección con `can_write: true` o ser administrador.

---

## Estructura del repositorio

```
Examen Tap Terminal/
├── frontend/                      # Angular 19
│   └── src/app/
│       ├── auth/                  # Login, recuperar contraseña
│       ├── core/                  # AuthService, interceptor, guards, theme
│       ├── layout/                # Shell (sidebar + topbar)
│       ├── products/
│       ├── users/
│       └── profiles/
├── backend/                       # Laravel 11 API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   ├── Http/Middleware/       # CheckSectionAccess
│   │   ├── Mail/                  # PasswordResetMail
│   │   ├── Models/
│   │   └── Services/              # AuditLog, CodeGenerator
│   ├── config/mail.php
│   ├── resources/views/emails/    # password-reset (HTML + texto)
│   ├── database/seeders/
│   └── routes/api.php
├── docker-compose.yml             # mongodb, mongo-express, mailpit, backend, frontend
├── ARCHITECTURE.md                # Este documento
├── postman/
└── README.md
```

---

## Endpoints principales

| Método | Ruta | Auth | Descripción |
|--------|------|------|-------------|
| POST | `/api/auth/login` | No | Iniciar sesión |
| POST | `/api/auth/forgot-password` | No | Recuperar contraseña (correo HTML) |
| POST | `/api/auth/logout` | Sí | Cerrar sesión |
| GET | `/api/auth/me` | Sí | Usuario actual |
| GET | `/api/sections` | Sí | Listar secciones |
| GET/POST/PUT/DELETE | `/api/products` | Sí + sección | CRUD productos |
| GET/POST/PUT/DELETE | `/api/users` | Sí + sección | CRUD usuarios |
| GET/POST/PUT/DELETE | `/api/profiles` | Sí + sección | CRUD perfiles |
| GET | `/api/*-export/{pdf\|excel}` | Sí + sección | Exportaciones |

---

## Variables de entorno relevantes (backend)

| Variable | Docker Compose | Uso |
|----------|------------------|-----|
| `MONGODB_URI` | `mongodb://mongodb:27017` | Conexión desde el contenedor backend |
| `MONGODB_DATABASE` | `tapterminal` | Nombre de la base |
| `FRONTEND_URL` | `http://localhost:4200` | Enlace en correo de recuperación |
| `MAIL_MAILER` | `smtp` | Envío real a Mailpit |
| `MAIL_HOST` | `mailpit` | Host SMTP en red Docker |
| `MAIL_PORT` | `1025` | Puerto SMTP Mailpit |

Desde el host (Compass, `php artisan` local): `MONGODB_URI=mongodb://127.0.0.1:27018`.

---

## Diagrama ASCII (resumen)

```
┌──────────────────────────────────────────────────────────┐
│  Angular 19 · :4200                                      │
│  Auth · RBAC en UI · Productos / Usuarios / Perfiles     │
└───────────────────────────┬──────────────────────────────┘
                            │ HTTP JSON (Bearer)
┌───────────────────────────▼──────────────────────────────┐
│  Laravel 11 · :8000                                        │
│  Sanctum · middleware section · PDF/Excel · audit_logs     │
│  Correo recuperación → Mailpit :8025                       │
└───────────────┬──────────────────────────┬─────────────────┘
                │                          │
                ▼                          ▼
┌───────────────────────────┐   ┌──────────────────────────┐
│  MongoDB (tapterminal)     │   │  Mailpit (SMTP :1025)    │
│  Host :27018 · Docker :27017│   │  UI :8025                │
│  Mongo Express :8081       │   └──────────────────────────┘
└───────────────────────────┘
```

---

## Referencias

- Instalación y credenciales de prueba: [README.md](./README.md)
- Colección Postman: `postman/TapTerminal.postman_collection.json`

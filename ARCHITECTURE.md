# Arquitectura — Tap Terminal

Documentación de la arquitectura del sistema full stack del examen de admisión (Área de Desarrollo).

## Stack tecnológico

| Capa | Tecnología |
|------|------------|
| Frontend | Angular 19, TypeScript, Angular Material |
| API | Laravel 11, PHP 8.2 |
| Autenticación | Laravel Sanctum (Bearer token) |
| Base de datos | MongoDB 7 |
| Exportaciones | DomPDF (PDF), Maatwebsite Excel |
| Contenedores | Docker Compose (frontend, backend, mongodb) |

---

## Vista de despliegue

```mermaid
flowchart TB
    subgraph Cliente["Cliente"]
        Browser["Navegador<br/>:4200"]
        Swagger["Swagger UI<br/>:8000/api/documentation"]
        Postman["Postman / cURL"]
    end

    subgraph Docker["Docker Compose"]
        FE["Frontend<br/>Angular 19 · Node 22<br/>:4200"]
        BE["Backend<br/>Laravel 11 · PHP 8.2<br/>:8000"]
        DB[(MongoDB 7<br/>:27017)]
        MEX["Mongo Express<br/>:8081"]
    end

    Browser --> FE
    Browser --> MEX
    Swagger --> BE
    Postman --> BE
    FE -->|"REST JSON<br/>Bearer token"| BE
    MEX -->|"MongoDB driver"| DB
    BE -->|"MongoDB driver"| DB
```

### Puertos

| Componente | Puerto | Rol |
|------------|--------|-----|
| Angular (frontend) | 4200 | SPA, UI, guards, interceptor Bearer |
| Laravel (backend) | 8000 | API REST, Sanctum, RBAC, exportaciones |
| MongoDB (Docker) | **27018** en el host (27017 en la red Docker) | Persistencia en documentos |
| Mongo Express | 8081 | UI web para explorar la base de datos |
| Swagger | 8000 | Documentación y pruebas de API |

---

## Vista lógica por capas

```mermaid
flowchart LR
    subgraph Presentación
        UI["Angular Material<br/>Login · Shell · CRUD"]
        INT["HTTP Interceptor<br/>+ Auth Guard"]
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
        MDL["Models<br/>User · Profile · Section · Product"]
    end

    subgraph Datos
        MONGO[(MongoDB)]
        TOK["personal_access_tokens"]
        AUD["audit_logs"]
    end

    UI --> INT --> API
    AUTH --> MW1
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

    U->>A: Correo + contraseña
    A->>L: POST /api/auth/login
    L->>M: Buscar user + verificar Hash
    M-->>L: Usuario + perfiles/secciones
    L->>M: Crear token Sanctum
    L-->>A: token + user (sections, write_sections)
    A->>A: localStorage tap_token

    Note over A,L: Peticiones protegidas
    A->>L: GET/POST ... Authorization: Bearer {token}
    L->>M: Validar token + cargar usuario
    L-->>A: JSON respuesta

    U->>A: Recuperar contraseña
    A->>L: POST /api/auth/forgot-password
    L->>M: Nueva contraseña temporal
    L->>L: Enviar correo (Mail log/SMTP)
```

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
| `personal_access_tokens` | Tokens Sanctum |
| `audit_logs` | Bitácora create/update/delete |
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

El middleware `section` valida que el usuario tenga acceso al módulo solicitado. Las rutas con sufijo `,write` exigen sección con `can_write: true` (o ser administrador).

---

## Estructura del repositorio

```
Examen Tap Terminal/
├── frontend/                 # Angular 19
│   └── src/app/
│       ├── auth/             # Login, recuperar contraseña
│       ├── core/             # AuthService, interceptor, guards, API
│       ├── layout/           # Shell (sidebar + topbar)
│       ├── products/
│       ├── users/
│       └── profiles/
├── backend/                  # Laravel 11 API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   ├── Http/Middleware/  # CheckSectionAccess
│   │   ├── Models/
│   │   └── Services/         # AuditLog, CodeGenerator
│   ├── database/seeders/
│   └── routes/api.php
├── docker-compose.yml
├── postman/
└── README.md
```

---

## Endpoints principales

| Método | Ruta | Auth | Descripción |
|--------|------|------|-------------|
| POST | `/api/auth/login` | No | Iniciar sesión |
| POST | `/api/auth/forgot-password` | No | Recuperar contraseña |
| POST | `/api/auth/logout` | Sí | Cerrar sesión |
| GET | `/api/auth/me` | Sí | Usuario actual |
| GET/POST/PUT/DELETE | `/api/products` | Sí + sección | CRUD productos |
| GET/POST/PUT/DELETE | `/api/users` | Sí + sección | CRUD usuarios |
| GET/POST/PUT/DELETE | `/api/profiles` | Sí + sección | CRUD perfiles |
| GET | `/api/*-export/{pdf\|excel}` | Sí + sección | Exportaciones |

---

## Diagrama ASCII (resumen)

```
┌─────────────────────────────────────────────────────────┐
│  Angular 19 + Material + TypeScript                     │
│  · AuthService / Guards / Interceptor                   │
│  · Módulos: Productos, Usuarios, Perfiles               │
└──────────────────────────┬──────────────────────────────┘
                           │ HTTP JSON (Bearer)
┌──────────────────────────▼──────────────────────────────┐
│  Laravel 11 API                                         │
│  · Sanctum (Bearer)                                     │
│  · Middleware section (RBAC)                            │
│  · DomPDF + Maatwebsite Excel                           │
│  · Bitácora audit_logs                                  │
└──────────────────────────┬──────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────┐
│  MongoDB (users, products, profiles, sections, …)     │
└─────────────────────────────────────────────────────────┘
```

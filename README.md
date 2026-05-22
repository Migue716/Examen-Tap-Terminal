# Tap Terminal — Examen de Admisión (Área de Desarrollo)

Sistema full stack según el examen de Tap Terminal:

- **Backend:** Laravel 11 + PHP 8.2 + MongoDB
- **Frontend:** Angular 19 + TypeScript + Angular Material
- **Auth:** Laravel Sanctum (Bearer token)
- **Exportaciones:** PDF (DomPDF) y Excel (Maatwebsite)
- **Bitácora:** colección `audit_logs` con datos anteriores y actuales
- **Correo (dev):** Mailpit (binario nativo o Docker)

Documentación de arquitectura: **[ARCHITECTURE.md](./ARCHITECTURE.md)**

---

## Arrancar el proyecto (local)

Requisitos: **PHP 8.2** (ext. `mongodb`), **Composer**, **MongoDB** en `127.0.0.1:27017`, **Node.js 22**.

Abre **3 terminales** en la raíz del proyecto:

### Terminal 1 — Mailpit

```powershell
.\scripts\start-mailpit.ps1
```

- Primera ejecución: descarga `mailpit.exe` en `tools/mailpit/`
- UI: http://localhost:8025
- Deja esta terminal abierta

Alternativa con Docker: `docker compose up -d mailpit`

### Terminal 2 — Backend

```powershell
cd backend
php artisan serve
```

- API: http://localhost:8000
- Swagger: http://localhost:8000/api/documentation

### Terminal 3 — Frontend

```powershell
cd frontend
npm start
```

- App: http://localhost:4200

### URLs

| Servicio | URL |
|----------|-----|
| Aplicación | http://localhost:4200 |
| API | http://localhost:8000 |
| Swagger | http://localhost:8000/api/documentation |
| Mailpit | http://localhost:8025 |
| MongoDB (Compass) | `mongodb://127.0.0.1:27017/tapterminal` |

---

## Primera instalación

### Windows (automático)

```powershell
winget install PHP.PHP.8.2 MongoDB.Server
# Cierra y abre la terminal, luego:
.\scripts\setup-windows.ps1
```

### Manual

```powershell
cd backend
composer install
copy .env.example .env
php artisan key:generate
php artisan db:seed
```

```powershell
cd frontend
npm install
```

Guía de comandos: `.\scripts\start-local.ps1`

---

## Usuarios de prueba (seed)

| Correo | Contraseña | Rol |
|--------|------------|-----|
| `admin@tapterminal.com` | `Admin123!` | Administrador |
| `miguel.gr716@gmail.com` | `Migue716$` | Administrador |
| `usuario01@tapterminal.com` … `usuario20@…` | `Test123!` | Según perfil |

Restaurar contraseñas tras recuperar acceso:

```powershell
cd backend
php artisan db:seed --force
```

O revisa la contraseña temporal en http://localhost:8025 (Mailpit).

---

## Configuración (`backend/.env`)

```env
MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DATABASE=tapterminal

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS=noreply@tapterminal.com
```

Sin Mailpit: `MAIL_MAILER=log` (correos en `backend/storage/logs/laravel.log`).

Referencia Docker: `backend/.env.docker.example`

---

## Scripts útiles

| Script | Descripción |
|--------|-------------|
| `scripts/setup-windows.ps1` | PHP, Composer, `.env`, seed |
| `scripts/start-mailpit.ps1` | Mailpit **sin Docker** |
| `scripts/start-local.ps1` | Resumen de comandos locales |

---

## Módulos

| Módulo | Consulta | Alta/Edición/Eliminación | Export PDF/Excel |
|--------|----------|---------------------------|------------------|
| Productos | Sí | Según perfil | Sí |
| Usuarios | Sí | Según perfil | Sí |
| Perfiles | Sí | Según perfil | Sí |

---

## MongoDB y Compass

- **Proyecto local:** `mongodb://127.0.0.1:27017/tapterminal`
- Si Compass en **27017** no muestra `tapterminal`, puede que estés viendo otro Mongo instalado en Windows.
- **Mongo Express** (solo Docker): http://localhost:8081 — `admin` / `tapterminal`

---

## Ejecución con Docker (opcional)

```bash
docker compose up --build
docker compose exec backend php artisan db:seed --force
```

| Servicio | URL |
|----------|-----|
| Frontend | http://localhost:4200 |
| API | http://localhost:8000 |
| Mailpit | http://localhost:8025 |
| Mongo Express | http://localhost:8081 |

Mongo del contenedor en el host (si tienes Mongo local en 27017):

```
mongodb://127.0.0.1:27018/tapterminal
```

Tras cambiar correo en Docker: `docker compose restart backend`

---

## Documentación API

### Swagger

1. Abre http://localhost:8000/api/documentation
2. `POST /auth/login` → copia el `token`
3. **Authorize** → `Bearer {token}`

Regenerar docs:

```bash
cd backend
php artisan l5-swagger:generate
```

### Postman

`postman/TapTerminal.postman_collection.json`

---

## Criterios del examen cubiertos

- Códigos automáticos (PRD, USR, PFL)
- Fechas `DD/MM/YYYY HH:MM`
- Precio máximo 3 dígitos (0–999)
- Contraseñas cifradas (`Hash::make`)
- Recuperación de contraseña por correo (HTML + Mailpit)
- Acceso por secciones según perfiles
- Bitácora en create/update/delete

# Tap Terminal — Examen de Admisión (Área de Desarrollo)

Sistema full stack según el examen de Tap Terminal:

- **Backend:** Laravel 11 + PHP 8.2 + MongoDB
- **Frontend:** Angular 19 + TypeScript + Angular Material
- **Auth:** Laravel Sanctum (Bearer token)
- **Exportaciones:** PDF (DomPDF) y Excel (Maatwebsite)
- **Bitácora:** colección `audit_logs` con datos anteriores y actuales

Ver **[ARCHITECTURE.md](./ARCHITECTURE.md)** para diagramas de despliegue, capas, autenticación y modelo de datos.

## Módulos

| Módulo    | Consulta | Alta/Edición/Eliminación | Export PDF/Excel |
|-----------|----------|---------------------------|------------------|
| Productos | Sí       | Según perfil              | Sí               |
| Usuarios  | Sí       | Según perfil              | Sí               |
| Perfiles  | Sí       | Según perfil              | Sí               |

## Usuarios iniciales (seed)

| Correo | Contraseña | Rol |
|--------|------------|-----|
| `admin@tapterminal.com` | `Admin123!` | Administrador |
| `miguel.gr716@gmail.com` | `Migue716$` | Administrador |
| `usuario01@tapterminal.com` … `usuario20@tapterminal.com` | `Test123!` | Según perfil |

Si el login devuelve `401` tras **recuperar contraseña**, ejecuta `php artisan db:seed --force` o revisa el correo en el log / Mailpit.

## Requisitos (desarrollo local)

- PHP 8.2 + extensión `mongodb`
- Composer
- MongoDB Server (puerto **27017**)
- Node.js 22

## Ejecución local (recomendado)

### 1. Instalar dependencias (Windows)

```powershell
winget install PHP.PHP.8.2 MongoDB.Server
# Cierra y abre la terminal, luego:
.\scripts\setup-windows.ps1
```

El script configura PHP, Composer, crea `backend/.env` y ejecuta el seed.

### 2. Iniciar servicios

**Terminal 1 — API:**

```bash
cd backend
composer install
cp .env.example .env   # si no existe
php artisan key:generate
php artisan db:seed
php artisan serve
```

**Terminal 2 — Frontend:**

```bash
cd frontend
npm install
npm start
```

Antes de iniciar el backend, levanta **Mailpit** (correos de recuperar contraseña):

**Sin Docker** — binario nativo (Windows):

```powershell
.\scripts\start-mailpit.ps1
```

La primera vez descarga `mailpit.exe` en `tools/mailpit/`. UI: http://localhost:8025

**Con Docker:**

```bash
docker compose up -d mailpit
```

O ejecuta `.\scripts\start-local.ps1` para ver todos los comandos.

### 3. Abrir

| Servicio | URL |
|----------|-----|
| App | http://localhost:4200 |
| API | http://localhost:8000 |
| Swagger | http://localhost:8000/api/documentation |
| **Mailpit** (correos) | http://localhost:8025 |

### Configuración local (`backend/.env`)

```env
MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DATABASE=tapterminal
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

- **MongoDB Compass:** `mongodb://127.0.0.1:27017/tapterminal`
- **Recuperar contraseña:** revisa el correo HTML en http://localhost:8025

### Seed en local

```bash
cd backend
php artisan db:seed --force
```

---

## Ejecución con Docker (opcional)

1. Iniciar Docker Desktop.
2. En la raíz del proyecto:

```bash
docker compose up --build
```

3. Seed:

```bash
docker compose exec backend php artisan db:seed --force
```

4. URLs extra:

| Servicio | URL |
|----------|-----|
| Mailpit | http://localhost:8025 |
| Mongo Express | http://localhost:8081 (`admin` / `tapterminal`) |

En Docker, el backend usa variables del `docker-compose.yml` (`mongodb://mongodb:27017`, SMTP → Mailpit).  
Mongo del contenedor se publica en el host en el puerto **27018** (para no chocar con Mongo local en 27017):

```
mongodb://127.0.0.1:27018/tapterminal
```

Referencia: `backend/.env.docker.example`

### Correo en Docker (Mailpit)

```bash
docker compose restart backend
```

---

## Documentación API

### Swagger UI

**http://localhost:8000/api/documentation**

1. `POST /auth/login` → copiar `token`
2. **Authorize** → `Bearer {token}`

```bash
cd backend
php artisan l5-swagger:generate
```

### Postman

`postman/TapTerminal.postman_collection.json`

## Criterios del examen cubiertos

- Códigos automáticos (PRD, USR, PFL)
- Fechas `DD/MM/YYYY HH:MM`
- Precio máximo 3 dígitos (0–999)
- Contraseñas cifradas (`Hash::make`)
- Recuperación de contraseña por correo
- Acceso por secciones según perfiles
- Bitácora en create/update/delete

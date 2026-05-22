# Tap Terminal — Examen de Admisión (Área de Desarrollo)

Sistema full stack según el examen de Tap Terminal:

- **Backend:** Laravel 11 + PHP 8.2 + MongoDB
- **Frontend:** Angular 19 + TypeScript + Angular Material
- **Auth:** Laravel Sanctum (Bearer token)
- **Exportaciones:** PDF (DomPDF) y Excel (Maatwebsite)
- **Bitácora:** colección `audit_logs` con datos anteriores y actuales

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

## Requisitos

- Docker Desktop (recomendado), o PHP 8.2 + Composer + MongoDB + Node 22

## Instalación en Windows (sin Docker)

Con **winget** (PowerShell como administrador opcional):

```powershell
winget install PHP.PHP.8.2 MongoDB.Server
```

Luego instala Composer y la extensión MongoDB para PHP (ver `scripts/setup-windows.ps1`) o sigue manualmente:

1. Cierra y abre una terminal nueva para refrescar el PATH.
2. Ejecuta `scripts/setup-windows.ps1` desde la raíz del proyecto.
3. Sigue los pasos de **Ejecución local** más abajo.

## Ejecución con Docker

1. Iniciar Docker Desktop.
2. En la raíz del proyecto:

```bash
docker compose up --build
```

3. En otra terminal, dentro del contenedor backend (o con PHP local):

```bash
docker compose exec backend composer install
docker compose exec backend cp .env.example .env
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan db:seed
```

4. Abrir:

- Frontend: http://localhost:4200
- API: http://localhost:8000

## Ejecución local (sin Docker)

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
# Configurar MONGODB_URI en .env
php artisan db:seed
php artisan serve
```

### Frontend

```bash
cd frontend
npm install
npm start
```

## Documentación API

### Swagger UI

Con el backend en marcha, abre:

**http://localhost:8000/api/documentation**

1. Ejecuta `POST /auth/login` y copia el `token`.
2. Pulsa **Authorize** e ingresa: `Bearer {tu_token}`.
3. Prueba el resto de endpoints.

Regenerar documentación:

```bash
cd backend
php artisan l5-swagger:generate
```

### Postman

Colección en `postman/TapTerminal.postman_collection.json`.

## Criterios del examen cubiertos

- Códigos automáticos (PRD, USR, PFL)
- Fechas `DD/MM/YYYY HH:MM`
- Precio máximo 3 dígitos (0–999)
- Contraseñas cifradas (`Hash::make`)
- Recuperación de contraseña por correo
- Acceso por secciones según perfiles
- Bitácora en create/update/delete

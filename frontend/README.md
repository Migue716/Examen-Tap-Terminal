# Tap Terminal — Frontend (Angular 19)

SPA del examen Tap Terminal. Documentación general del proyecto: [README.md](../README.md).

## Arranque

Desde la raíz del proyecto (con Mailpit y backend ya en marcha):

```bash
npm install
npm start
```

App: http://localhost:4200

API configurada en `src/environments/environment.ts`:

```typescript
apiUrl: 'http://localhost:8000/api'
```

## Estructura

| Carpeta | Contenido |
|---------|-----------|
| `auth/` | Login, recuperar contraseña |
| `core/` | AuthService, interceptor, guards, theme |
| `layout/` | Shell (sidebar, topbar) |
| `products/` | CRUD productos |
| `users/` | CRUD usuarios |
| `profiles/` | CRUD perfiles |

## Comandos Angular CLI

```bash
ng build          # producción → dist/
ng test           # unit tests
ng generate component nombre
```

Más información: [Angular CLI](https://angular.dev/tools/cli)

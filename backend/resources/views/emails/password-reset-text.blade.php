Tap Terminal — Recuperación de contraseña
==========================================

Hola, {{ $userName }},

Recibimos una solicitud para restablecer tu contraseña.

Usuario: {{ $username }}
Contraseña temporal: {{ $temporaryPassword }}

Inicia sesión en: {{ config('app.frontend_url', 'http://localhost:4200') }}/login

Si no solicitaste este cambio, ignora este mensaje o contacta al administrador.

— Tap Terminal (mensaje automático)

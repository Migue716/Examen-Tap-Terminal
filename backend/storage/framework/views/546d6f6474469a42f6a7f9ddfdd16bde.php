Tap Terminal — Recuperación de contraseña
==========================================

Hola, <?php echo e($userName); ?>,

Recibimos una solicitud para restablecer tu contraseña.

Usuario: <?php echo e($username); ?>

Contraseña temporal: <?php echo e($temporaryPassword); ?>


Inicia sesión en: <?php echo e(config('app.frontend_url', 'http://localhost:4200')); ?>/login

Si no solicitaste este cambio, ignora este mensaje o contacta al administrador.

— Tap Terminal (mensaje automático)
<?php /**PATH /var/www/html/resources/views/emails/password-reset-text.blade.php ENDPATH**/ ?>
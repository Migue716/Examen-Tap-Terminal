<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Recuperación de contraseña - Tap Terminal</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#e2e8f0;font-family:'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;-webkit-font-smoothing:antialiased;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#e2e8f0;padding:40px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#0f172a 100%);border-radius:16px 16px 0 0;padding:32px 36px;text-align:center;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center">
                                    <div style="display:inline-block;width:52px;height:52px;background:linear-gradient(135deg,#0ea5e9,#38bdf8);border-radius:14px;line-height:52px;font-size:26px;margin-bottom:16px;">
                                        &#128666;
                                    </div>
                                    <h1 style="margin:0;font-size:22px;font-weight:700;color:#f8fafc;letter-spacing:-0.02em;">Tap Terminal</h1>
                                    <p style="margin:8px 0 0;font-size:13px;color:#94a3b8;letter-spacing:0.02em;">Examen · Área de Desarrollo</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- Body -->
                <tr>
                    <td style="background-color:#ffffff;padding:36px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                        <p style="margin:0 0 8px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:#0ea5e9;">Recuperación de acceso</p>
                        <h2 style="margin:0 0 20px;font-size:20px;font-weight:700;color:#0f172a;letter-spacing:-0.02em;">Hola, <?php echo e($userName); ?></h2>
                        <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#475569;">
                            Recibimos una solicitud para restablecer tu contraseña. Usa las credenciales temporales siguientes para iniciar sesión. Te recomendamos cambiarla después de ingresar.
                        </p>
                        <!-- Credentials card -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:24px 28px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding-bottom:20px;border-bottom:1px solid #e2e8f0;">
                                                <p style="margin:0 0 6px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Usuario</p>
                                                <p style="margin:0;font-size:16px;font-weight:600;color:#0f172a;"><?php echo e($username); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top:20px;">
                                                <p style="margin:0 0 6px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Contraseña temporal</p>
                                                <p style="margin:0;font-size:18px;font-weight:700;color:#0369a1;font-family:Consolas,'Courier New',monospace;letter-spacing:0.04em;word-break:break-all;"><?php echo e($temporaryPassword); ?></p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <!-- CTA -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" style="padding-bottom:8px;">
                                    <a href="<?php echo e(config('app.frontend_url', 'http://localhost:4200')); ?>/login"
                                       style="display:inline-block;background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;padding:14px 32px;border-radius:10px;box-shadow:0 4px 14px rgba(14,165,233,0.35);">
                                        Iniciar sesión
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:24px 0 0;font-size:13px;line-height:1.5;color:#94a3b8;text-align:center;">
                            Este enlace te llevará al panel de administración de Tap Terminal.
                        </p>
                    </td>
                </tr>
                <!-- Footer -->
                <tr>
                    <td style="background-color:#f8fafc;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 16px 16px;padding:24px 36px;text-align:center;">
                        <p style="margin:0 0 8px;font-size:12px;line-height:1.5;color:#64748b;">
                            Si no solicitaste este cambio, ignora este mensaje o contacta al administrador del sistema.
                        </p>
                        <p style="margin:0;font-size:11px;color:#94a3b8;">
                            &copy; <?php echo e(date('Y')); ?> Tap Terminal · Mensaje automático, no responder.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
<?php /**PATH C:\Users\migue\Documents\GitHub\Examen Tap Terminal\backend\resources\views/emails/password-reset.blade.php ENDPATH**/ ?>
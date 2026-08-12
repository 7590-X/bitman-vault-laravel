<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nota Segura - BITMAN Vault</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #f1f5f9; -webkit-font-smoothing: antialiased;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #0b0f19; padding: 40px 10px;">
        <tr>
            <td align="center">
                <!-- Contenedor Principal (Tarjeta) -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #0f172a; border-radius: 16px; border: 1px solid #1e293b; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);">
                    
                    <!-- Encabezado con Gradiente y Logo -->
                    <tr>
                        <td align="center" style="padding: 32px 20px; background-color: #1e293b; border-bottom: 1px solid #334155;">
                            <h1 style="font-size: 22px; font-weight: 800; letter-spacing: -0.5px; color: #ffffff; margin: 0;">
                                BITMAN <span style="color: #3b82f6;">VAULT</span>
                            </h1>
                            <p style="font-size: 12px; color: #94a3b8; margin: 6px 0 0 0; text-transform: uppercase; letter-spacing: 1px;">
                                Sistema Gestor de Secretos Criptográficos
                            </p>
                        </td>
                    </tr>

                    <!-- Cuerpo del Mensaje -->
                    <tr>
                        <td style="padding: 36px 32px;">
                            <h2 style="font-size: 20px; font-weight: 700; color: #f8fafc; margin: 0 0 12px 0;">
                                🔒 Has recibido una Nota Segura
                            </h2>
                            <p style="font-size: 14px; color: #cbd5e1; line-height: 1.6; margin: 0 0 24px 0;">
                                Te han enviado una información confidencial protegida de un solo uso a través de BITMAN Vault.
                            </p>

                            <!-- Cuadro del Asunto / Título -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #1e293b; border-left: 4px solid #3b82f6; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; margin-bottom: 4px;">
                                            Asunto / Título de la Nota
                                        </div>
                                        <div style="font-size: 16px; font-weight: 600; color: #f8fafc;">
                                            {{ $nota->obtenerTitulo() }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Cuadro del Código de Apertura -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #020617; border: 1px dashed #334155; border-radius: 12px; margin-bottom: 28px;">
                                <tr>
                                    <td align="center" style="padding: 20px;">
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; font-weight: 600; margin-bottom: 8px;">
                                            CÓDIGO DE VERIFICACIÓN / APERTURA
                                        </div>
                                        <div style="font-family: 'Courier New', Courier, monospace; font-size: 28px; font-weight: 800; letter-spacing: 6px; color: #60a5fa;">
                                            {{ $nota->obtenerCodigoApertura() }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botón CTA Principal -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" style="background-color: #2563eb; border-radius: 10px; padding: 14px 32px;">
                                                    <a href="{{ $urlAcceso }}" target="_blank" style="color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none; display: inline-block;">
                                                        Aperturar Nota Segura
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 12px; color: #94a3b8; text-align: center; margin: 0 0 24px 0; line-height: 1.5;">
                                Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:<br />
                                <a href="{{ $urlAcceso }}" style="color: #60a5fa; text-decoration: underline; word-break: break-all;">{{ $urlAcceso }}</a>
                            </p>

                            <!-- Alerta de Autodestrucción -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #3b1111; border: 1px solid #7f1d1d; border-radius: 10px;">
                                <tr>
                                    <td style="padding: 16px; font-size: 13px; color: #fca5a5; line-height: 1.5;">
                                        <strong style="color: #f87171; display: block; margin-bottom: 4px;">🔥 Advertencia de Autodestrucción (Un Solo Uso):</strong>
                                        Una vez ingresado el código de apertura, dispondrás de <strong>{{ $nota->obtenerDuracionVisualizacionSegundos() }} segundos</strong> para leer la nota antes de que el contenido sea permanentemente eliminado de la base de datos.
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Pie de Página -->
                    <tr>
                        <td align="center" style="padding: 24px 32px; background-color: #090d16; border-top: 1px solid #1e293b; font-size: 12px; color: #64748b;">
                            <p style="margin: 0 0 4px 0;">Este mensaje fue generado automáticamente por <strong>BITMAN Vault</strong>.</p>
                            <p style="margin: 0; font-size: 11px;">Si no esperabas esta nota, puedes ignorar este correo de forma segura.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

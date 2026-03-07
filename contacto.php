<?php
// Archivo: contacto.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Configuración del correo DESTINO (donde recibirás los mensajes)
    $destinatario = "lauramontelongo1103@gmail.com"; // ¡CAMBIAR POR TU CORREO REAL!
    
    // Obtener datos del formulario
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $asunto_tipo = htmlspecialchars(trim($_POST['asunto']));
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));
    
    // Validar campos obligatorios
    if (empty($nombre) || empty($email) || empty($asunto_tipo) || empty($mensaje)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit;
    }
    
    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Por favor ingresa un correo electrónico válido."]);
        exit;
    }
    
    // Mapear valores de asunto a texto legible
    $asuntos = [
        'colaboracion' => 'Colaboración Académica',
        'proyecto' => 'Propuesta de Proyecto',
        'consulta' => 'Consulta Técnica',
        'feedback' => 'Feedback sobre Portafolio',
        'otro' => 'Otro'
    ];
    
    $asunto_texto = isset($asuntos[$asunto_tipo]) ? $asuntos[$asunto_tipo] : 'Consulta';
    
    // Preparar el contenido del correo
    $asunto_correo = "Contacto Portafolio: $asunto_texto - $nombre";
    
    $contenido = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #ff4f9a, #ff8fc4); color: white; padding: 20px; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #ff4f9a; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Nuevo mensaje desde el Portafolio</h2>
                <p>Fecha: " . date('d/m/Y H:i:s') . "</p>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Nombre:</span> $nombre
                </div>
                <div class='field'>
                    <span class='label'>Correo electrónico:</span> $email
                </div>
                <div class='field'>
                    <span class='label'>Tipo de consulta:</span> $asunto_texto
                </div>
                <div class='field'>
                    <span class='label'>Mensaje:</span><br>
                    <p>" . nl2br($mensaje) . "</p>
                </div>
            </div>
            <div class='footer'>
                <p>Este mensaje fue enviado desde el formulario de contacto del portafolio de Laura Ivón.</p>
                <p>© " . date('Y') . " Laura Ivón | LivTech - Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Cabeceras para correo HTML
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Portafolio Laura Ivón <no-reply@tudominio.com>\r\n";
    $headers .= "Reply-To: $nombre <$email>\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Intentar enviar el correo
    if (mail($destinatario, $asunto_correo, $contenido, $headers)) {
        // Opcional: Enviar copia de confirmación al remitente
        $asunto_confirmacion = "Confirmación: Hemos recibido tu mensaje";
        
        $contenido_confirmacion = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #4CAF50, #8BC34A); color: white; padding: 20px; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>¡Mensaje Recibido!</h2>
                </div>
                <div class='content'>
                    <p>Hola <strong>$nombre</strong>,</p>
                    <p>Hemos recibido tu mensaje sobre <strong>$asunto_texto</strong> y te responderemos en un plazo de 24-48 horas.</p>
                    <p><strong>Resumen de tu mensaje:</strong></p>
                    <blockquote style='background: #f0f0f0; padding: 10px; border-left: 4px solid #4CAF50; margin: 10px 0;'>
                        " . nl2br(substr($mensaje, 0, 200)) . (strlen($mensaje) > 200 ? "..." : "") . "
                    </blockquote>
                    <p>Si tienes alguna urgencia, puedes contactarnos directamente a través de los medios indicados en el portafolio.</p>
                    <p>¡Gracias por contactarnos!</p>
                </div>
                <div class='footer'>
                    <p>Este es un correo automático de confirmación. Por favor no respondas a este mensaje.</p>
                    <p>© " . date('Y') . " Laura Ivón | LivTech - Portafolio de Seguridad Informática</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers_confirmacion = "MIME-Version: 1.0\r\n";
        $headers_confirmacion .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers_confirmacion .= "From: Laura Ivón | LivTech <no-reply@tudominio.com>\r\n";
        
        // Enviar confirmación (puedes comentar esta línea si no quieres enviar confirmación)
        mail($email, $asunto_confirmacion, $contenido_confirmacion, $headers_confirmacion);
        
        echo json_encode(["success" => true, "message" => "Mensaje enviado exitosamente. Te responderemos en breve."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al enviar el mensaje. Por favor, intenta de nuevo más tarde."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>
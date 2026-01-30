<?php
// modules/admin/actions/update_profile_photo.php
session_start();
require_once '../../../config/database.php';
require_once '../../../config/api_helper.php';
require_once '../../../includes/supabase_storage.php';

// Verificación de autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1) {
    ApiResponse::error('No autorizado', 403);
}

// Debug log setup
$logFile = __DIR__ . '/debug_register.log';
if (!function_exists('logDebug')) {
    function logDebug($msg)
    {
        global $logFile;
        @file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, FILE_APPEND);
    }
}

// Iniciar buffer para evitar salidas accidentales
ob_start();

try {
    $user_id = $_SESSION['user_id'];

    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se ha subido ninguna imagen válida.');
    }

    $file = $_FILES['photo'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $max_size = 2 * 1024 * 1024; // 2MB

    // Validaciones
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Tipo de archivo no permitido. Solo JPG, PNG y WEBP.');
    }

    if ($file['size'] > $max_size) {
        throw new Exception('La imagen es demasiado pesada. Máximo 2MB.');
    }

    // Preparar Supabase Storage
    $storage = new SupabaseStorage();
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $path = "profiles/profile_{$user_id}.{$extension}";

    logDebug("ADMIN PROFILE UPLOAD: Iniciando para usuario $user_id");

    // Subir a Supabase
    $publicUrl = $storage->upload($path, $file['tmp_name'], $file['type']);

    if ($publicUrl) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE usuarios SET url_foto = ? WHERE id = ?");
        $stmt->execute([$publicUrl, $user_id]);

        logDebug("ADMIN PROFILE UPLOAD: Exito URL=$publicUrl");

        ob_end_clean();
        ApiResponse::success(['url' => $publicUrl], 'Foto de perfil actualizada con éxito.');
    } else {
        throw new Exception('Error al subir el archivo a Supabase Storage.');
    }

} catch (Throwable $t) {
    logDebug("ADMIN PROFILE ERROR: " . $t->getMessage());
    if (ob_get_length())
        ob_end_clean();
    ApiResponse::error('Error en la carga: ' . $t->getMessage());
}

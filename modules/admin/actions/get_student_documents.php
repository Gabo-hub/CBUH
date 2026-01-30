<?php
// modules/admin/actions/get_student_documents.php
session_start();
require_once '../../../config/database.php';
require_once '../../../config/api_helper.php';

// Verificación de autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1) {
    ApiResponse::error('No autorizado', 403);
}

$student_id = $_GET['student_id'] ?? null;

if (!$student_id) {
    ApiResponse::error('ID de estudiante no proporcionado.');
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT id, tipo_documento, url_archivo, nombre_original, tamano_bytes, verificado, creado_el 
        FROM documentos_estudiantes 
        WHERE estudiante_id = ? AND estado_id = 1
        ORDER BY creado_el DESC
    ");
    $stmt->execute([$student_id]);
    $documents = $stmt->fetchAll();

    ApiResponse::success($documents, 'Documentos recuperados con éxito.');

} catch (PDOException $e) {
    ApiResponse::error('Error de base de datos: ' . $e->getMessage());
}

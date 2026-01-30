<?php
session_start();
require_once '../../../config/database.php';

// Verificación de autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1) {
    echo "No autorizado";
    exit;
}

$tab = $_GET['tab'] ?? '';
$allowed_tabs = ['dashboard', 'directorio', 'inscripciones', 'calificaciones', 'horarios', 'profesores', 'materias', 'reportes'];

if (!in_array($tab, $allowed_tabs)) {
    echo "Pestaña no válida";
    exit;
}

$pdo = getDBConnection();

// Necesitamos las variables de perfil para el dashboard y otros
// Se podría optimizar trayendo solo lo necesario, pero para mantener compatibilidad:
$stmt = $pdo->prepare("
    SELECT 
        u.usuario, u.correo, u.rol_id, u.url_foto,
        pa.nombres, pa.apellidos, pa.cedula, pa.cargo, pa.telefono, 
        pa.fecha_nacimiento, pa.lugar_nacimiento, pa.direccion
    FROM usuarios u
    LEFT JOIN personal_administrativo pa ON u.id = pa.usuario_id
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$adminNombre = htmlspecialchars(($user['nombres'] && $user['apellidos']) ? $user['nombres'] . ' ' . $user['apellidos'] : ($user['usuario'] ?? 'Administrador'), ENT_QUOTES, 'UTF-8');
$adminCedula = htmlspecialchars($user['cedula'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$adminCorreo = htmlspecialchars($user['correo'] ?? 'admin@cbuh.edu', ENT_QUOTES, 'UTF-8');
$adminCargo = htmlspecialchars($user['cargo'] ?? 'ADMINISTRADOR', ENT_QUOTES, 'UTF-8');
$adminTelefono = htmlspecialchars($user['telefono'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$adminFoto = htmlspecialchars($user['url_foto'] ?? '', ENT_QUOTES, 'UTF-8');
$adminLugar = htmlspecialchars($user['lugar_nacimiento'] ?? 'No registrado', ENT_QUOTES, 'UTF-8');
$adminDireccion = htmlspecialchars($user['direccion'] ?? 'No registrada', ENT_QUOTES, 'UTF-8');

$adminEdad = 'N/A';
$adminFechaNac = 'No registrada';
if (!empty($user['fecha_nacimiento'])) {
    $dob = new DateTime($user['fecha_nacimiento']);
    $now = new DateTime();
    $adminEdad = $now->diff($dob)->y . ' Años';
    $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $adminFechaNac = $dob->format('d') . ' de ' . $meses[(int) $dob->format('m')] . ', <br />' . $dob->format('Y');
}

// Cargar la vista
include "../views/{$tab}.php";

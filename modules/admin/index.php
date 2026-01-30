<?php
session_start();
require_once '../../config/database.php';

// Verificación de autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1) {
    header('Location: ../auth/login.php');
    exit;
}

$pdo = getDBConnection();

// Fetch Admin Info
// Obtener información del administrador (combinando usuario de autenticación con perfil administrativo)
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

// Seguridad: Escapar salida para prevenir XSS
// Usar información detallada del perfil si está disponible, de lo contrario usar información de autenticación
$adminNombre = htmlspecialchars(($user['nombres'] && $user['apellidos']) ? $user['nombres'] . ' ' . $user['apellidos'] : ($user['usuario'] ?? 'Administrador'), ENT_QUOTES, 'UTF-8');
$adminCedula = htmlspecialchars($user['cedula'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$adminCorreo = htmlspecialchars($user['correo'] ?? 'admin@cbuh.edu', ENT_QUOTES, 'UTF-8');
$adminCargo = htmlspecialchars($user['cargo'] ?? 'ADMINISTRADOR', ENT_QUOTES, 'UTF-8');
$adminTelefono = htmlspecialchars($user['telefono'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$adminFoto = htmlspecialchars($user['url_foto'] ?? '', ENT_QUOTES, 'UTF-8');

// Formatear fechas y dirección
$adminLugar = htmlspecialchars($user['lugar_nacimiento'] ?? 'No registrado', ENT_QUOTES, 'UTF-8');
$adminDireccion = htmlspecialchars($user['direccion'] ?? 'No registrada', ENT_QUOTES, 'UTF-8');

// Calcular edad y formatear fecha de nacimiento
$adminEdad = 'N/A';
$adminFechaNac = 'No registrada';
if (!empty($user['fecha_nacimiento'])) {
    $dob = new DateTime($user['fecha_nacimiento']);
    $now = new DateTime();
    $adminEdad = $now->diff($dob)->y . ' Años';

    // Formato: 10 de Enero, 1969
    $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $adminFechaNac = $dob->format('d') . ' de ' . $meses[(int) $dob->format('m')] . ', <br />' . $dob->format('Y');
}



$tab = $_GET['tab'] ?? 'dashboard';

$estudiantes = [];
// Optimización: Solo cargar lista de estudiantes si estamos en el directorio
if ($tab === 'directorio') {
    try {
        $stmtStd = $pdo->query("SELECT * FROM estudiantes ORDER BY creado_el DESC LIMIT 50");
        $estudiantes = $stmtStd->fetchAll();
    } catch (PDOException $e) {
        $estudiantes = [];
    }
}

$page_title = "CBUH - Control de Estudio";
$is_root = false;
$path_depth = 2;

$extra_head = '
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
<link rel="stylesheet" href="../../assets/css/admin_custom.css">
<script src="../../assets/js/admin_config.js"></script>
';

include '../../includes/head.php';
?>

<!-- Envoltorio del diseño -->
<!-- Sobrescribimos las clases flex-col y otras de head.php si es necesario forzando clases en este envoltorio o vía script -->
<script>
    document.body.className = "bg-background-dark text-slate-100 min-h-screen overflow-hidden flex";
</script>

<!-- Barra lateral -->
<?php include 'views/layouts/sidebar.php'; ?>

<!-- Area de Contenido Principal -->
<main class="flex-1 flex flex-col relative w-0 overflow-hidden bg-background-dark h-screen">

    <!-- Encabezado Global -->
    <header
        class="h-16 flex items-center justify-between px-8 bg-primary-dark border-b border-white/10 sticky top-0 z-10 shrink-0">
        <div class="flex items-center gap-4">
            <h2 id="header-title" class="text-xl font-bold text-white uppercase tracking-tight">Dashboard</h2>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4 border-r border-white/10 pr-6">
                <button class="relative p-2 text-gold/80 hover:bg-white/5 rounded-full">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full"></span>
                </button>
                <div class="text-right">
                    <p class="text-xs text-white/40 uppercase tracking-widest text-[10px]">Administrador</p>
                    <p class="text-sm font-bold text-gold">
                        <?php echo isset($adminNombre) ? $adminNombre : 'Administrador'; ?>
                    </p>
                </div>
            </div>
            <nav class="hidden xl:flex items-center gap-6 text-xs font-bold uppercase tracking-widest text-white/60">
                <a class="hover:text-gold transition-colors" href="#">Inicio</a>
                <a class="hover:text-gold transition-colors" href="#">Ayuda</a>
            </nav>
        </div>
    </header>

    <div id="tab-dashboard" class="tab-content h-full flex flex-col overflow-y-auto custom-scrollbar">
        <?php include 'views/dashboard.php'; ?>
    </div>

    <div id="tab-directorio" class="tab-content h-full flex flex-col hidden overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-center h-full">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold"></div>
        </div>
    </div>

    <div id="tab-inscripciones" class="tab-content h-full flex flex-col hidden overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-center h-full">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold"></div>
        </div>
    </div>

    <div id="tab-calificaciones" class="tab-content h-full flex flex-col hidden overflow-hidden">
        <div class="flex items-center justify-center h-full">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold"></div>
        </div>
    </div>

    <div id="tab-horarios" class="tab-content h-full flex flex-col hidden overflow-hidden">
        <div class="flex items-center justify-center h-full">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold"></div>
        </div>
    </div>

    <div id="tab-profesores" class="tab-content h-full flex flex-col hidden overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-center h-full">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold"></div>
        </div>
    </div>

    <div id="tab-materias" class="tab-content h-full flex flex-col hidden overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-center h-full">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold"></div>
        </div>
    </div>

    <div id="tab-reportes" class="tab-content h-full flex flex-col hidden overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-center h-full">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold"></div>
        </div>
    </div>

</main>

<script>
    const loadedTabs = { 'dashboard': true };

    async function switchTab(tabId) {
        // Ocultar todas las pestañas
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });

        // Mostrar pestaña destino
        const target = document.getElementById('tab-' + tabId);
        if (target) target.classList.remove('hidden');

        // Cargar contenido si no está cargado
        if (!loadedTabs[tabId]) {
            try {
                const response = await fetch(`actions/load_view.php?tab=${tabId}`);
                const html = await response.text();
                target.innerHTML = html;
                loadedTabs[tabId] = true;

                // Re-ejecutar scripts si la pestaña los tiene
                const scripts = target.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
            } catch (error) {
                target.innerHTML = '<div class="p-8 text-red-400">Error al cargar la sección</div>';
            }
        }

        // Actualizar estilos de navegación
        document.querySelectorAll('.section-link').forEach(btn => {
            btn.classList.remove('bg-gold', 'text-primary-dark', 'font-bold');
            btn.classList.add('text-white/60', 'font-medium', 'hover:bg-white/5', 'hover:text-white');
            if (btn.id === 'nav-' + tabId) {
                btn.classList.add('bg-gold', 'text-primary-dark', 'font-bold');
                btn.classList.remove('text-white/60', 'font-medium', 'hover:bg-white/5', 'hover:text-white');
            }
        });

        const titles = {
            'dashboard': 'Dashboard',
            'directorio': 'Directorio <span class="text-gold">Estudiantil</span>',
            'inscripciones': 'Ficha de <span class="text-gold">Inscripción</span>',
            'calificaciones': 'Control de <span class="text-gold">Calificaciones</span>',
            'horarios': 'Gestión de <span class="text-gold">Horarios</span>',
            'profesores': 'Vista <span class="text-gold">Profesor</span>',
            'materias': 'Gestión de <span class="text-gold">Materias</span>',
            'reportes': 'Reportes del <span class="text-gold">Sistema</span>'
        };
        const titleEl = document.getElementById('header-title');
        if (titleEl && titles[tabId]) titleEl.innerHTML = titles[tabId];

        const newUrl = new URL(window.location);
        newUrl.searchParams.set('tab', tabId);
        window.history.pushState({}, '', newUrl);
    }

    // Inicializar desde URL o defecto
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const initialTab = urlParams.get('tab') || 'dashboard';
        switchTab(initialTab);
    });
</script>

<!-- Scripts Globales -->
<script src="../../assets/js/notifications.js"></script>
</body>

</html>
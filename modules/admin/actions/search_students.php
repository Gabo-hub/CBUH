<?php
// modules/admin/actions/search_students.php
require_once '../../../config/database.php';

$pdo = getDBConnection();

// 1. Manejo de Filtros y Búsqueda
$where = "1=1";
$params = [];

// Búsqueda por Texto (Nombre, Cédula)
if (!empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $where .= " AND (e.nombres ILIKE :search OR e.apellidos ILIKE :search OR e.cedula ILIKE :search)";
    $params['search'] = $search;
}

// Filtro por Año
if (!empty($_GET['year']) && $_GET['year'] != 'all') {
    $where .= " AND e.año_actual = :year";
    $params['year'] = $_GET['year'];
}

// Filtro por Estado
if (!empty($_GET['status']) && $_GET['status'] != 'all') {
    $where .= " AND e.estado_id = :status";
    $params['status'] = $_GET['status'];
}

// 2. Consulta SQL
$sql = "SELECT 
            e.id, 
            e.nombres, 
            e.apellidos, 
            e.cedula, 
            e.año_actual, 
            e.estado_id,
            e.telefono,
            e.direccion,
            e.lugar_nacimiento,
            e.fecha_nacimiento,
            er.nombre as estado_nombre,
            u.correo, 
            COALESCE(u.url_foto, (SELECT url_archivo FROM documentos_estudiantes WHERE estudiante_id = e.id AND tipo_documento = 'foto_perfil' ORDER BY creado_el DESC LIMIT 1)) as url_foto
        FROM estudiantes e
        LEFT JOIN usuarios u ON e.usuario_id = u.id
        LEFT JOIN estados_registro er ON e.estado_id = er.id
        WHERE $where
        ORDER BY e.apellidos ASC, e.nombres ASC
        LIMIT 50";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$estudiantes = $stmt->fetchAll();

if (!empty($estudiantes)):
    foreach($estudiantes as $e):
        $jsonData = htmlspecialchars(json_encode($e), ENT_QUOTES, 'UTF-8');
?>
    <tr class="hover:bg-white/5 transition-colors group">
        <td class="px-8 py-4">
            <div class="flex items-center gap-4">
                <div class="size-10 rounded-full bg-card-dark border border-gold/30 flex items-center justify-center text-xs font-bold text-gold shadow-lg overflow-hidden shrink-0">
                    <?php if(!empty($e['url_foto'])): ?>
                        <img src="<?php echo htmlspecialchars($e['url_foto']); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?php echo substr($e['nombres'],0,1).substr($e['apellidos'],0,1); ?>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-sm font-bold text-white group-hover:text-gold transition-colors">
                        <?php echo htmlspecialchars($e['nombres'] . ' ' . $e['apellidos']); ?>
                    </p>
                    <p class="text-[10px] text-white/40 font-medium"><?php echo htmlspecialchars($e['correo'] ?? 'Sin correo'); ?></p>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 text-center">
            <span class="text-xs font-mono font-bold text-white/80"><?php echo htmlspecialchars($e['cedula']); ?></span>
        </td>
        <td class="px-6 py-4 text-center">
            <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-bold text-white/60">
                <?php echo $e['año_actual']; ?>º Año
            </span>
        </td>
        <td class="px-6 py-4 text-center">
            <?php 
                $statusClass = ($e['estado_id'] == 1) ? 'text-green-400 bg-green-500/10 border-green-500/20' : 'text-red-400 bg-red-500/10 border-red-500/20';
                $statusDot = ($e['estado_id'] == 1) ? 'bg-green-400' : 'bg-red-400';
            ?>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border <?php echo $statusClass; ?>">
                <span class="size-1.5 rounded-full <?php echo $statusDot; ?>"></span>
                <?php echo htmlspecialchars($e['estado_nombre'] ?? 'Desconocido'); ?>
            </span>
        </td>
        <td class="px-8 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
                <button onclick="openViewModal(this)" data-student="<?php echo $jsonData; ?>" 
                        class="p-2 text-white/40 hover:text-gold hover:bg-gold/10 rounded-lg transition-all" title="Ver Expediente">
                    <span class="material-symbols-outlined text-lg">visibility</span>
                </button>
                <button onclick="openEditModal(this)" data-student="<?php echo $jsonData; ?>"
                        class="p-2 text-white/40 hover:text-gold hover:bg-gold/10 rounded-lg transition-all" title="Editar">
                    <span class="material-symbols-outlined text-lg">edit</span>
                </button>
            </div>
        </td>
    </tr>
<?php 
    endforeach;
else: 
?>
    <tr><td colspan="5" class="p-8 text-center text-white/40 uppercase tracking-widest text-xs font-bold">No se encontraron estudiantes.</td></tr>
<?php endif; ?>

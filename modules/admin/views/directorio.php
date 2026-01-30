<?php
// Validar acceso DB (asumimos que $pdo ya está disponible por el include en index.php)
if (!isset($pdo)) {
    // Fallback por si se carga directo (no debería ocurrir en este esquema)
    require_once '../../config/database.php';
    $pdo = getDBConnection();
}

// 1. Manejo de Filtros y Búsqueda
$where = "1=1";
$params = [];

// Búsqueda por Texto (Nombre, Cédula)
if (!empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $where .= " AND (e.nombres ILIKE :search OR e.apellidos ILIKE :search OR e.cedula ILIKE :search)";
    $params['search'] = $search;
}

// Filtro por Año (antes Semestre)
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
            e.telefono,
            e.direccion,
            e.lugar_nacimiento,
            e.fecha_nacimiento,
            e.estado_id,
            er.nombre as estado_nombre,
            u.correo, 
            u.usuario as login_usuario,
            COALESCE(u.url_foto, (SELECT url_archivo FROM documentos_estudiantes WHERE estudiante_id = e.id AND tipo_documento = 'foto_perfil' ORDER BY creado_el DESC LIMIT 1)) as url_foto
        FROM estudiantes e
        LEFT JOIN usuarios u ON e.usuario_id = u.id
        LEFT JOIN estados_registro er ON e.estado_id = er.id
        WHERE $where
        ORDER BY e.apellidos ASC, e.nombres ASC
        LIMIT 50"; // Paginación básica

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$estudiantes = $stmt->fetchAll();
?>

<!-- Estilos globales manejados en index.php -->
<div class="p-8 space-y-6">
    <!-- Búsqueda y Filtros -->
    <section class="bg-primary-dark rounded-2xl border border-white/10 p-6">
        <!-- Contenedor div/form sin acción, manejado por JS -->
        <div id="directoryFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Campo oculto para mantener la lógica de pestaña activa si es necesario -->

            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-1.5 ml-1">Buscar
                    Estudiante</label>
                <div class="relative">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gold/60 text-lg">search</span>
                    <input id="searchInput" name="search"
                        class="w-full bg-black/20 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:ring-1 focus:ring-gold/50 outline-none transition-all placeholder:text-white/20"
                        placeholder="Nombre o Cédula..." type="text" />
                </div>
            </div>

            <!-- Filtro de Año Académico -->

            <div>
                <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-1.5 ml-1">Año
                    Académico</label>
                <select id="yearSelect" name="year"
                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2 text-sm text-white focus:ring-1 focus:ring-gold/50 outline-none appearance-none cursor-pointer">
                    <option value="all">Todos los Años</option>
                    <option value="1">1er Año</option>
                    <option value="2">2do Año</option>
                    <option value="3">3er Año</option>
                    <option value="4">4to Año</option>
                </select>
            </div>
            <div>
                <label
                    class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-1.5 ml-1">Estado</label>
                <select id="statusSelect" name="status"
                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2 text-sm text-white focus:ring-1 focus:ring-gold/50 outline-none appearance-none cursor-pointer">
                    <option value="all">Todos</option>
                    <option value="1">Activo</option>
                    <option value="2">Suspendido</option>
                </select>
            </div>
        </div>
    </section>

    <!-- Table -->
    <section class="bg-primary-dark rounded-2xl border border-white/10 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead
                    class="bg-black/40 text-white/50 text-[10px] uppercase tracking-[0.2em] border-b border-white/10">
                    <tr>
                        <th class="px-8 py-5 font-black">Estudiante</th>
                        <th class="px-6 py-5 font-black text-center">Cédula</th>
                        <!-- Columna Carrera Eliminada -->
                        <th class="px-6 py-5 font-black text-center">Año</th>
                        <th class="px-6 py-5 font-black text-center">Estado</th>
                        <th class="px-8 py-5 font-black text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody" class="divide-y divide-white/5">
                    <?php if (!empty($estudiantes)): ?>
                        <?php foreach ($estudiantes as $e):
                            // Codificar datos para JS
                            $jsonData = htmlspecialchars(json_encode($e), ENT_QUOTES, 'UTF-8');
                            ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="size-10 rounded-full bg-card-dark border border-gold/30 flex items-center justify-center text-xs font-bold text-gold shadow-lg overflow-hidden shrink-0">
                                            <?php if (!empty($e['url_foto'])): ?>
                                                <img src="<?php echo htmlspecialchars($e['url_foto']); ?>"
                                                    class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <?php echo substr($e['nombres'], 0, 1) . substr($e['apellidos'], 0, 1); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-white group-hover:text-gold transition-colors">
                                                <?php echo htmlspecialchars($e['nombres'] . ' ' . $e['apellidos']); ?>
                                            </p>
                                            <p class="text-[10px] text-white/40 font-medium">
                                                <?php echo htmlspecialchars($e['correo'] ?? 'Sin correo'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-xs font-mono font-bold text-white/80"><?php echo htmlspecialchars($e['cedula']); ?></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-bold text-white/60">
                                        <?php echo $e['año_actual']; ?>º Año
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                    $statusClass = ($e['estado_id'] == 1) ? 'text-green-400 bg-green-500/10 border-green-500/20' : 'text-red-400 bg-red-500/10 border-red-500/20';
                                    $statusDot = ($e['estado_id'] == 1) ? 'bg-green-400' : 'bg-red-400';
                                    ?>
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border <?php echo $statusClass; ?>">
                                        <span class="size-1.5 rounded-full <?php echo $statusDot; ?>"></span>
                                        <?php echo htmlspecialchars($e['estado_nombre'] ?? 'Desconocido'); ?>
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="openViewModal(this)" data-student="<?php echo $jsonData; ?>"
                                            class="p-2 text-white/40 hover:text-gold hover:bg-gold/10 rounded-lg transition-all"
                                            title="Ver Expediente">
                                            <span class="material-symbols-outlined text-lg">visibility</span>
                                        </button>
                                        <button onclick="openEditModal(this)" data-student="<?php echo $jsonData; ?>"
                                            class="p-2 text-white/40 hover:text-gold hover:bg-gold/10 rounded-lg transition-all"
                                            title="Editar">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5"
                                class="p-8 text-center text-white/40 uppercase tracking-widest text-xs font-bold">No se
                                encontraron estudiantes.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Paginación (Estática por ahora) -->
        <div class="p-6 bg-black/40 border-t border-white/10 flex items-center justify-between">
            <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em]">Mostrando registros</p>
            <div class="flex items-center gap-2">
                <button disabled
                    class="size-9 flex items-center justify-center rounded-lg bg-card-dark border border-white/10 text-white/40 opacity-50 cursor-not-allowed">
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                </button>
                <button
                    class="size-9 flex items-center justify-center rounded-lg bg-gold text-primary-dark font-black text-xs">1</button>
                <button disabled
                    class="size-9 flex items-center justify-center rounded-lg bg-card-dark border border-white/10 text-white/40 opacity-50 cursor-not-allowed">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                </button>
            </div>
        </div>
    </section>
</div>

<!-- Botón Agregar -->
<div class="fixed bottom-8 right-8">
    <button onclick="switchTab('inscripciones')"
        class="bg-gold hover:bg-white text-primary-dark size-14 rounded-full shadow-2xl flex items-center justify-center transition-all group active:scale-95">
        <span
            class="material-symbols-outlined font-black text-2xl group-hover:rotate-90 transition-transform">add</span>
    </button>
</div>

<!-- ================= MODALS ================= -->
<?php include __DIR__ . '/partials/student_modals.php'; ?>

<script>
    async function openViewModal(btn) {
        const data = JSON.parse(btn.dataset.student);

        // Llenar campos básicos
        document.getElementById('view_fullname').textContent = data.nombres + ' ' + data.apellidos;
        document.getElementById('view_cedula').textContent = data.cedula;
        document.getElementById('view_email').textContent = data.correo || 'No registrado';
        document.getElementById('view_phone').textContent = data.telefono || 'No registrado';
        document.getElementById('view_address').textContent = data.direccion || 'No registrada';
        document.getElementById('view_birth_date').textContent = data.fecha_nacimiento || '--';
        document.getElementById('view_birth_place').textContent = data.lugar_nacimiento || '--';

        // Badge de Estado
        const badge = document.getElementById('view_status_badge');
        badge.textContent = data.estado_nombre || 'Desconocido';
        badge.className = data.estado_id == 1
            ? 'inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase bg-green-500/10 text-green-400'
            : 'inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase bg-red-500/10 text-red-400';

        // Avatar
        const avatarImg = document.getElementById('view_avatar_preview');
        const initials = document.getElementById('view_avatar_initials');
        if (data.url_foto) {
            avatarImg.src = data.url_foto;
            avatarImg.classList.remove('hidden');
            initials.classList.add('hidden');
        } else {
            avatarImg.classList.add('hidden');
            initials.classList.remove('hidden');
            initials.textContent = data.nombres[0] + data.apellidos[0];
        }

        document.getElementById('viewStudentModal').classList.remove('hidden');

        // Cargar Documentos dinámicamente
        const docContainer = document.getElementById('view_docs_container');
        docContainer.innerHTML = '<div class="animate-pulse text-white/20 text-[9px] font-black uppercase text-center mt-8">Consultando Supabase...</div>';

        try {
            const response = await fetch(`actions/get_student_documents.php?student_id=${data.id}`);
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                docContainer.innerHTML = '';
                result.data.forEach(doc => {
                    if (doc.tipo_documento === 'foto_perfil') return; // Saltar foto de perfil en la lista de documentos

                    const row = document.createElement('a');
                    row.href = doc.url_archivo;
                    row.target = '_blank';
                    row.className = "flex items-center justify-between p-3 bg-white/5 hover:bg-gold/10 border border-white/5 rounded-xl transition-all group mb-2";

                    const docName = doc.tipo_documento.replace('_', ' ').toUpperCase();

                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gold text-lg">description</span>
                            <div>
                                <p class="text-[9px] font-black text-white/90 group-hover:text-gold transition-colors">${docName}</p>
                                <p class="text-[8px] text-white/40">Ver Archivo Adjunto</p>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-white/20 group-hover:text-gold text-sm transition-all">open_in_new</span>
                    `;
                    docContainer.appendChild(row);
                });
            } else {
                docContainer.innerHTML = '<div class="text-white/20 text-center py-10 text-[9px] font-black uppercase tracking-widest border border-dashed border-white/10 rounded-xl">Sin documentos cargados</div>';
            }
        } catch (e) {
            docContainer.innerHTML = '<div class="text-red-400/50 text-center py-10 text-[9px] font-black uppercase tracking-widest">Error al conectar con el servidor</div>';
        }
    }

    function openEditModal(btn) {
        const data = JSON.parse(btn.dataset.student);

        // Llenar Formulario
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nombres').value = data.nombres;
        document.getElementById('edit_apellidos').value = data.apellidos;
        document.getElementById('edit_cedula').value = data.cedula;
        document.getElementById('edit_telefono').value = data.telefono || '';
        document.getElementById('edit_direccion').value = data.direccion || '';
        document.getElementById('edit_lugar_nacimiento').value = data.lugar_nacimiento || '';
        document.getElementById('edit_fecha_nacimiento').value = data.fecha_nacimiento || '';
        document.getElementById('edit_correo').value = data.correo || '';
        document.getElementById('edit_usuario_login').value = data.login_usuario || '';
        document.getElementById('edit_anio').value = data.año_actual;
        document.getElementById('edit_estado').value = data.estado_id;

        // Header Info
        document.getElementById('edit_header_name').textContent = data.nombres + ' ' + data.apellidos;
        document.getElementById('edit_header_cedula').textContent = 'Cédula: ' + data.cedula;

        // Avatar Preview
        const preview = document.getElementById('edit_avatar_preview');
        const initials = document.getElementById('edit_avatar_initials');
        if (data.url_foto) {
            preview.src = data.url_foto;
            preview.classList.remove('hidden');
            initials.classList.add('hidden');
        } else {
            preview.classList.add('hidden');
            initials.classList.remove('hidden');
            initials.textContent = data.nombres[0] + data.apellidos[0];
        }

        document.getElementById('editStudentModal').classList.remove('hidden');
    }

    // Previsualización de Foto en Modal de Edición
    document.getElementById('edit_photo_input').addEventListener('change', function (e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = (ex) => {
                const preview = document.getElementById('edit_avatar_preview');
                const initials = document.getElementById('edit_avatar_initials');
                preview.src = ex.target.result;
                preview.classList.remove('hidden');
                initials.classList.add('hidden');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    // Manejo de Formulario AJAX
    document.querySelector('#editStudentModal form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        // Estado de Carga
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            let result;
            try {
                result = await response.json();
            } catch (err) {
                throw new Error('Respuesta del servidor inválida');
            }

            if (result.status === 'success') {
                NotificationSystem.show(result.message, 'success');
                closeModal('editStudentModal');

                // Actualizar interfaz de fila dinámicamente
                // Buscamos la fila basándonos en el botón de edición que contiene el ID
                const studentId = formData.get('student_id');

                const buttons = document.querySelectorAll('button[onclick="openEditModal(this)"]');
                for (let btn of buttons) {
                    let data = JSON.parse(btn.dataset.student);
                    if (data.id == studentId) {
                        // Update the dataset with new values
                        const newData = { ...data, ...result.data };
                        // Map Status Id to text?
                        const statusMap = { '1': 'Activo', '2': 'Suspendido', '3': 'Retirado' };
                        // We need 'estado_nombre' in jsonData if we want to display it correctly
                        if (result.data.estado_id) newData.estado_name = statusMap[result.data.estado_id]; // Fallback mapping

                        btn.dataset.student = JSON.stringify(newData);
                        btn.previousElementSibling.dataset.student = JSON.stringify(newData);

                        // Actualizar UI - Nombres
                        const row = btn.closest('tr');
                        row.querySelector('.text-sm.font-bold').textContent = newData.nombres + ' ' + newData.apellidos;

                        // Actualizar UI - Cédula
                        // It's the second TD
                        row.querySelectorAll('td')[1].querySelector('span').textContent = newData.cedula;

                        // Actualizar UI - Año
                        row.querySelectorAll('td')[2].querySelector('span').innerHTML = newData.año_actual + 'º Año';

                        // Actualizar UI - Estado
                        const statusSpan = row.querySelectorAll('td')[3].querySelector('span');
                        const statusName = statusMap[newData.estado_id] || 'Desconocido';
                        // Reset classes
                        statusSpan.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border';
                        let colorClass = (newData.estado_id == 1) ? 'text-green-400 bg-green-500/10 border-green-500/20' : 'text-red-400 bg-red-500/10 border-red-500/20';
                        let dotClass = (newData.estado_id == 1) ? 'bg-green-400' : 'bg-red-400';
                        statusSpan.classList.add(...colorClass.split(' '));
                        statusSpan.innerHTML = `<span class="size-1.5 rounded-full ${dotClass}"></span>${statusName}`;

                        break;
                    }
                }

            } else {
                NotificationSystem.show(result.message || 'Error al actualizar', 'error');
            }

        } catch (error) {
            console.error(error);
            NotificationSystem.show('Ocurrió un error inesperado', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
    // Lógica de Búsqueda
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const yearSelect = document.getElementById('yearSelect');
    const statusSelect = document.getElementById('statusSelect');
    const tableBody = document.getElementById('studentsTableBody');

    function performSearch() {
        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(async () => {
            const query = searchInput.value;
            const year = yearSelect.value;
            const status = statusSelect.value;

            // Disable inputs while searching? Optional.
            // tableBody.style.opacity = '0.5';

            try {
                const params = new URLSearchParams({
                    search: query,
                    year: year,
                    status: status
                });

                const response = await fetch(`actions/search_students.php?${params.toString()}`);
                if (!response.ok) throw new Error('Error en búsqueda');

                const html = await response.text();
                tableBody.innerHTML = html;

            } catch (error) {
                console.error(error);
                NotificationSystem.show('Error al buscar estudiantes', 'error');
            } finally {
                // tableBody.style.opacity = '1';
            }
        }, 300); // Debounce 300ms
    }

    // Asignar Eventos
    if (searchInput) searchInput.addEventListener('input', performSearch);
    if (yearSelect) yearSelect.addEventListener('change', performSearch);
    if (statusSelect) statusSelect.addEventListener('change', performSearch);

    // Inicializa la búsqueda si hay parámetros en la URL
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        if (searchParam && searchInput) {
            searchInput.value = searchParam;
            performSearch();
        }
    });

    // Escuchar cambios en la URL (para navegación interna por pushState)
    window.addEventListener('popstate', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'directorio') {
            const searchParam = urlParams.get('search');
            if (searchParam && searchInput) {
                searchInput.value = searchParam;
                performSearch();
            }
        }
    });
</script>
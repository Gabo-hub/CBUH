<div class="p-8 space-y-8">
    <!-- Sección de búsqueda (Movida del encabezado) -->
    <?php
    require_once '../../includes/StudentService.php';
    $studentService = new StudentService($pdo);

    // 1-3. Estadísticas Generales
    $stats = $studentService->getDashboardStats();

    $statsTotal = $stats['total'];
    $newStudents = $stats['nuevos'];
    $statsInscritos = $stats['inscritos'];
    $statsGraduando = $stats['graduandos'];

    // Cálculo de crecimiento
    $prevTotal = $statsTotal - $newStudents;
    $growthTotal = ($prevTotal > 0) ? (($newStudents / $prevTotal) * 100) : 0;
    $growthClass = ($growthTotal >= 0) ? 'text-green-400' : 'text-red-400';
    $growthSign = ($growthTotal >= 0) ? '+' : '';

    // 4. Requerimientos Pendientes
    $pendientes = $studentService->getPendingDocuments(10);

    // Función auxiliar para nombres de documentos legibles
    if (!function_exists('formatDocType')) {
        function formatDocType($type)
        {
            $types = [
                'cedula' => 'Copia de Cédula',
                'titulo_bachiller' => 'Título de Bachiller',
                'notas_certificadas' => 'Notas Certificadas',
                'partida_nacimiento' => 'Partida de Nacimiento'
            ];
            return $types[$type] ?? ucwords(str_replace('_', ' ', $type));
        }
    }

    // 5. Actividad Reciente
    $actividades = $studentService->getRecentActivity(5);

    if (!function_exists('time_elapsed_string')) {
        function time_elapsed_string($datetime, $full = false)
        {
            $now = new DateTime;
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);

            // Calcular semanas manualmente
            $weeks = floor($diff->d / 7);
            $days = $diff->d - ($weeks * 7);

            $string = array(
                'y' => 'año',
                'm' => 'mes',
                'w' => 'semana',
                'd' => 'día',
                'h' => 'hora',
                'i' => 'minuto',
                's' => 'segundo',
            );

            $values = [
                'y' => $diff->y,
                'm' => $diff->m,
                'w' => $weeks,
                'd' => $days,
                'h' => $diff->h,
                'i' => $diff->i,
                's' => $diff->s,
            ];

            // Clean up values map logic in standard impl
            $string = array(
                'y' => 'año',
                'm' => 'mes',
                'w' => 'semana',
                'd' => 'día',
                'h' => 'hora',
                'i' => 'minuto',
                's' => 'segundo',
            );
            foreach ($string as $k => &$v) {
                if (isset($values[$k]) && $values[$k]) {
                    $v = $values[$k] . ' ' . $v . ($values[$k] > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            }

            if (!$full)
                $string = array_slice($string, 0, 1);
            return $string ? 'Hace ' . implode(', ', $string) : 'Justo ahora';
        }
    }
    ?>
    <section class="bg-primary-dark rounded-2xl border border-white/10 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-4">
                <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-1.5 ml-1">Búsqueda
                    Global</label>
                <div class="relative">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gold/60 text-lg">search</span>
                    <input id="globalSearchInput"
                        class="w-full bg-black/20 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:ring-1 focus:ring-gold/50 outline-none transition-all placeholder:text-white/20"
                        placeholder="Buscar por nombre, cédula o expediente..." type="text" />
                </div>
            </div>
        </div>
    </section>

    <section class="bg-primary-dark rounded-[2rem] p-10 border border-white/10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gold/5 blur-[100px] rounded-full -mr-20 -mt-20">
        </div>
        <div class="flex flex-col lg:flex-row gap-12 items-center relative z-0">
            <div class="flex flex-col items-center text-center shrink-0">
                <div class="relative mb-6">
                    <div class="size-48 rounded-full border-4 border-gold p-1 relative">
                        <div
                            class="size-full rounded-full bg-slate-800 bg-cover bg-center overflow-hidden flex items-center justify-center border-2 border-white/5 shadow-2xl relative group-hover:border-gold/50 transition-all">
                            <?php if (!empty($adminFoto)): ?>
                                <img id="profileImageDisplay" src="<?php echo $adminFoto; ?>?v=<?php echo time(); ?>"
                                    class="w-full h-full object-cover" alt="Perfil">
                            <?php else: ?>
                                <span id="profileIconDisplay" class="text-6xl">👤</span>
                                <img id="profileImageDisplay" src="" class="w-full h-full object-cover hidden" alt="Perfil">
                            <?php endif; ?>
                        </div>
                        <input type="file" id="profilePhotoInput" accept="image/*" class="hidden">
                        <button onclick="document.getElementById('profilePhotoInput').click()"
                            class="absolute bottom-2 right-2 size-12 bg-gold hover:bg-white text-primary-dark rounded-full flex items-center justify-center shadow-lg transition-all group"
                            title="Cambiar Foto">
                            <span
                                class="material-symbols-outlined font-bold group-hover:scale-110 transition-transform">photo_camera</span>
                        </button>
                    </div>
                </div>
                <h2 class="text-3xl font-extrabold text-white tracking-tight uppercase leading-none">
                    <?php echo isset($adminNombre) ? $adminNombre : 'USUARIO ADMIN'; ?>
                </h2>
                <p class="text-gold text-[10px] font-bold mt-2 uppercase tracking-widest">
                    <?php echo isset($adminCargo) ? $adminCargo : 'ADMINISTRADOR'; ?>
                </p>
            </div>
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 w-full">
                <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-white/40 mb-2">
                        <span class="material-symbols-outlined text-sm">id_card</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Cédula</span>
                    </div>
                    <p class="text-xl font-bold text-white">
                        <?php echo isset($adminCedula) ? $adminCedula : 'N/A'; ?>
                    </p>
                </div>
                <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-white/40 mb-2">
                        <span class="material-symbols-outlined text-sm">cake</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Edad</span>
                    </div>
                    <p class="text-xl font-bold text-white"><?php echo $adminEdad; ?></p>
                </div>
                <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-white/40 mb-2">
                        <span class="material-symbols-outlined text-sm">call</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Teléfono</span>
                    </div>
                    <p class="text-xl font-bold text-white">
                        <?php echo $adminTelefono; ?>
                    </p>
                </div>
                <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-white/40 mb-2">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Lugar de
                            Nacimiento</span>
                    </div>
                    <p class="text-base font-bold text-white leading-tight"><?php echo $adminLugar; ?></p>
                </div>
                <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-white/40 mb-2">
                        <span class="material-symbols-outlined text-sm">event</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Fecha de
                            Nacimiento</span>
                    </div>
                    <p class="text-base font-bold text-white leading-tight"><?php echo $adminFechaNac; ?></p>
                </div>
                <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-white/40 mb-2">
                        <span class="material-symbols-outlined text-sm">mail</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Correo
                            Institucional</span>
                    </div>
                    <p class="text-base font-bold text-white truncate">
                        <?php echo isset($adminCorreo) ? $adminCorreo : 'admin@cbuh.edu'; ?>
                    </p>
                </div>
                <div
                    class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1 md:col-span-2 lg:col-span-3">
                    <div class="flex items-center gap-2 text-white/40 mb-2">
                        <span class="material-symbols-outlined text-sm">home</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Dirección de
                            Habitación</span>
                    </div>
                    <p class="text-sm font-medium text-white/90 leading-relaxed">
                        <?php echo $adminDireccion; ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="text-center py-4">
        <h2
            class="text-5xl font-black italic tracking-tighter text-white uppercase flex items-center justify-center gap-4">
            CONTROL DE <span class="text-gold">ESTUDIO</span>
        </h2>
    </div>

    <!-- Fila de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-primary-dark p-6 rounded-2xl border border-white/10 flex items-center gap-6">
            <div class="size-14 bg-gold/10 rounded-xl flex items-center justify-center text-gold">
                <span class="material-symbols-outlined text-3xl">school</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Estudiantes Totales</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-white"><?php echo number_format($statsTotal); ?></h3>
                    <span
                        class="text-xs font-bold <?php echo $growthClass; ?>"><?php echo $growthSign . number_format($growthTotal, 1); ?>%</span>
                </div>
            </div>
        </div>
        <div class="bg-primary-dark p-6 rounded-2xl border border-white/10 flex items-center gap-6">
            <div class="size-14 bg-gold/10 rounded-xl flex items-center justify-center text-gold">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Inscritos Activos</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-white"><?php echo number_format($statsInscritos); ?></h3>
                    <!-- Percentage removed as per user request/lack of historical data -->
                </div>
            </div>
        </div>
        <div class="bg-primary-dark p-6 rounded-2xl border border-white/10 flex items-center gap-6">
            <div class="size-14 bg-orange-400/10 rounded-xl flex items-center justify-center text-orange-400">
                <span class="material-symbols-outlined text-3xl">pending_actions</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Por Graduarse</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-white"><?php echo number_format($statsGraduando); ?></h3>
                    <!-- Percentage removed as per user request/lack of historical data -->
                </div>
            </div>
        </div>
    </div>

    <!-- Script para búsqueda global -->
    <script>
        // Búsqueda Global
        document.getElementById('globalSearchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                switchTab('directorio');
                // Un pequeño retraso para asegurar que la pestaña se cargue si es lazy
                setTimeout(() => {
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput) {
                        searchInput.value = this.value;
                        searchInput.dispatchEvent(new Event('input'));
                    }
                }, 500);
            }
        });

        // Cambio de Foto de Perfil
        document.getElementById('profilePhotoInput').addEventListener('change', async function () {
            if (this.files && this.files[0]) {
                const formData = new FormData();
                formData.append('photo', this.files[0]);

                try {
                    // Mostrar carga visual
                    NotificationSystem.show('Subiendo imagen...', 'info');

                    const response = await fetch('actions/update_profile_photo.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Actualizar imagen en la página sin recargar
                        const img = document.getElementById('profileImageDisplay');
                        const icon = document.getElementById('profileIconDisplay');

                        img.src = result.data.url + '?t=' + new Date().getTime();
                        img.classList.remove('hidden');
                        if (icon) icon.classList.add('hidden');

                        // También actualizar la imagen en la barra lateral si existe
                        // (En este proyecto la barra lateral no tiene foto pequeña, pero si la tuviera se actualizaría aquí)

                        NotificationSystem.show('¡Foto actualizada!', 'success');
                    } else {
                        NotificationSystem.show(result.message || 'Error al subir la foto', 'error');
                    }
                } catch (error) {
                    NotificationSystem.show('Error de conexión al subir la foto', 'error');
                }
            }
        });
    </script>

    <!-- Acciones y Actividades -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-primary-dark rounded-2xl border border-white/10 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-white/5 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-white uppercase tracking-tight">Acciones Rápidas</h2>
                    <p class="text-sm text-white/40">Gestión administrativa frecuente</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button onclick="switchTab('inscripciones')"
                    class="flex items-center gap-4 p-4 rounded-xl bg-gold text-primary-dark hover:bg-gold/90 transition-all text-left">
                    <div class="size-10 bg-black/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined font-bold">person_add</span>
                    </div>
                    <div>
                        <p class="font-extrabold uppercase text-xs">Inscribir Nuevo</p>
                        <p class="text-[10px] opacity-80">Registrar nuevo ingreso</p>
                    </div>
                </button>
                <button
                    class="flex items-center gap-4 p-4 rounded-xl bg-card-dark border border-white/5 hover:border-gold/30 transition-all text-left group">
                    <div
                        class="size-10 bg-gold/10 rounded-lg flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-primary-dark transition-all">
                        <span class="material-symbols-outlined">assignment_turned_in</span>
                    </div>
                    <div>
                        <p class="font-bold text-white text-sm">Generar Notas</p>
                        <p class="text-[10px] text-white/40">Record académico oficial</p>
                    </div>
                </button>
                <button
                    class="flex items-center gap-4 p-4 rounded-xl bg-card-dark border border-white/5 hover:border-gold/30 transition-all text-left group">
                    <div
                        class="size-10 bg-gold/10 rounded-lg flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-primary-dark transition-all">
                        <span class="material-symbols-outlined">upload_file</span>
                    </div>
                    <div>
                        <p class="font-bold text-white text-sm">Subir Calificaciones</p>
                        <p class="text-[10px] text-white/40">Carga masiva de resultados</p>
                    </div>
                </button>
                <button
                    class="flex items-center gap-4 p-4 rounded-xl bg-card-dark border border-white/5 hover:border-gold/30 transition-all text-left group">
                    <div
                        class="size-10 bg-gold/10 rounded-lg flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-primary-dark transition-all">
                        <span class="material-symbols-outlined">mail</span>
                    </div>
                    <div>
                        <p class="font-bold text-white text-sm">Enviar Aviso</p>
                        <p class="text-[10px] text-white/40">Circular a comunidad CBUH</p>
                    </div>
                </button>
            </div>
        </div>
        <div class="bg-primary-dark rounded-2xl border border-white/10 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <h2 class="text-lg font-bold text-white uppercase tracking-tighter">Actividad</h2>
                <button
                    class="text-[10px] font-black text-gold uppercase tracking-widest border border-gold/20 px-3 py-1 rounded-full hover:bg-gold hover:text-primary-dark transition-all">Ver
                    Todo</button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
                <?php if (!empty($actividades)): ?>
                    <?php foreach ($actividades as $act): ?>
                        <div class="flex gap-4 items-start">
                            <div class="size-2 mt-2 bg-green-500 rounded-full shrink-0 shadow-[0_0_10px_rgba(34,197,94,0.5)]">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">Nota Actualizada</p>
                                <p class="text-xs text-white/40">
                                    <?php echo htmlspecialchars($act['nombres'] . ' ' . $act['apellidos']); ?> •
                                    <?php echo htmlspecialchars($act['materia']); ?>
                                </p>
                                <p class="text-[10px] text-gold mt-1 uppercase font-bold tracking-widest">
                                    <?php echo time_elapsed_string($act['actualizado_el']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center h-full text-white/20">
                        <span class="material-symbols-outlined text-4xl mb-2">history</span>
                        <p class="text-xs uppercase tracking-widest font-bold">Sin actividad reciente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabla de requerimientos pendientes -->
    <div class="bg-primary-dark rounded-2xl border border-white/10 overflow-hidden">
        <div class="p-6 border-b border-white/5 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-white uppercase tracking-tight">Requerimientos Pendientes</h2>
                <p class="text-sm text-white/40">Estudiantes con documentación incompleta</p>
            </div>
            <button
                class="px-4 py-2 border border-white/10 rounded-lg text-xs font-bold text-gold uppercase tracking-widest hover:bg-white/5 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-base">filter_list</span> Filtrar
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-black/20 text-white/40 text-[10px] uppercase tracking-[0.2em]">
                    <tr>
                        <th class="px-6 py-4 font-black">Estudiante</th>
                        <th class="px-6 py-4 font-black">Cédula</th>
                        <th class="px-6 py-4 font-black">Pendiente</th>
                        <th class="px-6 py-4 font-black">Nivel</th>
                        <th class="px-6 py-4 font-black">Estado</th>
                        <th class="px-6 py-4 font-black text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <!-- Fila de ejemplo -->
                    <?php if (!empty($pendientes)): ?>
                        <?php foreach ($pendientes as $p): ?>
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="size-8 rounded-full bg-card-dark border border-gold/30 flex items-center justify-center text-[10px] font-bold text-gold">
                                            <?php echo substr($p['nombres'], 0, 1) . substr($p['apellidos'], 0, 1); ?>
                                        </div>
                                        <span
                                            class="text-sm font-bold text-white"><?php echo htmlspecialchars($p['nombres'] . ' ' . $p['apellidos']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-white/80">
                                    <?php echo htmlspecialchars($p['cedula']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded bg-orange-500/10 text-orange-400 text-[10px] font-black uppercase tracking-wider">
                                        <?php echo htmlspecialchars(formatDocType($p['tipo_documento'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-white/60"><?php echo $p['año_actual']; ?>º Año</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 text-orange-500">
                                        <span class="material-symbols-outlined text-sm">warning</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Pendiente</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        onclick="window.location.href='?tab=directorio&search=<?php echo urlencode($p['cedula']); ?>'"
                                        class="text-gold hover:underline text-[10px] font-black uppercase tracking-widest">
                                        Revisar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6"
                                class="px-6 py-8 text-center text-white/40 font-bold uppercase tracking-widest text-xs">
                                No hay requerimientos pendientes
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
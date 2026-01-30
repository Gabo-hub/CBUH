<div class="p-8 space-y-8">
    <!-- Sección de Búsqueda -->
    <section class="bg-primary-dark rounded-2xl border border-white/10 p-6">
        <div class="md:col-span-4">
            <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-1.5 ml-1">Buscar
                Profesor</label>
            <div class="relative">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gold/60 text-lg">search</span>
                <input id="search-input"
                    class="w-full bg-black/20 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:ring-1 focus:ring-gold/50 outline-none transition-all placeholder:text-white/20"
                    placeholder="Buscar profesor por nombre o cédula..." type="text" />
            </div>
        </div>
    </section>

    <!-- Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-primary-dark p-5 rounded-2xl border border-white/10">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Total Horas</p>
            <h3 id="stat-total-hours" class="text-2xl font-black text-white">...</h3>
            <p class="text-[9px] text-gold font-bold uppercase mt-2">Semana Actual</p>
        </div>
        <div class="bg-primary-dark p-5 rounded-2xl border border-white/10">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Cupos Disponibles</p>
            <h3 id="stat-available-slots" class="text-2xl font-black text-gold">...</h3>
            <p class="text-[9px] text-green-400 font-bold uppercase mt-2">Por asignar</p>
        </div>
        <div class="bg-primary-dark p-5 rounded-2xl border border-white/10">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Carga Máxima</p>
            <h3 id="stat-max-load" class="text-2xl font-black text-white">...</h3>
            <p class="text-[9px] text-red-400 font-bold uppercase mt-2">Al límite (5/5)</p>
        </div>
        <div class="bg-primary-dark p-5 rounded-2xl border border-white/10">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Materias Activas</p>
            <h3 id="stat-active-subjects" class="text-2xl font-black text-white">...</h3>
            <p class="text-[9px] text-gold font-bold uppercase mt-2">En pensum</p>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-white uppercase tracking-tight italic">ASIGNACIÓN DE <span
                    class="text-gold">MATERIAS</span></h1>
            <p class="text-white/40 text-sm font-medium mt-1 uppercase tracking-widest">Gestión de carga
                académica para el personal docente</p>
        </div>
        <button id="btn-nuevo-profesor"
            class="bg-gold hover:bg-white text-primary-dark font-black px-6 py-2.5 rounded-xl flex items-center gap-2 transition-all uppercase text-xs tracking-widest shadow-lg shadow-gold/10">
            <span class="material-symbols-outlined font-bold">person_add</span>
            Nuevo Profesor
        </button>
    </div>

    <div class="bg-primary-dark rounded-2xl border border-white/10 overflow-hidden">
        <div class="p-6 border-b border-white/5 flex justify-between items-center bg-black/10">
            <div class="flex items-center gap-4">
                <div class="bg-gold/10 px-4 py-2 rounded-lg border border-gold/20">
                    <span id="teachers-count" class="text-gold font-bold text-sm">0 Profesores Registrados</span>
                </div>
            </div>
            <div class="flex gap-2">
                <button
                    class="px-4 py-2 border border-white/10 rounded-lg text-xs font-bold text-white/60 uppercase tracking-widest hover:bg-white/5 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">download</span> Exportar
                </button>
                <button
                    class="px-4 py-2 border border-white/10 rounded-lg text-xs font-bold text-gold uppercase tracking-widest hover:bg-white/5 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">filter_list</span> Filtrar
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-black/20 text-white/40 text-[10px] uppercase tracking-[0.2em]">
                    <tr>
                        <th class="px-6 py-5 font-black">Información del Profesor</th>
                        <th class="px-6 py-5 font-black">Cédula</th>
                        <th class="px-6 py-5 font-black">Materias Asignadas (Máx. 5)</th>
                        <th class="px-6 py-5 font-black text-center">Carga</th>
                        <th class="px-6 py-5 font-black text-right">Acción</th>
                    </tr>
                </thead>
                <tbody id="teachers-tbody" class="divide-y divide-white/5">
                    <!-- Se llenará dinámicamente -->
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold mx-auto"></div>
                            <p class="text-white/40 mt-4">Cargando profesores...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div
            class="p-6 bg-black/20 border-t border-white/5 flex items-center justify-between text-[10px] font-bold text-white/40 uppercase tracking-[0.2em]">
            <div class="flex items-center gap-4">
                <span id="pagination-info">Cargando...</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición de Asignaciones -->
<div id="modal-edit-assignments" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 hidden">
    <div
        class="bg-primary-dark rounded-2xl border border-white/10 p-8 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-white uppercase">Editar Asignaciones</h2>
            <button id="btn-close-modal" class="text-white/60 hover:text-gold transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div id="modal-content">
            <p class="text-white/60 mb-4">Profesor: <span id="teacher-name" class="text-gold font-bold"></span></p>

            <div class="space-y-2 mb-6">
                <label class="text-[10px] font-bold text-gold uppercase tracking-widest">Materias Disponibles</label>
                <div id="subjects-list" class="space-y-2 max-h-96 overflow-y-auto">
                    <!-- Se llenará dinámicamente -->
                </div>
            </div>

            <div class="flex gap-4">
                <button id="btn-save-assignments"
                    class="flex-1 bg-gold hover:bg-white text-primary-dark font-black px-6 py-3 rounded-xl uppercase text-xs tracking-widest transition-all">
                    Guardar Cambios
                </button>
                <button id="btn-cancel-modal"
                    class="flex-1 bg-white/10 hover:bg-white/20 text-white font-black px-6 py-3 rounded-xl uppercase text-xs tracking-widest transition-all">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Nuevo Profesor -->
<div id="modal-nuevo-profesor" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 hidden">
    <div
        class="bg-primary-dark rounded-2xl border border-white/10 p-8 max-w-3xl w-full mx-4 max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-white uppercase">Nuevo Profesor</h2>
            <button id="btn-close-nuevo-modal" class="text-white/60 hover:text-gold transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="form-nuevo-profesor" class="space-y-6">
            <!-- Información Personal -->
            <div class="border-b border-white/10 pb-4">
                <h3 class="text-sm font-bold text-gold uppercase tracking-wider mb-4">Información Personal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Cédula
                            *</label>
                        <input type="text" name="cedula" required
                            class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                            placeholder="V-12345678">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Teléfono</label>
                        <input type="tel" name="telefono"
                            class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                            placeholder="0414-1234567">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Nombres
                            *</label>
                        <input type="text" name="nombres" required
                            class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                            placeholder="Juan Carlos">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Apellidos
                            *</label>
                        <input type="text" name="apellidos" required
                            class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                            placeholder="Pérez González">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Fecha de
                            Nacimiento</label>
                        <input type="date" name="fecha_nacimiento"
                            class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Lugar de
                            Nacimiento</label>
                        <input type="text" name="lugar_nacimiento"
                            class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                            placeholder="Caracas, Venezuela">
                    </div>
                </div>
            </div>

            <!-- Información Académica -->
            <div class="border-b border-white/10 pb-4">
                <h3 class="text-sm font-bold text-gold uppercase tracking-wider mb-4">Información Académica</h3>
                <div>
                    <label
                        class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Especialidad</label>
                    <input type="text" name="especialidad"
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                        placeholder="Lic. en Educación Matemática">
                </div>
                <div class="mt-4">
                    <label class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Resumen
                        Profesional</label>
                    <textarea name="resumen_profesional" rows="3"
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all resize-none"
                        placeholder="Breve descripción de la experiencia profesional..."></textarea>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="border-b border-white/10 pb-4">
                <h3 class="text-sm font-bold text-gold uppercase tracking-wider mb-4">Información de Contacto</h3>
                <div>
                    <label
                        class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Dirección</label>
                    <textarea name="direccion" rows="2"
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all resize-none"
                        placeholder="Dirección completa..."></textarea>
                </div>
            </div>

            <!-- Crear Usuario (Opcional) -->
            <div class="bg-white/5 p-4 rounded-lg">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="crear-usuario-check" class="accent-gold size-5">
                    <div>
                        <span class="text-sm font-bold text-white">Crear usuario para acceso al sistema</span>
                        <p class="text-[10px] text-white/40 mt-1">Se creará un usuario con clave temporal: <span
                                class="text-gold">profesor123</span></p>
                    </div>
                </label>
                <div id="usuario-fields" class="mt-4 space-y-4 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Usuario</label>
                            <input type="text" name="usuario"
                                class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                                placeholder="jperez">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Correo
                                Electrónico</label>
                            <input type="email" name="correo"
                                class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                                placeholder="jperez@cbuh.edu.ve">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex gap-4 pt-4">
                <button type="submit"
                    class="flex-1 bg-gold hover:bg-white text-primary-dark font-black px-6 py-3 rounded-xl uppercase text-xs tracking-widest transition-all">
                    <span class="flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        Crear Profesor
                    </span>
                </button>
                <button type="button" id="btn-cancel-nuevo-modal"
                    class="flex-1 bg-white/10 hover:bg-white/20 text-white font-black px-6 py-3 rounded-xl uppercase text-xs tracking-widest transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    (function () {
        let teachers = [];
        let allSubjects = [];
        let currentTeacherId = null;
        let searchTimeout = null;

        // Inicializar
        async function init() {
            await loadStats();
            await loadSubjects();
            await loadTeachers();
        }

        // Cargar estadísticas
        async function loadStats() {
            try {
                const response = await fetch('actions/get_teacher_stats.php');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('stat-total-hours').textContent = data.total_hours + ' hrs';
                    document.getElementById('stat-available-slots').textContent = data.available_slots;
                    document.getElementById('stat-max-load').textContent = data.teachers_at_max + ' Docentes';
                    document.getElementById('stat-active-subjects').textContent = data.active_subjects;
                }
            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }

        // Cargar materias disponibles
        async function loadSubjects() {
            try {
                const response = await fetch('actions/get_available_subjects.php');
                const data = await response.json();
                if (data.success) {
                    allSubjects = data.subjects;
                }
            } catch (error) {
                console.error('Error al cargar materias:', error);
            }
        }

        // Cargar profesores
        async function loadTeachers() {
            try {
                const response = await fetch('actions/get_teachers.php');
                const data = await response.json();
                if (data.success) {
                    teachers = data.teachers;
                    renderTeachers(teachers);
                    document.getElementById('teachers-count').textContent = `${data.total} Profesores Registrados`;
                    document.getElementById('pagination-info').textContent = `${data.total} Profesores encontrados`;
                }
            } catch (error) {
                console.error('Error al cargar profesores:', error);
                showError('Error al cargar profesores');
            }
        }

        // Buscar profesores
        async function searchTeachers(query) {
            if (!query.trim()) {
                loadTeachers();
                return;
            }

            try {
                const response = await fetch(`actions/search_teachers.php?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                if (data.success) {
                    teachers = data.teachers;
                    renderTeachers(teachers);
                    document.getElementById('pagination-info').textContent = `${data.total} resultados`;
                }
            } catch (error) {
                console.error('Error en búsqueda:', error);
            }
        }

        // Renderizar profesores
        function renderTeachers(teachersList) {
            const tbody = document.getElementById('teachers-tbody');

            if (teachersList.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-white/40">
                        No se encontraron profesores
                    </td>
                </tr>
            `;
                return;
            }

            tbody.innerHTML = teachersList.map(teacher => {
                const cargaColor = {
                    'empty': 'bg-white/5 text-white/40',
                    'normal': 'bg-green-500/10 text-green-400',
                    'high': 'bg-orange-500/10 text-orange-400',
                    'full': 'bg-red-500/10 text-red-400'
                }[teacher.carga_estado] || 'bg-white/5 text-white/40';

                return `
                <tr class="hover:bg-white/5 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="size-10 rounded-full bg-card-dark border border-gold/30 flex items-center justify-center text-xs font-bold text-gold overflow-hidden">
                                ${teacher.url_foto ?
                        `<img alt="${teacher.nombre_completo}" class="w-full h-full object-cover" src="${teacher.url_foto}" />` :
                        teacher.nombres.charAt(0) + teacher.apellidos.charAt(0)
                    }
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white uppercase group-hover:text-gold transition-colors">
                                    ${teacher.nombre_completo}
                                </p>
                                <p class="text-[10px] text-white/40 font-bold uppercase tracking-wider">
                                    ${teacher.especialidad || 'Docente'}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-white/80">${teacher.cedula}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1.5">
                            ${teacher.materias.length > 0 ?
                        teacher.materias.slice(0, 3).map(materia =>
                            `<button class="px-2.5 py-1 rounded-md bg-white/5 border border-white/10 text-white/70 text-[10px] font-bold uppercase hover:border-gold/50 transition-all">${materia}</button>`
                        ).join('') + (teacher.materias.length > 3 ? `<span class="px-2.5 py-1 text-[10px] text-white/40">+${teacher.materias.length - 3} más</span>` : '') :
                        '<span class="text-[10px] italic text-white/20">Sin materias asignadas</span>'
                    }
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full ${cargaColor} text-[10px] font-black uppercase tracking-wider">
                            ${teacher.materias_count}/5
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editTeacherAssignments(${teacher.id})" class="bg-gold text-primary-dark px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-white transition-all shadow-md">
                            Editar Asignaturas
                        </button>
                    </td>
                </tr>
            `;
            }).join('');
        }

        // Mostrar error
        function showError(message) {
            const tbody = document.getElementById('teachers-tbody');
            tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-red-400">
                    ${message}
                </td>
            </tr>
        `;
        }

        // Editar asignaciones de profesor
        window.editTeacherAssignments = function (teacherId) {
            const teacher = teachers.find(t => t.id == teacherId);
            if (!teacher) return;

            currentTeacherId = teacherId;
            document.getElementById('teacher-name').textContent = teacher.nombre_completo;

            // Renderizar lista de materias
            const subjectsList = document.getElementById('subjects-list');
            subjectsList.innerHTML = allSubjects.map(subject => {
                const isAssigned = teacher.materias.includes(subject.nombre);
                return `
                <label class="flex items-center gap-3 p-3 rounded-lg bg-card-dark border border-white/5 cursor-pointer hover:border-gold/30 transition-all">
                    <input type="checkbox" class="subject-checkbox accent-gold size-4" 
                           value="${subject.materia_id}" 
                           ${isAssigned ? 'checked' : ''} />
                    <span class="text-xs font-bold text-white">${subject.display_name}</span>
                </label>
            `;
            }).join('');

            // Mostrar modal
            document.getElementById('modal-edit-assignments').classList.remove('hidden');
        };

        // Guardar asignaciones
        async function saveAssignments() {
            const checkboxes = document.querySelectorAll('.subject-checkbox:checked');
            const materiasIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

            if (materiasIds.length > 5) {
                alert('Un profesor no puede tener más de 5 materias asignadas');
                return;
            }

            try {
                const response = await fetch('actions/update_teacher_subjects.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        docente_id: currentTeacherId,
                        materias_ids: materiasIds
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    closeModal();
                    await loadTeachers();
                    await loadStats();
                } else {
                    alert(data.error || 'Error al actualizar asignaciones');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar asignaciones');
            }
        }

        // Cerrar modal
        function closeModal() {
            document.getElementById('modal-edit-assignments').classList.add('hidden');
            currentTeacherId = null;
        }

        // Guardar nuevo profesor
        async function saveNuevoProfesor(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = {
                cedula: formData.get('cedula'),
                nombres: formData.get('nombres'),
                apellidos: formData.get('apellidos'),
                especialidad: formData.get('especialidad'),
                telefono: formData.get('telefono'),
                fecha_nacimiento: formData.get('fecha_nacimiento'),
                lugar_nacimiento: formData.get('lugar_nacimiento'),
                direccion: formData.get('direccion'),
                resumen_profesional: formData.get('resumen_profesional'),
                crear_usuario: document.getElementById('crear-usuario-check').checked,
                usuario: formData.get('usuario'),
                correo: formData.get('correo')
            };

            try {
                const response = await fetch('actions/create_teacher.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.success) {
                    alert('¡Profesor creado exitosamente!');
                    document.getElementsById('modal-nuevo-profesor').classList.add('hidden');
                    await loadTeachers();
                    await loadStats();
                } else {
                    alert(result.error || 'Error al crear profesor');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al crear profesor');
            }
        }


        // Event Listeners
        document.getElementById('search-input').addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchTeachers(e.target.value);
            }, 300);
        });

        document.getElementById('btn-save-assignments').addEventListener('click', saveAssignments);
        document.getElementById('btn-close-modal').addEventListener('click', closeModal);
        document.getElementById('btn-cancel-modal').addEventListener('click', closeModal);

        // Botón Nuevo Profesor
        document.getElementById('btn-nuevo-profesor').addEventListener('click', () => {
            document.getElementById('form-nuevo-profesor').reset();
            document.getElementById('usuario-fields').classList.add('hidden');
            document.getElementById('modal-nuevo-profesor').classList.remove('hidden');
        });
        
        // Toggle campos de usuario
        document.getElementById('crear-usuario-check').addEventListener('change', (e) => {
            if (e.target.checked) {
                document.getElementById('usuario-fields').classList.remove('hidden');
            } else {
                document.getElementById('usuario-fields').classList.add('hidden');
            }
        });
        
        // Form submit nuevo profesor
        document.getElementById('form-nuevo-profesor').addEventListener('submit', saveNuevoProfesor);
        
        // Cerrar modales nuevo profesor
        document.getElementById('btn-close-nuevo-modal').addEventListener('click', () => {
            document.getElementById('modal-nuevo-profesor').classList.add('hidden');
        });
        document.getElementById('btn-cancel-nuevo-modal').addEventListener('click', () => {
            document.getElementById('modal-nuevo-profesor').classList.add('hidden');
        });
        
        // Cerrar modal al hacer clic fuera (asignaciones)
        document.getElementById('modal-edit-assignments').addEventListener('click', (e) => {
            if (e.target.id === 'modal-edit-assignments') {
                closeModal();
            }
        });
        
        // Cerrarmodal al hacer clic fuera (nuevo profesor)
        document.getElementById('modal-nuevo-profesor').addEventListener('click', (e) => {
            if (e.target.id === 'modal-nuevo-profesor') {
                document.getElementById('modal-nuevo-profesor').classList.add('hidden');
            }
        });

        // Inicializar
        init();
    })();
</script>
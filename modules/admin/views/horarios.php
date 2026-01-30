<div class="flex-1 flex overflow-hidden h-full">
    <!-- Calendario Principal -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        <!-- Controles de Vista -->
        <div class="p-6 flex items-center justify-between">
            <button id="btn-nueva-clase"
                class="flex items-center gap-2 px-4 py-2 bg-gold text-primary-dark rounded-lg text-xs font-black uppercase tracking-widest hover:bg-white transition-all">
                <span class="material-symbols-outlined text-lg">add</span>
                Nueva Clase
            </button>
            <!-- Controles de Calendario -->
            <div class="p-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="btn-prev-month"
                        class="size-8 rounded-lg flex items-center justify-center border border-white/10 text-white/60 hover:text-white hover:border-white/30 transition-all">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <h3 id="current-month" class="text-lg font-black text-white uppercase tracking-wider">Cargando...
                    </h3>
                    <button id="btn-next-month"
                        class="size-8 rounded-lg flex items-center justify-center border border-white/10 text-white/60 hover:text-white hover:border-white/30 transition-all">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="flex bg-black/30 p-1 rounded-lg">
                    <button id="btn-vista-semanal"
                        class="px-4 py-1.5 rounded-md bg-white/10 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">Vista
                        Semanal</button>
                    <button id="btn-vista-mensual"
                        class="px-4 py-1.5 rounded-md text-white/40 hover:text-white text-[10px] font-bold uppercase tracking-wider transition-colors">Mensual</button>
                </div>
                <button id="btn-settings"
                    class="size-9 rounded-full border border-white/10 flex items-center justify-center text-white/60 hover:text-gold hover:border-gold transition-all">
                    <span class="material-symbols-outlined text-lg">settings</span>
                </button>
            </div>
        </div>

        <!-- Encabezado de la cuadrícula del calendario -->
        <div class="grid grid-cols-6 border-b border-white/10 text-center">
            <div class="py-3 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] border-r border-white/5">
                Hora</div>
            <div class="py-3 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] border-r border-white/5">
                Lunes</div>
            <div class="py-3 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] border-r border-white/5">
                Martes</div>
            <div class="py-3 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] border-r border-white/5">
                Miércoles</div>
            <div class="py-3 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] border-r border-white/5">
                Jueves</div>
            <div class="py-3 text-[10px] font-black text-white/30 uppercase tracking-[0.2em]">Viernes</div>
        </div>

        <!-- Cuerpo desplazable del calendario -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="grid grid-cols-6 relative min-h-[600px]">
                <!-- Columna de Hora -->
                <div class="border-r border-white/10 bg-black/10">
                    <div
                        class="h-24 border-b border-white/5 flex items-center justify-center text-xs font-bold text-white/40">
                        07:00 AM</div>
                    <div
                        class="h-24 border-b border-white/5 flex items-center justify-center text-xs font-bold text-white/40">
                        08:00 AM</div>
                    <div
                        class="h-24 border-b border-white/5 flex items-center justify-center text-xs font-bold text-white/40">
                        09:00 AM</div>
                    <div
                        class="h-24 border-b border-white/5 flex items-center justify-center text-xs font-bold text-white/40">
                        10:00 AM</div>
                    <div
                        class="h-24 border-b border-white/5 flex items-center justify-center text-xs font-bold text-white/40">
                        11:00 AM</div>
                    <div
                        class="h-24 border-b border-white/5 flex items-center justify-center text-xs font-bold text-white/40">
                        12:00 PM</div>
                    <div
                        class="h-24 border-b border-white/5 flex items-center justify-center text-xs font-bold text-white/40">
                        01:00 PM</div>
                    <div
                        class="h-24 border-b border-white/5 flex items-center justify-center text-xs font-bold text-white/40">
                        02:00 PM</div>
                    <div
                        class="h-24 border-b border-white/5 flex items-center justify-center text-xs font-bold text-white/40">
                        03:00 PM</div>
                    <div
                        class="h-24 border-b border-white/5 flex items-center justify-center text-xs font-bold text-white/40">
                        04:00 PM</div>
                </div>

                <!-- Columnas de Día (se llenarán dinámicamente) -->
                <div id="day-1" class="border-r border-white/5 relative p-1"></div>
                <div id="day-2" class="border-r border-white/5 relative p-1"></div>
                <div id="day-3" class="border-r border-white/5 relative p-1"></div>
                <div id="day-4" class="border-r border-white/5 relative p-1"></div>
                <div id="day-5" class="relative p-1"></div>
            </div>
        </div>
    </div>

    <!-- Barra lateral de herramientas -->
    <aside id="editor-rapido" class="w-[300px] bg-primary-dark border-l border-white/10 flex flex-col transition-all">
        <div class="p-6 border-b border-white/10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-white uppercase tracking-widest">Editor Rápido</h3>
                <button id="btn-close-editor" class="text-white/60 hover:text-gold transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="form-schedule" class="space-y-4">
                <input type="hidden" id="schedule-id" name="id">
                <input type="hidden" id="carga-id" name="carga_id">

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gold uppercase tracking-widest">Materia</label>
                    <select id="select-materia" name="materia"
                        class="w-full bg-card-dark border-none rounded-lg text-xs text-white p-2.5 focus:ring-1 focus:ring-gold outline-none">
                        <option value="">Cargando...</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gold uppercase tracking-widest">Día</label>
                    <div class="flex gap-2">
                        <button type="button" data-day="1"
                            class="dia-btn flex-1 py-2 bg-card-dark text-white/40 font-black text-[10px] rounded hover:text-white">LUN</button>
                        <button type="button" data-day="2"
                            class="dia-btn flex-1 py-2 bg-card-dark text-white/40 font-black text-[10px] rounded hover:text-white">MAR</button>
                        <button type="button" data-day="3"
                            class="dia-btn flex-1 py-2 bg-card-dark text-white/40 font-black text-[10px] rounded hover:text-white">MIE</button>
                        <button type="button" data-day="4"
                            class="dia-btn flex-1 py-2 bg-card-dark text-white/40 font-black text-[10px] rounded hover:text-white">JUE</button>
                        <button type="button" data-day="5"
                            class="dia-btn flex-1 py-2 bg-card-dark text-white/40 font-black text-[10px] rounded hover:text-white">VIE</button>
                    </div>
                    <input type="hidden" id="dia-semana" name="dia_semana">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gold uppercase tracking-widest">Inicio</label>
                        <input type="time" id="hora-inicio" name="hora_inicio"
                            class="w-full bg-card-dark border-none rounded-lg text-xs text-white p-2.5 outline-none" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gold uppercase tracking-widest">Fin</label>
                        <input type="time" id="hora-fin" name="hora_fin"
                            class="w-full bg-card-dark border-none rounded-lg text-xs text-white p-2.5 outline-none" />
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gold uppercase tracking-widest">Aula</label>
                    <input type="text" id="aula" name="aula" placeholder="Ej: Aula 4B"
                        class="w-full bg-card-dark border-none rounded-lg text-xs text-white p-2.5 outline-none" />
                </div>
                <button type="submit"
                    class="w-full bg-white/10 hover:bg-white/20 text-white text-xs font-black uppercase tracking-widest py-3 rounded-xl transition-all border border-white/5 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Guardar
                </button>
            </form>
        </div>

        <div class="flex-1 p-6 overflow-y-auto">
            <h3 class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-4">Filtrar por año</h3>
            <div class="space-y-2">
                <label
                    class="flex items-center gap-3 p-3 rounded-lg bg-card-dark border border-white/5 cursor-pointer hover:border-gold/30 transition-all">
                    <input type="checkbox" checked class="year-filter accent-gold size-4" data-year="1" />
                    <span class="text-xs font-bold text-white">Primer Año</span>
                </label>
                <label
                    class="flex items-center gap-3 p-3 rounded-lg bg-card-dark border border-white/5 cursor-pointer hover:border-gold/30 transition-all">
                    <input type="checkbox" checked class="year-filter accent-gold size-4" data-year="2" />
                    <span class="text-xs font-bold text-white">Segundo Año</span>
                </label>
                <label
                    class="flex items-center gap-3 p-3 rounded-lg bg-card-dark border border-white/5 cursor-pointer hover:border-gold/30 transition-all">
                    <input type="checkbox" checked class="year-filter accent-gold size-4" data-year="3" />
                    <span class="text-xs font-bold text-white">Tercer Año</span>
                </label>
            </div>
        </div>
    </aside>
</div>

<script>
    (function () {
        let schedules = [];
        let subjects = [];
        let selectedDay = null;
        let viewMode = 'weekly'; // 'weekly' or 'monthly'
        let currentMonth = new Date();

        // Cargar datos iniciales
        async function init() {
            await loadSubjects();
            await loadSchedules();
            renderCalendar();
        }

        // Cargar materias disponibles
        async function loadSubjects() {
            try {
                const response = await fetch('actions/get_available_subjects.php');
                const data = await response.json();
                if (data.success) {
                    subjects = data.subjects;
                    renderSubjectsDropdown();
                }
            } catch (error) {
                console.error('Error al cargar materias:', error);
            }
        }

        // Cargar horarios
        async function loadSchedules() {
            try {
                const activeYears = getActiveYears();
                let url = 'actions/get_schedules.php';
                if (activeYears.length > 0) {
                    url += '?year=' + activeYears.join(',');
                }

                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    schedules = data.schedules;
                    renderCalendar();
                }
            } catch (error) {
                console.error('Error al cargar horarios:', error);
            }
        }

        // Renderizar dropdown de materias
        function renderSubjectsDropdown() {
            const select = document.getElementById('select-materia');
            select.innerHTML = '<option value="">Seleccionar materia...</option>';
            subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.carga_id;
                option.textContent = subject.display_name;
                select.appendChild(option);
            });
        }

        // Renderizar calendario
        function renderCalendar() {
            // Limpiar días
            for (let i = 1; i <= 5; i++) {
                const dayCol = document.getElementById(`day-${i}`);
                dayCol.innerHTML = '';
            }

            // Renderizar bloques de horario
            schedules.forEach(schedule => {
                const dayCol = document.getElementById(`day-${schedule.dia_semana}`);
                if (!dayCol) return;

                const block = createScheduleBlock(schedule);
                dayCol.appendChild(block);
            });

            updateMonthDisplay();
        }

        // Crear bloque de horario
        function createScheduleBlock(schedule) {
            const div = document.createElement('div');
            div.className = 'absolute left-1 right-1 bg-primary/80 border border-gold/50 rounded-lg p-2 hover:scale-[1.02] transition-transform cursor-pointer shadow-lg';
            div.dataset.scheduleId = schedule.id;

            // Calcular posición basada en hora
            const startMinutes = timeToMinutes(schedule.hora_inicio);
            const endMinutes = timeToMinutes(schedule.hora_fin);
            const baseStart = 7 * 60; // 7:00 AM
            const pixelsPerMinute = 96 / 60; // 96px (height) / 60 minutes

            const topPixels = (startMinutes - baseStart) * pixelsPerMinute;
            const heightPixels = (endMinutes - startMinutes) * pixelsPerMinute;

            div.style.top = `${topPixels}px`;
            div.style.height = `${heightPixels}px`;

            div.innerHTML = `
            <p class="text-[9px] font-black text-gold uppercase tracking-wider mb-0.5">${schedule.materia_nombre}</p>
            <p class="text-[10px] text-white font-bold leading-tight">${schedule.aula || 'Sin aula'}</p>
            <p class="text-[9px] text-white/40 mt-1">${formatTime(schedule.hora_inicio)} - ${formatTime(schedule.hora_fin)}</p>
        `;

            div.addEventListener('click', () => editSchedule(schedule));

            return div;
        }

        // Convertir hora a minutos
        function timeToMinutes(time) {
            const [hours, minutes] = time.split(':').map(Number);
            return hours * 60 + minutes;
        }

        // Formatear hora
        function formatTime(time) {
            const [hours, minutes] = time.split(':');
            const h = parseInt(hours);
            const ampm = h >= 12 ? 'PM' : 'AM';
            const displayHour = h > 12 ? h - 12 : (h === 0 ? 12 : h);
            return `${displayHour}:${minutes} ${ampm}`;
        }

        // Obtener años activos desde filtros
        function getActiveYears() {
            const checkboxes = document.querySelectorAll('.year-filter:checked');
            return Array.from(checkboxes).map(cb => parseInt(cb.dataset.year));
        }

        // Actualizar display del mes
        function updateMonthDisplay() {
            const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            document.getElementById('current-month').textContent = `${months[currentMonth.getMonth()]} ${currentMonth.getFullYear()}`;
        }

        // Editar horario
        function editSchedule(schedule) {
            document.getElementById('schedule-id').value = schedule.id;
            document.getElementById('carga-id').value = schedule.carga_id;
            document.getElementById('select-materia').value = schedule.carga_id;
            document.getElementById('hora-inicio').value = schedule.hora_inicio;
            document.getElementById('hora-fin').value = schedule.hora_fin;
            document.getElementById('aula').value = schedule.aula || '';

            selectDay(schedule.dia_semana);
        }

        // Seleccionar día
        function selectDay(day) {
            selectedDay = day;
            document.getElementById('dia-semana').value = day;

            document.querySelectorAll('.dia-btn').forEach(btn => {
                btn.classList.remove('bg-gold', 'text-primary-dark');
                btn.classList.add('bg-card-dark', 'text-white/40');
            });

            const selectedBtn = document.querySelector(`.dia-btn[data-day="${day}"]`);
            if (selectedBtn) {
                selectedBtn.classList.add('bg-gold', 'text-primary-dark');
                selectedBtn.classList.remove('bg-card-dark', 'text-white/40');
            }
        }

        // Limpiar formulario
        function clearForm() {
            document.getElementById('form-schedule').reset();
            document.getElementById('schedule-id').value = '';
            document.getElementById('carga-id').value = '';
            selectedDay = null;
            document.querySelectorAll('.dia-btn').forEach(btn => {
                btn.classList.remove('bg-gold', 'text-primary-dark');
                btn.classList.add('bg-card-dark', 'text-white/40');
            });
        }

        // Event Listeners
        document.getElementById('btn-nueva-clase').addEventListener('click', () => {
            clearForm();
        });

        document.querySelectorAll('.dia-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                selectDay(parseInt(btn.dataset.day));
            });
        });

        document.getElementById('form-schedule').addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!selectedDay) {
                alert('Por favor selecciona un día');
                return;
            }

            const formData = {
                id: document.getElementById('schedule-id').value || null,
                carga_id: parseInt(document.getElementById('select-materia').value),
                dia_semana: selectedDay,
                hora_inicio: document.getElementById('hora-inicio').value,
                hora_fin: document.getElementById('hora-fin').value,
                aula: document.getElementById('aula').value
            };

            try {
                const response = await fetch('actions/save_schedule.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    clearForm();
                    await loadSchedules();
                } else {
                    alert(data.error || 'Error al guardar horario');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar horario');
            }
        });

        // Filtros de año
        document.querySelectorAll('.year-filter').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                loadSchedules();
            });
        });

        // Cambio de vista
        document.getElementById('btn-vista-semanal').addEventListener('click', function () {
            viewMode = 'weekly';
            this.classList.add('bg-white/10', 'text-white');
            this.classList.remove('text-white/40');
            document.getElementById('btn-vista-mensual').classList.remove('bg-white/10', 'text-white');
            document.getElementById('btn-vista-mensual').classList.add('text-white/40');
        });

        document.getElementById('btn-vista-mensual').addEventListener('click', function () {
            viewMode = 'monthly';
            this.classList.add('bg-white/10', 'text-white');
            this.classList.remove('text-white/40');
            document.getElementById('btn-vista-semanal').classList.remove('bg-white/10', 'text-white');
            document.getElementById('btn-vista-semanal').classList.add('text-white/40');
        });

        // Navegación de mes
        document.getElementById('btn-prev-month').addEventListener('click', () => {
            currentMonth.setMonth(currentMonth.getMonth() - 1);
            updateMonthDisplay();
        });

        document.getElementById('btn-next-month').addEventListener('click', () => {
            currentMonth.setMonth(currentMonth.getMonth() + 1);
            updateMonthDisplay();
        });

        // Settings
        document.getElementById('btn-settings').addEventListener('click', () => {
            alert('Configuración del calendario (próximamente)');
        });

        // Inicializar
        init();
    })();
</script>
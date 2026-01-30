<div class="p-8 space-y-8">
    <!-- Barra de Búsqueda y Filtros -->
    <section class="bg-primary-dark rounded-2xl border border-white/10 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-1.5 ml-1">Buscar
                    Materia</label>
                <div class="relative">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gold/60 text-lg">search</span>
                    <input id="search-input"
                        class="w-full bg-black/20 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:ring-1 focus:ring-gold/50 outline-none transition-all placeholder:text-white/20"
                        placeholder="Buscar por código o nombre..." type="text" />
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-1.5 ml-1">Filtrar
                    por Año</label>
                <select id="filter-year"
                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2 text-sm text-white focus:ring-1 focus:ring-gold/50 outline-none transition-all">
                    <option value="">Todos los años</option>
                    <option value="1">Primer Año</option>
                    <option value="2">Segundo Año</option>
                    <option value="3">Tercer Año</option>
                </select>
            </div>
            <div>
                <label
                    class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-1.5 ml-1">&nbsp;</label>
                <button id="btn-clear-filters"
                    class="w-full bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl px-4 py-2 text-sm text-white/60 font-bold uppercase tracking-wider transition-all">
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </section>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-white uppercase tracking-tight italic">GESTIÓN DE <span
                    class="text-gold">MATERIAS</span></h1>
            <p class="text-white/40 text-sm font-medium mt-1 uppercase tracking-widest">Administración del pensum
                académico</p>
        </div>
        <button id="btn-nueva-materia"
            class="bg-gold hover:bg-white text-primary-dark font-black px-6 py-2.5 rounded-xl flex items-center gap-2 transition-all uppercase text-xs tracking-widest shadow-lg shadow-gold/10">
            <span class="material-symbols-outlined font-bold">add</span>
            Nueva Materia
        </button>
    </div>

    <!-- Tabs por Año -->
    <div class="flex gap-2 border-b border-white/10">
        <button class="tab-year active px-6 py-3 text-sm font-black uppercase tracking-wider transition-all"
            data-year="">
            Todas
        </button>
        <button class="tab-year px-6 py-3 text-sm font-black uppercase tracking-wider transition-all" data-year="1">
            Primer Año
        </button>
        <button class="tab-year px-6 py-3 text-sm font-black uppercase tracking-wider transition-all" data-year="2">
            Segundo Año
        </button>
        <button class="tab-year px-6 py-3 text-sm font-black uppercase tracking-wider transition-all" data-year="3">
            Tercer Año
        </button>
    </div>

    <!-- Grid de Materias -->
    <div id="subjects-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Loading -->
        <div class="col-span-full flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold"></div>
        </div>
    </div>

    <!-- Stats Footer -->
    <div class="bg-primary-dark rounded-2xl border border-white/10 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Total Materias</p>
                <h3 id="stat-total" class="text-3xl font-black text-white">0</h3>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Primer Año</p>
                <h3 id="stat-year-1" class="text-3xl font-black text-gold">0</h3>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Segundo Año</p>
                <h3 id="stat-year-2" class="text-3xl font-black text-gold">0</h3>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Tercer Año</p>
                <h3 id="stat-year-3" class="text-3xl font-black text-gold">0</h3>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Materia -->
<div id="modal-nueva-materia" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 hidden">
    <div
        class="bg-primary-dark rounded-2xl border border-white/10 p-8 max-w-2xl w-full mx-4 max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-white uppercase">Nueva Materia</h2>
            <button id="btn-close-modal" class="text-white/60 hover:text-gold transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="form-nueva-materia" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Código
                        *</label>
                    <input type="text" name="codigo" required
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                        placeholder="TEO-001">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Créditos</label>
                    <input type="number" name="creditos" min="1" max="10"
                        class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                        placeholder="3">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Nombre de la
                    Materia *</label>
                <input type="text" name="nombre" required
                    class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all"
                    placeholder="Teología I">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Año al que
                    Pertenece</label>
                <select name="año_materia"
                    class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all">
                    <option value="">Sin asignar</option>
                    <option value="1">Primer Año</option>
                    <option value="2">Segundo Año</option>
                    <option value="3">Tercer Año</option>
                </select>
            </div>

            <div>
                <label
                    class="block text-[10px] font-bold text-white/60 uppercase tracking-wider mb-2">Descripción</label>
                <textarea name="descripcion" rows="4"
                    class="w-full bg-black/20 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold/50 outline-none transition-all resize-none"
                    placeholder="Descripción de la materia..."></textarea>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit"
                    class="flex-1 bg-gold hover:bg-white text-primary-dark font-black px-6 py-3 rounded-xl uppercase text-xs tracking-widest transition-all">
                    <span class="flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        Crear Materia
                    </span>
                </button>
                <button type="button" id="btn-cancel-modal"
                    class="flex-1 bg-white/10 hover:bg-white/20 text-white font-black px-6 py-3 rounded-xl uppercase text-xs tracking-widest transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .tab-year {
        color: rgba(255, 255, 255, 0.4);
        border-bottom: 2px solid transparent;
    }

    .tab-year.active {
        color: #D4AF37;
        border-bottom-color: #D4AF37;
    }

    .tab-year:hover:not(.active) {
        color: rgba(255, 255, 255, 0.7);
    }
</style>

<script>
    (function () {
        let allSubjects = [];
        let filteredSubjects = [];
        let currentYear = '';
        let searchQuery = '';

        // Inicializar
        async function init() {
            await loadSubjects();
        }

        // Cargar materias
        async function loadSubjects() {
            try {
                const response = await fetch('actions/get_subjects.php');
                const data = await response.json();
                if (data.success) {
                    allSubjects = data.subjects;
                    updateStats(data.subjects_by_year);
                    filterAndRender();
                }
            } catch (error) {
                console.error('Error al cargar materias:', error);
                showError('Error al cargar materias');
            }
        }

        // Actualizar estadísticas
        function updateStats(subjectsByYear) {
            document.getElementById('stat-total').textContent = allSubjects.length;
            document.getElementById('stat-year-1').textContent = subjectsByYear[1]?.length || 0;
            document.getElementById('stat-year-2').textContent = subjectsByYear[2]?.length || 0;
            document.getElementById('stat-year-3').textContent = subjectsByYear[3]?.length || 0;
        }

        // Filtrar y renderizar
        function filterAndRender() {
            filteredSubjects = allSubjects.filter(subject => {
                const matchesYear = !currentYear || subject.año_materia == currentYear;
                const matchesSearch = !searchQuery ||
                    subject.codigo.toLowerCase().includes(searchQuery.toLowerCase()) ||
                    subject.nombre.toLowerCase().includes(searchQuery.toLowerCase());
                return matchesYear && matchesSearch;
            });
            renderSubjects();
        }

        // Renderizar materias
        function renderSubjects() {
            const grid = document.getElementById('subjects-grid');

            if (filteredSubjects.length === 0) {
                grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-symbols-outlined text-6xl text-white/20">school</span>
                    <p class="text-white/40 mt-4">No se encontraron materias</p>
                </div>
            `;
                return;
            }

            grid.innerHTML = filteredSubjects.map(subject => `
            <div class="bg-primary-dark rounded-2xl border border-white/10 p-6 hover:border-gold/30 transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <span class="text-[10px] font-bold text-gold uppercase tracking-wider">${subject.codigo}</span>
                        ${subject.año_materia ? `<span class="ml-2 text-[9px] font-bold text-white/40 uppercase">${subject.año_materia}° Año</span>` : ''}
                    </div>
                    <button onclick="deleteSubject(${subject.id})" class="text-white/40 hover:text-red-400 transition-opacity opacity-0 group-hover:opacity-100">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
                <h3 class="text-lg font-black text-white mb-2">${subject.nombre}</h3>
                ${subject.creditos ? `<p class="text-xs text-white/60 mb-2"><span class="font-bold">${subject.creditos}</span> créditos</p>` : ''}
                ${subject.descripcion ? `<p class="text-xs text-white/40 line-clamp-2">${subject.descripcion}</p>` : ''}
            </div>
        `).join('');
        }

        // Mostrar error
        function showError(message) {
            const grid = document.getElementById('subjects-grid');
            grid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <p class="text-red-400">${message}</p>
            </div>
        `;
        }

        // Guardar nueva materia
        async function saveSubject(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = {
                codigo: formData.get('codigo'),
                nombre: formData.get('nombre'),
                año_materia: formData.get('año_materia') || null,
                creditos: formData.get('creditos') || null,
                descripcion: formData.get('descripcion')
            };

            try {
                const response = await fetch('actions/create_subject.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.success) {
                    alert('¡Materia creada exitosamente!');
                    document.getElementById('modal-nueva-materia').classList.add('hidden');
                    await loadSubjects();
                } else {
                    alert(result.error || 'Error al crear materia');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al crear materia');
            }
        }

        // Eliminar materia
        window.deleteSubject = async function (id) {
            if (!confirm('¿Estás seguro de eliminar esta materia?')) return;

            try {
                const response = await fetch('actions/delete_subject.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const result = await response.json();
                if (result.success) {
                    alert('Materia eliminada');
                    await loadSubjects();
                } else {
                    alert(result.error || 'Error al eliminar');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al eliminar materia');
            }
        };

        // Event Listeners
        document.getElementById('btn-nueva-materia').addEventListener('click', () => {
            document.getElementById('form-nueva-materia').reset();
            document.getElementById('modal-nueva-materia').classList.remove('hidden');
        });

        document.getElementById('btn-close-modal').addEventListener('click', () => {
            document.getElementById('modal-nueva-materia').classList.add('hidden');
        });

        document.getElementById('btn-cancel-modal').addEventListener('click', () => {
            document.getElementById('modal-nueva-materia').classList.add('hidden');
        });

        document.getElementById('form-nueva-materia').addEventListener('submit', saveSubject);

        document.getElementById('search-input').addEventListener('input', (e) => {
            searchQuery = e.target.value;
            filterAndRender();
        });

        document.getElementById('filter-year').addEventListener('change', (e) => {
            currentYear = e.target.value;
            filterAndRender();
        });

        document.getElementById('btn-clear-filters').addEventListener('click', () => {
            searchQuery = '';
            currentYear = '';
            document.getElementById('search-input').value = '';
            document.getElementById('filter-year').value = '';
            filterAndRender();
        });

        // Tabs por año
        document.querySelectorAll('.tab-year').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab-year').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                currentYear = tab.dataset.year;
                filterAndRender();
            });
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('modal-nueva-materia').addEventListener('click', (e) => {
            if (e.target.id === 'modal-nueva-materia') {
                document.getElementById('modal-nueva-materia').classList.add('hidden');
            }
        });

        // Inicializar
        init();
    })();
</script>
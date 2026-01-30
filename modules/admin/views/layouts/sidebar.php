<!-- Barra lateral -->
<aside class="w-64 flex flex-col bg-primary-dark border-r border-white/10 shrink-0 h-screen z-50">
    <div class="flex flex-col h-full p-4">
        <div class="flex items-center gap-3 px-2 py-4 mb-6">
            <div class="size-12 flex items-center justify-center bg-white p-1 rounded-lg">
                <img alt="CBUH Logo" class="w-full h-full object-contain"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBHVLAyLTTR1rQPXa69XeJdd81b6rmbSqANb4Dq-sRa8xkJg2dkPUkjxCQiJmjbn8LUuEwQFHCBdRwv04OHJkSyJ4RkzzALkYycqtnszTV311EK_mQfUs4d6aYqUHfA-IsxdbBCcrQc10eFhcZcEenkHuMqaXblP2ISWz2yKnZznTtAHLi1FQhMLmbvDSeoHIZ-3HPJFYU4_1dGjWJOxkf3PEEfUE_1YNjLlbQY_zMWYn8MciyaQGy56rErY9tDtbcPzFY1lJ6b_-8" />
            </div>
            <div>
                <h1 class="text-white font-bold text-lg leading-tight uppercase tracking-tight">CBUH</h1>
                <p class="text-gold text-[10px] font-bold uppercase">Control de Estudio</p>
            </div>
        </div>
        <nav class="flex-1 space-y-1">
            <button onclick="switchTab('dashboard')" id="nav-dashboard"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg bg-gold text-primary-dark font-bold section-link transition-colors text-left">
                <span class="material-symbols-outlined">dashboard</span>
                <p class="text-sm uppercase tracking-tight">Dashboard</p>
            </button>
            <button onclick="switchTab('directorio')" id="nav-directorio"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/5 hover:text-white font-medium section-link transition-colors text-left">
                <span class="material-symbols-outlined">group</span>
                <p class="text-sm leading-normal">Directorio Estudiantil</p>
            </button>
            <button onclick="switchTab('inscripciones')" id="nav-inscripciones"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/5 hover:text-white font-medium section-link transition-colors text-left">
                <span class="material-symbols-outlined">person_add</span>
                <p class="text-sm leading-normal">Inscripciones</p>
            </button>
            <button onclick="switchTab('calificaciones')" id="nav-calificaciones"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/5 hover:text-white font-medium section-link transition-colors text-left">
                <span class="material-symbols-outlined">grade</span>
                <p class="text-sm leading-normal">Calificaciones</p>
            </button>
            <button onclick="switchTab('horarios')" id="nav-horarios"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/5 hover:text-white font-medium section-link transition-colors text-left">
                <span class="material-symbols-outlined">calendar_month</span>
                <p class="text-sm leading-normal">Horarios</p>
            </button>
            <button onclick="switchTab('profesores')" id="nav-profesores"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/5 hover:text-white font-medium section-link transition-colors text-left">
                <span class="material-symbols-outlined">co_present</span>
                <p class="text-sm leading-normal">Vista Profesor</p>
            </button>
            <button onclick="switchTab('materias')" id="nav-materias"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/5 hover:text-white font-medium section-link transition-colors text-left">
                <span class="material-symbols-outlined">school</span>
                <p class="text-sm leading-normal">Materias</p>
            </button>
            <button onclick="switchTab('reportes')" id="nav-reportes"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-white/60 hover:bg-white/5 hover:text-white font-medium section-link transition-colors text-left">
                <span class="material-symbols-outlined">description</span>
                <p class="text-sm leading-normal">Reportes</p>
            </button>
        </nav>
        <div class="pt-4 border-t border-white/10">
            <button
                class="flex w-full items-center gap-3 px-3 py-2 text-white/60 hover:bg-white/5 rounded-lg mb-4 text-left transition-colors">
                <span class="material-symbols-outlined">settings</span>
                <span class="text-sm font-medium">Configuración</span>
            </button>
            <a href="../auth/logout.php"
                class="flex w-full items-center gap-3 px-3 py-2 text-red-400 hover:bg-red-400/10 rounded-lg text-left transition-colors">
                <span class="material-symbols-outlined text-red-400">logout</span>
                <span class="text-sm font-medium uppercase tracking-wider">Cerrar Sesión</span>
            </a>
        </div>
    </div>
</aside>
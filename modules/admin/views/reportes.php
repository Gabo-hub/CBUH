<div class="p-8 space-y-8 flex-1 overflow-y-auto custom-scrollbar">

    <!-- Estadísticas Superiores -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Tarjeta Estadística 1 -->
        <div
            class="bg-primary-dark p-6 rounded-2xl border border-white/10 relative overflow-hidden group hover:border-gold/30 transition-all">
            <div class="absolute -right-6 -top-6 bg-gold/5 size-32 rounded-full group-hover:bg-gold/10 transition-all">
            </div>
            <p class="text-[10px] font-black text-white/40 uppercase tracking-widest relative z-10">Matrícula Total</p>
            <h3 class="text-4xl font-black text-white mt-2 relative z-10">1,240</h3>
            <div class="flex items-center gap-2 mt-2 relative z-10">
                <span
                    class="bg-green-500/20 text-green-500 text-[9px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">+12%</span>
                <p class="text-[9px] font-bold text-white/30 uppercase tracking-widest">vs. Periodo Ant.</p>
            </div>
        </div>
        <!-- Tarjeta Estadística 2 -->
        <div
            class="bg-primary-dark p-6 rounded-2xl border border-white/10 relative overflow-hidden group hover:border-gold/30 transition-all">
            <div
                class="absolute -right-6 -top-6 bg-blue-500/5 size-32 rounded-full group-hover:bg-blue-500/10 transition-all">
            </div>
            <p class="text-[10px] font-black text-white/40 uppercase tracking-widest relative z-10">Promedio General</p>
            <h3 class="text-4xl font-black text-white mt-2 relative z-10">16.4</h3>
            <div class="flex items-center gap-2 mt-2 relative z-10">
                <span
                    class="bg-green-500/20 text-green-500 text-[9px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">+0.8</span>
                <p class="text-[9px] font-bold text-white/30 uppercase tracking-widest">Puntos</p>
            </div>
        </div>
        <!-- Tarjeta Estadística 3 -->
        <div
            class="bg-primary-dark p-6 rounded-2xl border border-white/10 relative overflow-hidden group hover:border-gold/30 transition-all">
            <div
                class="absolute -right-6 -top-6 bg-red-500/5 size-32 rounded-full group-hover:bg-red-500/10 transition-all">
            </div>
            <p class="text-[10px] font-black text-white/40 uppercase tracking-widest relative z-10">Índice de Retención
            </p>
            <h3 class="text-4xl font-black text-white mt-2 relative z-10">94%</h3>
            <div class="flex items-center gap-2 mt-2 relative z-10">
                <span
                    class="bg-red-500/20 text-red-500 text-[9px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">-2%</span>
                <p class="text-[9px] font-bold text-white/30 uppercase tracking-widest">Alerta Baja</p>
            </div>
        </div>
        <!-- Tarjeta Estadística 4 -->
        <div
            class="bg-primary-dark p-6 rounded-2xl border border-white/10 relative overflow-hidden group hover:border-gold/30 transition-all">
            <div
                class="absolute -right-6 -top-6 bg-purple-500/5 size-32 rounded-full group-hover:bg-purple-500/10 transition-all">
            </div>
            <p class="text-[10px] font-black text-white/40 uppercase tracking-widest relative z-10">Ingresos (Mes)</p>
            <h3 class="text-4xl font-black text-white mt-2 relative z-10">$12k</h3>
            <div class="flex items-center gap-2 mt-2 relative z-10">
                <span
                    class="bg-white/10 text-white text-[9px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">Estable</span>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4 border-r border-white/10 pr-6">
                <div class="text-right">
                    <p class="text-xs text-white/40 uppercase tracking-widest text-[10px]">Última Actualización</p>
                    <p class="text-sm font-bold text-gold">Hoy, 09:30 AM</p>
                </div>
            </div>
            <button
                class="px-4 py-2 bg-gold text-primary-dark rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-white hover:scale-105 transition-all shadow-lg shadow-gold/10">
                Descargar Todo
            </button>
        </div>
    </div>

    <!-- Sección de Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Gráfico Principal -->
        <div class="lg:col-span-2 bg-primary-dark p-8 rounded-3xl border border-white/10 flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-tight">Tendencia de Inscripción</h3>
                    <p class="text-xs text-white/40">Comparativa 2023 vs 2024</p>
                </div>
                <div class="flex bg-black/30 p-1 rounded-lg">
                    <button
                        class="px-3 py-1 rounded-md bg-white/10 text-white text-[10px] font-bold uppercase tracking-wider">Mensual</button>
                    <button
                        class="px-3 py-1 rounded-md text-white/40 hover:text-white text-[10px] font-bold uppercase tracking-wider">Semestral</button>
                </div>
            </div>
            <!-- Área de Gráfico de Líneas Simulado -->
            <div class="flex-1 w-full h-64 relative border-b border-l border-white/10">
                <!-- Líneas de Cuadrícula -->
                <div class="absolute inset-x-0 bottom-1/4 h-px bg-white/5"></div>
                <div class="absolute inset-x-0 bottom-2/4 h-px bg-white/5"></div>
                <div class="absolute inset-x-0 bottom-3/4 h-px bg-white/5"></div>

                <!-- SVGs de Líneas (Maqueta) -->
                <svg class="absolute inset-0 w-full h-full overflow-visible" preserveAspectRatio="none">
                    <!-- Line 1 -->
                    <path d="M0 80 Q 50 120, 100 60 T 200 80 T 300 150 T 400 40 T 500 100 T 600 20 L 600 256 L 0 256 Z"
                        fill="url(#gradientGold)" stroke="none" opacity="0.1"></path>
                    <path d="M0 80 Q 50 120, 100 60 T 200 80 T 300 150 T 400 40 T 500 100 T 600 20" fill="none"
                        stroke="#c5a059" stroke-width="2"></path>
                    <defs>
                        <linearGradient id="gradientGold" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#c5a059" />
                            <stop offset="100%" stop-color="transparent" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>

        <!-- Gráfico Circular -->
        <div
            class="bg-primary-dark p-8 rounded-3xl border border-white/10 flex flex-col items-center justify-center relative">
            <h3 class="absolute top-8 left-8 text-sm font-black text-white uppercase tracking-widest">Distribución por
                Año</h3>
            <div class="relative size-64 flex items-center justify-center">
                <!-- Maqueta de gráfico de rosquilla usando gradiente cónico -->
                <div class="size-full rounded-full"
                    style="background: conic-gradient(#c5a059 0% 35%, #4a0d1e 35% 60%, #333 60% 100%);"></div>
                <div class="absolute bg-primary-dark size-48 rounded-full flex flex-col items-center justify-center">
                    <span class="text-3xl font-black text-white">100%</span>
                </div>
            </div>
            <div class="w-full mt-6 space-y-2">
                <div class="flex justify-between items-center text-xs">
                    <span class="flex items-center gap-2 text-white/60 font-bold uppercase tracking-wider"><span
                            class="size-2 rounded-full bg-gold"></span> 1º Año</span>
                    <span class="text-white font-bold">35%</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="flex items-center gap-2 text-white/60 font-bold uppercase tracking-wider"><span
                            class="size-2 rounded-full bg-primary"></span> 2º Año</span>
                    <span class="text-white font-bold">25%</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="flex items-center gap-2 text-white/60 font-bold uppercase tracking-wider"><span
                            class="size-2 rounded-full bg-gray-800"></span> 3º Año</span>
                    <span class="text-white font-bold">40%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuadrícula de Reportes Descargables -->
    <div>
        <h3 class="text-sm font-black text-white uppercase tracking-widest mb-4">Generar Reporte Específico</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button
                class="group p-4 bg-card-dark border border-white/5 hover:border-gold rounded-xl flex items-center justify-between transition-all text-left">
                <div>
                    <p
                        class="text-[10px] text-white/40 font-bold uppercase tracking-widest mb-1 group-hover:text-gold transition-colors">
                        Académico</p>
                    <h4 class="text-white font-bold text-sm">Resumen de Notas</h4>
                </div>
                <span
                    class="material-symbols-outlined text-white/20 group-hover:text-gold transition-colors">download</span>
            </button>
            <button
                class="group p-4 bg-card-dark border border-white/5 hover:border-gold rounded-xl flex items-center justify-between transition-all text-left">
                <div>
                    <p
                        class="text-[10px] text-white/40 font-bold uppercase tracking-widest mb-1 group-hover:text-gold transition-colors">
                        Administrativo</p>
                    <h4 class="text-white font-bold text-sm">Listado Inscritos</h4>
                </div>
                <span
                    class="material-symbols-outlined text-white/20 group-hover:text-gold transition-colors">file_download</span>
            </button>
            <button
                class="group p-4 bg-card-dark border border-white/5 hover:border-gold rounded-xl flex items-center justify-between transition-all text-left">
                <div>
                    <p
                        class="text-[10px] text-white/40 font-bold uppercase tracking-widest mb-1 group-hover:text-gold transition-colors">
                        Docencia</p>
                    <h4 class="text-white font-bold text-sm">Carga Horaria</h4>
                </div>
                <span
                    class="material-symbols-outlined text-white/20 group-hover:text-gold transition-colors">picture_as_pdf</span>
            </button>
        </div>
    </div>
</div>
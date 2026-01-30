<div class="flex-1 flex overflow-hidden h-full">
    <div class="flex-1 overflow-y-auto custom-scrollbar p-8">
        <!-- Sección de Búsqueda -->
        <section class="bg-primary-dark rounded-2xl border border-white/10 p-6 mb-6">
            <div class="md:col-span-4">
                <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-1.5 ml-1">Buscar
                    Estudiante</label>
                <div class="relative">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gold/60 text-lg">search</span>
                    <input
                        class="w-full bg-black/20 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:ring-1 focus:ring-gold/50 outline-none transition-all placeholder:text-white/20"
                        placeholder="Buscar estudiante por nombre o cédula..." type="text" />
                </div>
            </div>
        </section>
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-black text-white uppercase tracking-tight italic">Panel de <span
                        class="text-gold">Calificaciones</span></h2>
                <p class="text-white/40 text-sm">Resumen general del rendimiento estudiantil por cohorte.</p>
            </div>
            <div class="flex gap-3">
                <button
                    class="px-4 py-2 bg-primary-dark border border-white/10 rounded-lg text-xs font-bold text-gold uppercase tracking-widest hover:bg-white/5 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">download</span> Exportar PDF
                </button>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-primary-dark rounded-2xl border border-white/10 overflow-hidden shadow-2xl">
            <table class="w-full text-left">
                <thead class="bg-black/40 text-white/40 text-[10px] uppercase tracking-[0.2em] sticky top-0">
                    <tr>
                        <th class="px-6 py-5 font-black">Estudiante / Cédula</th>
                        <th class="px-6 py-5 font-black text-center">Promedio Gral.</th>
                        <th class="px-6 py-5 font-black text-center">Asistencia %</th>
                        <th class="px-6 py-5 font-black">Estatus</th>
                        <th class="px-6 py-5 font-black text-right">Detalles</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <!-- Filas de ejemplo -->
                    <tr class="student-row transition-all cursor-pointer group bg-white/[0.02]">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div
                                    class="size-10 rounded-full border border-gold/30 p-0.5 overflow-hidden bg-card-dark">
                                    <span
                                        class="flex items-center justify-center w-full h-full font-bold text-gold text-xs">AM</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white group-hover:text-gold transition-colors">Ana
                                        María Martinez Briceño</p>
                                    <p class="text-[10px] font-bold text-white/40 tracking-wider">V-27.123.456</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-lg font-black text-gold">18.5</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-sm font-bold text-white">92%</span>
                                <div class="w-16 h-1 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500" style="width: 92%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span
                                class="px-2.5 py-1 rounded-full bg-green-500/10 text-green-400 text-[10px] font-black uppercase tracking-widest border border-green-500/20">Regular</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <span
                                class="material-symbols-outlined text-white/20 group-hover:text-gold transition-all">chevron_right</span>
                        </td>
                    </tr>
                    <!-- Agregar más filas según bucle PHP -->
                </tbody>
            </table>
            <div
                class="p-4 bg-black/20 border-t border-white/5 flex items-center justify-between text-[10px] font-bold text-white/40 uppercase tracking-widest">
                <span>Mostrando estudiantes</span>
                <div class="flex gap-2">
                    <button
                        class="px-3 py-1 bg-card-dark border border-white/10 rounded hover:border-gold/50 transition-all">Anterior</button>
                    <button
                        class="px-3 py-1 bg-card-dark border border-white/10 rounded hover:border-gold/50 transition-all">Siguiente</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra lateral derecha para detalles -->
    <aside class="w-[350px] bg-primary-dark border-l border-white/10 flex flex-col overflow-hidden details-panel">
        <div class="p-6 border-b border-white/5 bg-black/10">
            <div class="flex justify-between items-start mb-6">
                <h3 class="text-sm font-black text-gold uppercase tracking-widest">Récord Académico Detallado</h3>
                <button onclick="document.querySelector('.details-panel').classList.add('hidden')"
                    class="p-1 hover:bg-white/5 rounded-full text-white/40"><span
                        class="material-symbols-outlined">close</span></button>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="size-16 rounded-2xl border-2 border-gold p-0.5">
                    <div
                        class="w-full h-full rounded-[14px] bg-card-dark flex items-center justify-center text-gold font-bold">
                        AM</div>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-white leading-tight">Ana María Martinez</h4>
                    <p class="text-xs font-bold text-gold/80 tracking-widest">C.I: V-27.123.456</p>
                    <p class="text-[10px] font-bold text-white/40 uppercase mt-1">4to Semestre</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="bg-card-dark p-3 rounded-xl border border-white/5">
                    <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-1">Índice Académico</p>
                    <p class="text-xl font-black text-gold">18.52 <span
                            class="text-[10px] text-green-400 font-bold">▲</span></p>
                </div>
                <div class="bg-card-dark p-3 rounded-xl border border-white/5">
                    <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-1">Asistencia Global</p>
                    <p class="text-xl font-black text-white">92.4%</p>
                </div>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h5 class="text-[10px] font-black text-white/60 uppercase tracking-widest">Materias en Curso</h5>
                <span class="text-[10px] font-bold text-gold">2024-II</span>
            </div>
            <!-- Ítems de materia -->
            <div class="bg-card-dark rounded-xl border border-white/5 p-4 space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-black text-white uppercase leading-none">Matemáticas III</p>
                        <p class="text-[10px] text-white/40 mt-1">Prof. Roberto Jimenez</p>
                    </div>
                    <span class="text-lg font-black text-gold">19</span>
                </div>
                <div class="flex items-center justify-between text-[10px] font-bold">
                    <div class="flex items-center gap-2">
                        <span class="text-white/40 uppercase tracking-widest">Asistencia:</span>
                        <span class="text-green-400">98%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-black/20 border-t border-white/5 space-y-2">
            <button
                class="w-full py-3 bg-gold text-primary-dark font-black text-[10px] uppercase tracking-widest rounded-lg hover:bg-white transition-all">Modificar
                Calificaciones</button>
            <button
                class="w-full py-3 bg-transparent text-white/60 font-black text-[10px] uppercase tracking-widest rounded-lg hover:text-white transition-all">Generar
                Constancia Notas</button>
        </div>
    </aside>
</div>
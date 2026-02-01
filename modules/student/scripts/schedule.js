import { supabase } from '../../../config/supabase-client.js'

let allSchedules = [];
let isInitialized = false;

// Configuración de tiempo (Default)
// Se podría cargar desde la base de datos igual que en docencia
let configStartStr = '07:00';
let configEndStr = '18:00'; // Estudiantes pueden tener clases hasta tarde
let configStartMinutes = 420; // 7 * 60
let configEndMinutes = 1080; // 18 * 60

export async function initSchedule() {
    if (isInitialized) return; // Prevent double init if called multiple times unless filtered
    console.log('[StudentSchedule] Initializing...');

    // Load schedule data
    await loadStudentSchedules();

    // Render grid base
    renderTimeGrid();

    isInitialized = true;
}

// Attach to window
window.initSchedule = initSchedule;

function renderTimeGrid() {
    const timeCol = document.getElementById('student-grid-time-column');
    if (!timeCol) return;

    const startHour = Math.floor(configStartMinutes / 60);
    const endHour = Math.floor(configEndMinutes / 60);

    let html = '';
    for (let h = startHour; h <= endHour; h++) {
        const timeLabel = `${h.toString().padStart(2, '0')}:00`;
        // Each hour block height needs to match the pxPerMin math in createScheduleBlock.
        // If 1 hr = 60 min. pxPerMin = 96/60 = 1.6. Height = 96px.
        html += `
             <div class="h-24 border-b border-white/5 flex items-start justify-center pt-2 text-[9px] font-bold text-white/40 font-mono tracking-widest">
                ${timeLabel}
            </div>
        `;
    }
    timeCol.innerHTML = html;
}

async function loadStudentSchedules() {
    const loading = document.getElementById('schedule-loading');
    const context = window.studentContext;

    if (!context || !context.estudiante) {
        if (loading) loading.classList.add('hidden');
        return;
    }

    try {
        // 1. Get enrolled loads (Only the active one for the current sequence)
        const { data: enrollments, error: enrollError } = await supabase
            .from('inscripciones')
            .select(`
                carga_academica_id,
                estado_id,
                carga:cargas_academicas!carga_academica_id (
                    materia:materias!materia_id (orden_secuencia, año_materia)
                )
            `)
            .eq('estudiante_id', context.estudiante.id)
            .eq('estado_id', 1);

        if (enrollError) throw enrollError;

        if (!enrollments || enrollments.length === 0) {
            if (loading) loading.classList.add('hidden');
            return;
        }

        // Sequential Logic: If multiple are active (shouldn't happen with new rule, but safety first),
        // pick the one with lowest sequence from the current year.
        const sortedEnrollments = enrollments.sort((a, b) => {
            const seqA = a.carga?.materia?.orden_secuencia || 0;
            const seqB = b.carga?.materia?.orden_secuencia || 0;
            return seqA - seqB;
        });

        const activeEnrollment = sortedEnrollments[0];
        const loadIds = [activeEnrollment.carga_academica_id];

        // 2. Get schedules for these loads
        const { data: schedules, error: schedError } = await supabase
            .from('horarios')
            .select(`
                id,
                dia_semana,
                hora_inicio,
                hora_fin,
                aula,
                carga:cargas_academicas (
                    id,
                    materia:materias (id, nombre, codigo, año_materia),
                    docente:docentes (nombres, apellidos)
                )
            `)
            .in('carga_academica_id', loadIds);

        if (schedError) throw schedError;

        allSchedules = schedules || [];
        renderCalendar(allSchedules);

    } catch (e) {
        console.error('[StudentSchedule] Error:', e);
    } finally {
        if (loading) loading.classList.add('hidden');
    }
}

function renderCalendar(schedules) {
    // Clear columns
    for (let i = 1; i <= 6; i++) {
        const col = document.getElementById(`student-schedule-day-${i}`);
        if (col) col.innerHTML = '';
    }

    schedules.forEach(schedule => {
        const col = document.getElementById(`student-schedule-day-${schedule.dia_semana}`);
        if (!col) return;

        const block = createScheduleBlock(schedule);
        col.appendChild(block);
    });
}

function createScheduleBlock(schedule) {
    const div = document.createElement('div');
    const startMinutes = timeToMinutes(schedule.hora_inicio);
    const endMinutes = timeToMinutes(schedule.hora_fin);
    const durationCurrent = endMinutes - startMinutes;

    const pxPerMin = 96 / 60; // 96px per hour (h-24 class = 6rem = 96px)
    const baseStart = configStartMinutes;
    const top = (startMinutes - baseStart) * pxPerMin;
    const height = durationCurrent * pxPerMin;

    // Styling
    div.className = `absolute left-1 right-1 rounded-xl p-3 transition-all hover:z-50 hover:scale-[1.02] cursor-pointer shadow-lg group overflow-hidden border-l-4`;

    const year = schedule.carga?.materia?.año_materia || 1;
    // Dynamic Colors based on Year
    const colors = {
        1: 'bg-blue-500/20 border-blue-500 hover:bg-blue-500/30',
        2: 'bg-purple-500/20 border-purple-500 hover:bg-purple-500/30',
        3: 'bg-gold/20 border-gold hover:bg-gold/30',
        4: 'bg-emerald-500/20 border-emerald-500 hover:bg-emerald-500/30',
        5: 'bg-red-500/20 border-red-500 hover:bg-red-500/30'
    };

    div.classList.add(...(colors[year] || 'bg-white/10 border-white/50').split(' '));

    div.style.top = `${top}px`;
    div.style.height = `${height}px`;

    const docenteName = schedule.carga?.docente ?
        `${schedule.carga.docente.nombres.split(' ')[0]} ${schedule.carga.docente.apellidos.split(' ')[0]}` : 'Sin Asignar';

    div.innerHTML = `
        <div class="flex flex-col h-full justify-between relative z-10">
            <div>
                <p class="text-[10px] font-black text-white uppercase tracking-wider leading-tight line-clamp-2">
                    ${schedule.carga?.materia?.nombre || 'Materia'}
                </p>
                <p class="text-[9px] font-bold text-white/50 mt-1">${schedule.carga?.materia?.codigo}</p>
            </div>
            
            <div class="space-y-1">
                 <div class="flex items-center gap-1 text-[9px] font-bold text-white/80 bg-black/40 w-fit px-1.5 py-0.5 rounded">
                    <span class="material-symbols-outlined text-[10px]">location_on</span>
                    ${schedule.aula || '?'}
                </div>
                 <div class="flex items-center gap-1 text-[9px] font-bold text-white/60">
                    <span class="material-symbols-outlined text-[10px]">person</span>
                    ${docenteName}
                </div>
            </div>
        </div>
        <!-- Decorative Glow -->
        <div class="absolute -right-4 -bottom-4 size-16 bg-white/5 blur-xl rounded-full group-hover:bg-white/10 transition-colors"></div>
    `;

    return div;
}

function timeToMinutes(timeStr) {
    if (!timeStr) return 0;
    const [hours, minutes] = timeStr.split(':').map(Number);
    return hours * 60 + minutes;
}

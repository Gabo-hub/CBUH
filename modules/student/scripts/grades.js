import { supabase } from '../../../config/supabase-client.js';

export async function initGrades() {
    console.log('[Grades] Initializing...');
    const container = document.getElementById('grades-container');
    if (!container) return;

    // Get context
    const context = window.studentContext;
    if (!context || !context.estudiante) {
        container.innerHTML = '<p class="text-center text-white/40">No se pudo cargar la información del estudiante.</p>';
        return;
    }

    try {
        // 1. Fetch academic history (grades)
        // We select qualifications joined with inscriptions, loads and subjects.
        // We need all records to build the full history.
        const { data: grades, error } = await supabase
            .from('calificaciones')
            .select(`
                id,
                nota_corte,
                nota_final,
                inscripcion:inscripciones!inscripcion_id!inner (
                    id,
                    estudiante_id,
                    carga:cargas_academicas!carga_academica_id (
                        id,
                        periodo:periodos_academicos!periodo_id (nombre),
                        materia:materias!materia_id (
                            id,
                            codigo,
                            nombre,
                            año_materia,
                            creditos,
                            descripcion
                        )
                    )
                )
            `)
            .eq('inscripciones.estudiante_id', context.estudiante.id)
            .order('id', { ascending: false }); // Latest first

        if (error) throw error;

        if (!grades || grades.length === 0) {
            container.innerHTML = `
                <div class="text-center py-20 opacity-50">
                    <span class="material-symbols-outlined text-6xl text-white/20 mb-4">school</span>
                    <h3 class="text-xl font-bold text-white uppercase">Sin Historial Académico</h3>
                    <p class="text-sm text-white/40 mt-2">Aún no tienes notas registradas.</p>
                </div>
            `;
            return;
        }

        // 2. Process Data: Group by Year (año_materia)
        // Structure: yearGroups = { 1: [subjects...], 2: [subjects...] }
        const yearGroups = {};

        // Also calculate stats
        let totalSubjectsLength = 0;
        let totalApproved = 0;
        let sumGrades = 0;
        let countGrades = 0;
        let totalCredits = 0;

        grades.forEach(record => {
            const materia = record.inscripcion?.carga?.materia;
            if (!materia) return;

            const year = materia.año_materia || 0;
            if (!yearGroups[year]) yearGroups[year] = [];

            // Simplified Logic: 1 cut (nota_corte) and Final Grade (nota_final)
            let displayGrade = record.nota_final;
            let isProvisional = false;

            if (displayGrade === null && record.nota_corte !== null) {
                displayGrade = record.nota_corte;
                isProvisional = true;
            }

            // Add record
            yearGroups[year].push({
                ...materia,
                nota_final: record.nota_final,
                nota_provisional: isProvisional ? displayGrade : null,
                periodo: record.inscripcion?.carga?.periodo?.nombre
            });

            // Stats Logic
            if (displayGrade !== null) {
                countGrades++;
                sumGrades += displayGrade;
                if (displayGrade >= 10) {
                    totalApproved++;
                    totalCredits += (materia.creditos || 0);
                }
            }
            totalSubjectsLength++;
        });

        // Update Header Stats
        const avg = countGrades > 0 ? (sumGrades / countGrades).toFixed(2) : '--';
        const avgEl = document.getElementById('grades-avg-all');
        const creditEl = document.getElementById('grades-credits');
        const totalSubEl = document.getElementById('grades-total-subjects');

        if (avgEl) avgEl.textContent = avg;
        if (creditEl) creditEl.textContent = totalCredits;
        if (totalSubEl) totalSubEl.textContent = totalSubjectsLength;


        // 3. Render HTML
        const years = Object.keys(yearGroups).sort();

        container.innerHTML = years.map(year => {
            const subjects = yearGroups[year];
            const yearLabel = getYearLabel(year);

            return `
                <div class="bg-primary-dark/50 rounded-3xl border border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-white/5 flex items-center gap-4 bg-black/10">
                         <div class="size-10 rounded-xl bg-gold/10 border border-gold/20 flex items-center justify-center text-gold font-bold text-lg">
                            ${year}º
                         </div>
                         <h3 class="text-lg font-black text-white uppercase tracking-tight">${yearLabel}</h3>
                    </div>
                    
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        ${subjects.map(sub => renderSubjectCard(sub)).join('')}
                    </div>
                </div>
            `;
        }).join('');

    } catch (e) {
        console.error('[Grades] Error:', e);
        container.innerHTML = `
            <div class="p-8 bg-red-500/10 border border-red-500/20 rounded-xl text-center">
                <p class="text-red-400 font-bold">Error cargando notas</p>
                <p class="text-xs text-white/40 mt-1">${e.message}</p>
            </div>
        `;
    }
}

function getYearLabel(year) {
    const labels = {
        1: 'Primer Año Académico',
        2: 'Segundo Año Académico',
        3: 'Tercer Año Académico',
        4: 'Cuarto Año Académico',
        5: 'Quinto Año Académico'
    };
    return labels[year] || `Año ${year}`;
}

function renderSubjectCard(subject) {
    const hasFinal = subject.nota_final !== null && subject.nota_final !== undefined;
    const hasProvisional = subject.nota_provisional !== null && subject.nota_provisional !== undefined;

    // Status Logic
    let statusText = 'Sin Nota';
    let statusColor = 'text-white/40';
    let statusBg = 'bg-white/5';
    let icon = 'pending';
    let gradeValue = '';

    if (hasFinal) {
        const isApproved = subject.nota_final >= 10;
        statusText = 'Final';
        statusColor = isApproved ? 'text-emerald-400' : 'text-red-400';
        statusBg = isApproved ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-red-500/10 border-red-500/20';
        icon = isApproved ? 'check_circle' : 'cancel';
        gradeValue = subject.nota_final + ' PTS';
    } else if (hasProvisional) {
        statusText = 'Progreso';
        statusColor = 'text-gold';
        statusBg = 'bg-gold/10 border-gold/20';
        icon = 'monitoring';
        gradeValue = subject.nota_provisional + ' PTS';
    }

    return `
        <div class="bg-card-dark p-5 rounded-2xl border border-white/5 hover:border-white/10 transition-all flex flex-col gap-4 group">
            <div class="flex justify-between items-start">
                <div>
                     <span class="text-[9px] font-black text-gold/60 uppercase tracking-widest border border-gold/10 px-2 py-0.5 rounded mb-2 inline-block">${subject.codigo}</span>
                     <h4 class="font-bold text-white leading-tight group-hover:text-gold transition-colors">${subject.nombre}</h4>
                </div>
                ${subject.creditos ? `<span class="text-[10px] font-bold text-white/20 bg-white/5 px-2 py-1 rounded">UC: ${subject.creditos}</span>` : ''}
            </div>
            
            <div class="mt-auto pt-4 border-t border-white/5 flex items-center justify-between">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider">${subject.periodo || 'Periodo ?'}</p>
                
                <div class="flex items-center gap-2 ${statusBg} px-3 py-1.5 rounded-lg border border-transparent">
                    <span class="material-symbols-outlined text-sm ${statusColor}">${icon}</span>
                    <div class="flex flex-col items-end leading-none">
                        <span class="text-[8px] font-black uppercase opacity-60 ${statusColor}">${statusText}</span>
                        <span class="font-black ${statusColor} text-sm">${gradeValue || '---'}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Attach to window for dynamic calls
window.initGrades = initGrades;

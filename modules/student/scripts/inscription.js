import { supabase } from '../../../config/supabase-client.js';

let currentStep = 1;
let eligibility = {
    status: false,
    docs: false,
    period: false,
    targetYear: 1,
    nextSubject: null,
    activePeriod: null
};

export async function initInscription() {
    console.log('[Inscription] Initializing module');

    // Reset wizard
    currentStep = 1;

    // Small delay to ensure DOM is ready (fix for race conditions with lazy load)
    let retryCount = 0;
    while (!document.getElementById('inscription-step-1') && retryCount < 10) {
        await new Promise(r => setTimeout(r, 50));
        retryCount++;
    }

    if (!document.getElementById('inscription-step-1')) {
        console.error('[Inscription] Required DOM elements for wizard not found');
        return;
    }

    updateWizardUI();

    // Check initial eligibility
    await checkEligibility();

    // Setup listeners
    setupListeners();
}

async function checkEligibility() {
    try {
        const student = window.studentContext.estudiante;
        const profile = window.studentContext.profile;

        if (!student) {
            showError('Perfil de estudiante no encontrado.');
            return;
        }

        // 1. Status Check
        const isStatusOk = student.estado_id === 1; // Assuming 1 = Active
        updateCheckUI('check-status', isStatusOk, isStatusOk ? 'Estudiante Activo' : 'Estatus No Válido');

        // 2. Docs Check
        const { data: docs, error: docError } = await supabase
            .from('documentos_estudiantes')
            .select('id')
            .eq('estudiante_id', student.id);

        // Assuming there are required docs (cedula, titulo, etc.). 
        // For simplicity: at least 2 docs uploaded. In a real app, check specific types.
        const isDocsOk = !docError && docs && docs.length >= 1;
        updateCheckUI('check-docs', isDocsOk, isDocsOk ? 'Expediente Completo' : 'Documentos Pendientes');

        // 3. Academic Context & Next Subject
        // Fetch student's inscriptions to calculate progress
        const { data: allIns, error: insError } = await supabase
            .from('inscripciones')
            .select(`
                id,
                estado_id,
                carga:cargas_academicas!carga_academica_id (
                    materia:materias!materia_id (año_materia, orden_secuencia, nombre)
                ),
                calificaciones (nota_final)
            `)
            .eq('estudiante_id', student.id);

        if (insError) throw insError;

        const activeEnrollment = allIns.find(i => i.estado_id === 1);
        const isAlreadyEnrolled = !!activeEnrollment;

        // Find boundary (highest year and sequence)
        let lastYear = 0;
        let lastSeq = 0;

        allIns.forEach(i => {
            const m = i.carga?.materia;
            // Check if passed (nota_final >= 10) or treat as passed if previously moved on?
            // Correct logic: Only consider "passed" subjects for sequence advancement
            // Fix O2O extraction again just in case
            let grade = null;
            if (Array.isArray(i.calificaciones) && i.calificaciones.length > 0) grade = i.calificaciones[0].nota_final;
            else if (i.calificaciones && i.calificaciones.nota_final) grade = i.calificaciones.nota_final;

            if (m && grade !== null && grade >= 10) {
                if (m.año_materia > lastYear) {
                    lastYear = m.año_materia;
                    lastSeq = m.orden_secuencia || 0;
                } else if (m.año_materia === lastYear && (m.orden_secuencia || 0) > lastSeq) {
                    lastSeq = m.orden_secuencia || 0;
                }
            }
        });

        let searchYear = lastYear || 1;
        let { data: nextSubjects, error: nextSubError } = await supabase
            .from('materias')
            .select('*')
            .eq('año_materia', searchYear)
            .gt('orden_secuencia', lastSeq)
            .order('orden_secuencia', { ascending: true })
            .limit(1);

        if (!nextSubError && (!nextSubjects || nextSubjects.length === 0)) {
            searchYear++;
            const { data: firstOfNextYear } = await supabase
                .from('materias')
                .select('*')
                .eq('año_materia', searchYear)
                .order('orden_secuencia', { ascending: true })
                .limit(1);
            nextSubjects = firstOfNextYear;
        }

        const nextSub = (!nextSubError && nextSubjects && nextSubjects.length > 0) ? nextSubjects[0] : null;
        eligibility.nextSubject = nextSub;
        eligibility.targetYear = nextSub ? nextSub.año_materia : (lastYear || 1);

        // Check for active academic period (Global, no longer by sede)
        const { data: period, error: perError } = await supabase
            .from('periodos_academicos')
            .select('*')
            .eq('estado_id', 1) // Assuming 1 = Active
            .maybeSingle();

        const now = new Date();
        let isPeriodOk = false;
        let periodMessage = 'Sin Periodo Activo';

        if (!perError && period) {
            const start = period.fecha_inicio_inscripcion ? new Date(period.fecha_inicio_inscripcion) : null;
            const end = period.fecha_fin_inscripcion ? new Date(period.fecha_fin_inscripcion) : null;

            if (start && end && now >= start && now <= end) {
                isPeriodOk = true;
                periodMessage = `Inscripción Abierta: ${period.nombre}`;
            } else if (start && now < start) {
                periodMessage = `Inscripción inicia el ${start.toLocaleDateString()}`;
            } else {
                periodMessage = 'Fuera del periodo de inscripción';
            }
        }

        eligibility.activePeriod = period;
        updateCheckUI('check-period', isPeriodOk, periodMessage);

        // Total Eligibility
        const isFullyEligible = isStatusOk && isDocsOk && isPeriodOk && nextSub && !isAlreadyEnrolled;
        eligibility.status = isStatusOk;
        eligibility.docs = isDocsOk;
        eligibility.period = isPeriodOk;

        const nextBtn = document.getElementById('btn-next-step-1');
        if (nextBtn) {
            nextBtn.disabled = !isFullyEligible;
            if (isFullyEligible) {
                nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        if (!isFullyEligible) {
            const msg = document.getElementById('eligibility-message');
            const errorText = document.getElementById('eligibility-error-text');
            if (msg && errorText) {
                msg.classList.remove('hidden');
                let err = 'No cumples con los requisitos para inscribir la siguiente materia.';
                if (isAlreadyEnrolled) err = `Ya estás cursando: ${activeEnrollment.carga.materia.nombre}.`;
                else if (!nextSub) err = `No hay más materias definidas para tu plan de estudios.`;
                else if (!isPeriodOk) err = periodMessage;
                else if (!isDocsOk) err = 'Debes subir tus documentos obligatorios en Configuración.';
                else if (!isStatusOk) err = 'Tu estatus actual no permite inscripciones.';
                errorText.textContent = err;
            }
        } else {
            const msg = document.getElementById('eligibility-message');
            if (msg) msg.classList.add('hidden');
        }

    } catch (e) {
        console.error('Eligibility check failed:', e);
    }
}

function updateCheckUI(id, success, message) {
    const el = document.getElementById(id);
    if (!el) return;

    el.innerHTML = `
        <span class="material-symbols-outlined text-sm ${success ? 'text-green-400' : 'text-red-400'}">
            ${success ? 'check_circle' : 'cancel'}
        </span>
        <span class="text-[10px] font-bold ${success ? 'text-white/80' : 'text-red-400/80'} uppercase">${message}</span>
    `;
}

function setupListeners() {
    const btn1 = document.getElementById('btn-next-step-1');
    if (btn1) btn1.onclick = () => goToStep(2);

    const btn2 = document.getElementById('btn-next-step-2');
    if (btn2) btn2.onclick = () => goToStep(3);

    const btnConfirm = document.getElementById('btn-confirm-final');
    if (btnConfirm) btnConfirm.onclick = handleFinalConfirmation;

    window.prevInscStep = (s) => goToStep(s);
}

function goToStep(step) {
    currentStep = step;
    updateWizardUI();

    if (step === 2) {
        renderSubjectSelection();
    }
    if (step === 3) {
        renderSummary();
    }
}

function updateWizardUI() {
    // Hide all steps
    const steps = ['inscription-step-1', 'inscription-step-2', 'inscription-step-3'];
    steps.forEach(s => {
        const el = document.getElementById(s);
        if (el) el.classList.add('hidden');
    });

    // Show current
    const current = document.getElementById(`inscription-step-${currentStep}`);
    if (current) current.classList.remove('hidden');

    // Update dots
    for (let i = 1; i <= 3; i++) {
        const dot = document.getElementById(`step-dot-${i}`);
        if (!dot) continue;

        if (i < currentStep) {
            dot.className = 'size-6 rounded-full border-2 border-green-500 bg-green-500 flex items-center justify-center text-[10px] font-black text-primary-dark';
            dot.innerHTML = '<span class="material-symbols-outlined text-xs">check</span>';
        } else if (i === currentStep) {
            dot.className = 'size-6 rounded-full border-2 border-gold bg-gold flex items-center justify-center text-[10px] font-black text-primary-dark';
            dot.innerHTML = i;
        } else {
            dot.className = 'size-6 rounded-full border-2 border-white/10 bg-transparent flex items-center justify-center text-[10px] font-black text-white/20';
            dot.innerHTML = i;
        }
    }
}

async function renderSubjectSelection() {
    const title = document.getElementById('selection-year-title');
    const list = document.getElementById('subjects-selection-list');

    if (title) title.textContent = `Oferta Académica: ${eligibility.targetYear}º Año`;

    const sub = eligibility.nextSubject;
    if (!sub) return;

    list.innerHTML = `
        <div class="py-6 flex items-center justify-between group">
            <div class="flex items-center gap-4">
                <div class="size-12 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center text-gold font-black">
                    ${sub.codigo}
                </div>
                <div>
                    <h5 class="text-sm font-black text-white group-hover:text-gold transition-colors capitalize">${sub.nombre.toLowerCase()}</h5>
                    <p class="text-[10px] text-white/30 font-bold uppercase tracking-widest">Materia Inicial del Año</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[9px] font-black uppercase tracking-widest">Inscripción Sugerida</span>
                <span class="material-symbols-outlined text-gold">verified</span>
            </div>
        </div>
        <div class="py-4 text-center">
            <p class="text-[9px] text-white/20 font-medium uppercase italic">Nota: Las materias restantes de este año se habilitarán secuencialmente.</p>
        </div>
    `;
}

function renderSummary() {
    const summary = document.getElementById('enrollment-summary');
    const sub = eligibility.nextSubject;

    summary.innerHTML = `
        <div class="flex justify-between items-center pb-3 border-b border-white/5">
            <span class="text-[10px] font-bold text-white/40 uppercase">Año a Cursar:</span>
            <span class="text-sm font-black text-gold">${eligibility.targetYear}º Año Académico</span>
        </div>
        <div class="flex justify-between items-center pb-3 border-b border-white/5">
            <span class="text-[10px] font-bold text-white/40 uppercase">Materia a Inscribir:</span>
            <span class="text-sm font-black text-white font-mono">${sub.codigo} - ${sub.nombre}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-[10px] font-bold text-white/40 uppercase">Sede:</span>
            <span class="text-sm font-black text-white">${window.studentContext.sede?.nombre || 'Sede Principal'}</span>
        </div>
    `;
}

async function handleFinalConfirmation() {
    const btn = document.getElementById('btn-confirm-final');
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin material-symbols-outlined">sync</span> PROCESANDO...';

    try {
        const student = window.studentContext.estudiante;
        const sub = eligibility.nextSubject;

        // 1. Find Carga Academica for this subject in current period and student's sede
        const { data: cargas, error: cargaError } = await supabase
            .from('cargas_academicas')
            .select('id')
            .eq('materia_id', sub.id)
            .eq('periodo_id', eligibility.activePeriod.id)
            .eq('sede_id', student.sede_id)
            .limit(1);

        if (cargaError || !cargas || cargas.length === 0) {
            throw new Error('No hay secciones disponibles para esta materia en tu sede este periodo.');
        }

        const cargaId = cargas[0].id;

        // 2. Insert into inscripciones
        const { error: insError } = await supabase
            .from('inscripciones')
            .insert({
                estudiante_id: student.id,
                carga_academica_id: cargaId,
                estado_id: 1
            });

        if (insError) throw insError;

        // 3. Update Student Year if needed (e.g. if they moved up from 0 to 1)
        const { error: updError } = await supabase
            .from('estudiantes')
            .update({ año_actual: eligibility.targetYear })
            .eq('id', student.id);

        if (updError) console.warn('No se pudo actualizar el año_actual del estudiante:', updError);

        // Success Notification
        if (window.NotificationSystem) {
            NotificationSystem.show(`¡Inscripción exitosa en ${sub.nombre}!`, 'success');
        }

        // Redirect to dashboard
        setTimeout(() => {
            window.switchTab('dashboard');
        }, 2000);

    } catch (e) {
        console.error('Enrollment process failed:', e);
        if (window.NotificationSystem) {
            NotificationSystem.show(e.message || 'Error durante la inscripción', 'error');
        }
        btn.disabled = false;
        btn.innerHTML = 'Confirmar y Finalizar';
    }
}

window.initInscription = initInscription;

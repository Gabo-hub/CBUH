import { supabase } from '../../../config/supabase-client.js'

/**
 * Inicializar módulo de estudiantes
 * @param {number|null} filterSubjectId - ID de la materia para filtrar y expandir automáticamente
 */
export async function initStudents(filterSubjectId = null) {
    console.log('[StudentsModule] Initializing...', filterSubjectId);
    await loadStudentsData(filterSubjectId);
    setupGradeForm();
}

// Global functions for modal interaction
window.closeModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden');
}

window.confirmCloseSubject = async function (cargaId, button) {
    if (!confirm('¿Estás seguro de que deseas CERRAR el acta de esta materia? Una vez cerrada, no podrás modificar más notas.')) return;

    try {
        button.disabled = true;
        button.innerHTML = '...';

        const { error } = await supabase
            .from('cargas_academicas')
            .update({ es_confirmada: true })
            .eq('id', cargaId);

        if (error) throw error;

        // Success notification and reload
        if (window.NotificationSystem) NotificationSystem.show('Acta cerrada exitosamente', 'success');

        // Reload to reflect changes (lock UI)
        await loadStudentsData(cargaId);

    } catch (e) {
        console.error(e);
        alert('Error al cerrar el acta: ' + e.message);
        button.disabled = false;
        button.innerHTML = 'Cerrar Acta';
    }
}

window.openGradeModal = function (inscriptionId, studentName, corteField, currentValue, title) {
    const modal = document.getElementById('modal-grades');
    if (!modal) return;

    document.getElementById('modal-student-name').textContent = studentName;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('grade-inscription-id').value = inscriptionId;
    document.getElementById('grade-field').value = corteField; // nota1, nota2
    document.getElementById('grade-input').value = currentValue || '';

    modal.classList.remove('hidden');
}

window.handleGradeClick = function (btn) {
    const id = btn.dataset.id;
    const name = btn.dataset.name;
    const field = btn.dataset.field;
    const val = btn.dataset.value;
    const title = btn.dataset.title;
    window.openGradeModal(id, name, field, val, title);
}

window.openAttendanceModal = function () {
    const modal = document.getElementById('modal-attendance');
    if (modal) modal.classList.remove('hidden');
}

window.openStudentProfile = function (student) {
    const modal = document.getElementById('modal-student-profile');
    if (!modal) return;

    // Populate data
    document.getElementById('profile-modal-name').textContent = `${student.nombres} ${student.apellidos}`;
    document.getElementById('profile-modal-cedula').textContent = student.cedula;
    document.getElementById('profile-modal-phone').textContent = student.telefono || 'No registrado';
    document.getElementById('profile-modal-email').textContent = student.usuario?.correo || 'No registrado'; // Fetch email from user relation

    // Photo logic
    const imgEl = document.getElementById('profile-modal-img');
    const iconEl = document.getElementById('profile-modal-icon');

    if (student.usuario?.url_foto) {
        imgEl.src = student.usuario.url_foto;
        imgEl.classList.remove('hidden');
        iconEl.classList.add('hidden');
    } else {
        imgEl.classList.add('hidden');
        iconEl.classList.remove('hidden');
    }

    modal.classList.remove('hidden');
}

function setupGradeForm() {
    const form = document.getElementById('form-grades');
    if (form && !form.dataset.listening) {
        form.dataset.listening = "true";
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            try {
                btn.disabled = true;
                btn.textContent = '...';

                const inscriptionId = document.getElementById('grade-inscription-id').value;
                const field = document.getElementById('grade-field').value;
                const val = parseFloat(document.getElementById('grade-input').value);

                if (isNaN(val) || val < 0 || val > 20) throw new Error('Nota inválida (0-20)');

                // 1. Check if grade record exists
                const { data: existing } = await supabase
                    .from('calificaciones')
                    .select('id')
                    .eq('inscripcion_id', inscriptionId)
                    .maybeSingle();

                let error = null;

                if (!existing) {
                    // Create
                    const { error: insertError } = await supabase
                        .from('calificaciones')
                        .insert([{
                            inscripcion_id: inscriptionId,
                            [field]: val
                        }]);
                    error = insertError;
                } else {
                    // Update
                    const { error: updateError } = await supabase
                        .from('calificaciones')
                        .update({ [field]: val })
                        .eq('id', existing.id);
                    error = updateError;
                }

                if (error) throw error;

                // SPECIAL LOGIC: If saving Final Grade, update Status to "Closed" (2)
                // This allows the student to enroll in the next subject
                if (field === 'nota_final') {
                    const { error: statusError } = await supabase
                        .from('inscripciones')
                        .update({ estado_id: 2 }) // 2 = Closed/Finalized
                        .eq('id', inscriptionId);

                    if (statusError) console.warn('Could not update inscription status', statusError);
                }

                if (error) throw error;

                // Success
                if (window.NotificationSystem) NotificationSystem.show('Nota guardada', 'success');
                window.closeModal('modal-grades');

                // Reload data and keep current filter to auto-expand the section
                // We extract the carga_academica_id from the student list logic if possible, 
                // but simpler: just reload with the same filter that was used before or detect it.
                const activeDetails = document.querySelector('details[open]');
                const lastFilterId = activeDetails ? activeDetails.id.replace('details-', '') : null;
                await loadStudentsData(lastFilterId);

            } catch (e) {
                console.error(e);
                alert(e.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }
}

async function loadStudentsData(filterSubjectId = null) {
    const context = window.teacherContext;
    if (!context) return;

    const accordion = document.getElementById('subjects-accordion');
    if (!accordion) return;

    try {
        // 1. Obtener materias asignadas al profesor
        const { data: assignments, error: assignError } = await supabase
            .from('cargas_academicas')
            .select(`
                id,
                es_confirmada,
                materia:materias (id, nombre, codigo),
                seccion:secciones (nombre)
            `)
            .eq('docente_id', context.docenteId);

        if (assignError) throw assignError;

        if (!assignments || assignments.length === 0) {
            accordion.innerHTML = '<p class="text-center py-10 text-white/40 uppercase font-black tracking-widest">No tienes materias asignadas</p>';
            return;
        }

        // 1.5. Fetch Schedules manually to avoid nested query issues (ERR_CONNECTION_CLOSED)
        const cargaIds = assignments.map(a => a.id);
        const { data: allHorarios, error: horError } = await supabase
            .from('horarios')
            .select('id, carga_academica_id, dia_semana, hora_inicio, hora_fin, aula')
            .in('carga_academica_id', cargaIds);

        if (!horError && allHorarios) {
            // Map schedules to assignments
            assignments.forEach(a => {
                a.horarios = allHorarios.filter(h => h.carga_academica_id === a.id);
            });
        }

        // Update counts
        const totalSubjectsEl = document.getElementById('total-subjects-count');
        if (totalSubjectsEl) totalSubjectsEl.textContent = assignments.length.toString().padStart(2, '0');

        let totalStudents = 0;
        let html = '';

        // 2. Por cada materia, obtener estudiantes inscritos Y sus calificaciones
        for (const assign of assignments) {
            // Fetch students + grades linked to inscripciones
            // Since qualifications is a child of inscripciones, we can select it nested
            const { data: enrollments, error: enrollError } = await supabase
                .from('inscripciones')
                .select(`
                    id,
                    estudiante:estudiantes (
                        id,
                        nombres,
                        apellidos,
                        cedula,
                        telefono,
                        estado_id,
                        usuario:usuarios (url_foto, correo)
                    ),
                    calificaciones (
                        id,
                        nota_corte,
                        nota_final
                    )
                `)
                .eq('carga_academica_id', assign.id);

            if (enrollError) throw enrollError;

            totalStudents += enrollments.length;

            const isOpen = filterSubjectId && assign.id == filterSubjectId;
            html += renderSubjectAccordion(assign, enrollments, isOpen);
        }

        accordion.innerHTML = html;

        const totalStudentsEl = document.getElementById('total-students-count');
        if (totalStudentsEl) totalStudentsEl.textContent = totalStudents.toString().padStart(3, '0');

        // Scroll to opened element if any
        if (filterSubjectId) {
            setTimeout(() => {
                const opened = document.querySelector('details[open]');
                if (opened) opened.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }

    } catch (e) {
        console.error('[StudentsModule] Error:', e);
        accordion.innerHTML = '<p class="text-red-400 text-center py-10">Error al cargar datos</p>';
    }
}

function renderSubjectAccordion(assignment, enrollments, isOpen) {
    const hasSchedule = assignment.horarios && assignment.horarios.length > 0;

    const horarioPrincipal = hasSchedule
        ? `${assignment.horarios[0].hora_inicio} - ${assignment.horarios[0].hora_fin}`
        : 'Horario no definido';
    const aulaPrincipal = hasSchedule
        ? assignment.horarios[0].aula
        : 'N/A';

    const studentRows = enrollments.map(row => {
        const s = row.estudiante;
        // Fix for O2O relationship detection: Supabase might return an object instead of array
        let grades = {};
        if (Array.isArray(row.calificaciones)) {
            grades = row.calificaciones.length > 0 ? row.calificaciones[0] : {};
        } else if (row.calificaciones) {
            grades = row.calificaciones;
        }

        // Simplified Logic: 1 cut (nota_corte) and Final Grade (nota_final)
        // Fix: Use loose inequality for null/undefined check
        const n1 = (grades.nota_corte != null && grades.nota_corte !== "") ? parseFloat(grades.nota_corte) : null;
        const nf = (grades.nota_final != null && grades.nota_final !== "") ? parseFloat(grades.nota_final) : null;

        // Display logic
        const getGradeStyle = (n) => n !== null && !isNaN(n) ? (n >= 10 ? 'text-gold' : 'text-red-400') : 'text-white/40';
        const n1Display = (n1 !== null && !isNaN(n1)) ? n1 : '--';
        const nfDisplay = (nf !== null && !isNaN(nf)) ? nf : ((n1 !== null && !isNaN(n1)) ? n1 : '0.0');

        // Check if subject is closed (locked) OR if it has NO schedule
        const isLocked = assignment.es_confirmada || !hasSchedule;

        let pointerEvents = '';
        let rowOpacity = '';

        if (assignment.es_confirmada) {
            pointerEvents = 'pointer-events-none grayscale opacity-50';
        } else if (!hasSchedule) {
            pointerEvents = 'pointer-events-none opacity-30 cursor-not-allowed';
        }

        return `
        <div class="flex items-center justify-between p-3 rounded-xl hover:bg-card-dark/50 transition-colors group/item border border-transparent hover:border-white/5">
            <div class="flex items-center gap-4">
                <div class="size-9 rounded-full bg-slate-800 border border-white/10 overflow-hidden shrink-0 flex items-center justify-center">
                    ${s.usuario?.url_foto
                ? `<img src="${s.usuario.url_foto}" class="w-full h-full object-cover">`
                : '<span class="text-xs">👤</span>'}
                </div>
                <div>
                    <p class="text-sm font-bold text-white uppercase">${s.apellidos}, ${s.nombres}</p>
                    <p class="text-[10px] text-white/40 font-mono tracking-tighter">ID: ${s.cedula}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex gap-2 mr-2 opacity-100 lg:opacity-0 lg:group-hover/item:opacity-100 transition-opacity">
                    <button onclick="openAttendanceModal()" class="w-10 h-11 flex items-center justify-center bg-white/5 hover:bg-gold/20 text-gold rounded-xl border border-white/5 transition-all" title="Asistencia">
                        <span class="material-symbols-outlined text-[20px]">how_to_reg</span>
                    </button>
                    
                    <div class="${pointerEvents}">
                        <button 
                            data-action="grade"
                            data-id="${row.id}"
                            data-name="${s.nombres} ${s.apellidos}"
                            data-field="nota_corte"
                            data-value="${n1 !== null ? n1 : ''}"
                            data-title="Calificación de Corte"
                            onclick="window.handleGradeClick(this)"
                            class="w-16 h-11 flex flex-col items-center justify-center bg-white/5 hover:bg-gold/20 ${getGradeStyle(n1)} hover:text-white rounded-xl border border-white/5 transition-all px-2"
                            title="Nota de Corte">
                            <span class="text-[7px] uppercase font-bold opacity-40 mb-0.5">Corte</span>
                            <span class="text-xs font-black">${n1Display}</span>
                        </button>
                    </div>
                    
                    <div class="${pointerEvents}">
                        <button 
                            data-action="grade"
                            data-id="${row.id}"
                            data-name="${s.nombres} ${s.apellidos}"
                            data-field="nota_final"
                            data-value="${nf !== null ? nf : ''}"
                            data-title="Calificación Final Actualizada"
                            onclick="window.handleGradeClick(this)"
                            class="w-16 h-11 flex flex-col items-center justify-center bg-gold/5 hover:bg-gold/20 ${getGradeStyle(nf)} text-gold rounded-xl border border-gold/20 transition-all px-2"
                            title="Nota Final Definitiva">
                            <span class="text-[7px] uppercase font-bold opacity-40 text-gold/60 mb-0.5">Final</span>
                            <span class="text-xs font-black">${nfDisplay}</span>
                        </button>
                    </div>
                </div>
                    <button onclick='window.openStudentProfile(${JSON.stringify(s).replace(/'/g, "&#39;")})'
                            class="hidden lg:block px-4 py-2 bg-white/5 hover:bg-gold text-white/60 hover:text-primary-dark text-[10px] font-black uppercase rounded-xl border border-white/10 transition-all">
                        Perfil
                    </button>
            </div>
</div>
    `}).join('');

    return `
        <details id="details-${assignment.id}" ${isOpen ? 'open' : ''} class="group bg-primary-dark rounded-2xl border border-white/10 overflow-hidden">
            <summary class="flex items-center justify-between p-6 cursor-pointer hover:bg-white/5 transition-all list-none">
                <div class="flex items-center gap-4">
                    <div class="size-12 rounded-xl ${assignment.es_confirmada ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500' : 'bg-card-dark border-gold/20 text-gold'} border flex items-center justify-center">
                        <span class="material-symbols-outlined">${assignment.es_confirmada ? 'lock' : 'auto_stories'}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2">
                            ${assignment.materia.nombre}
                            ${assignment.es_confirmada ? '<span class="px-2 py-0.5 rounded text-[9px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">CERRADA</span>' : ''}
                            ${!hasSchedule ? '<span class="px-2 py-0.5 rounded text-[9px] bg-red-500/10 text-red-400 border border-red-500/20">SIN HORARIO</span>' : ''}
                        </h3>
                        <p class="text-[11px] font-bold text-white/40 uppercase tracking-widest">${horarioPrincipal} • Aula ${aulaPrincipal}</p>
                        ${!hasSchedule ? '<p class="text-[9px] text-red-400 mt-1 font-bold">⚠️ Debe asignar un horario para cargar notas</p>' : ''}
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    ${!assignment.es_confirmada ? `
                        <button onclick="event.stopPropagation(); window.confirmCloseSubject(${assignment.id}, this)" 
                            class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 rounded-lg text-xs font-bold uppercase transition-all">
                            Cerrar Acta
                        </button>
                    ` : ''}
                    
                    <div class="text-right">
                        <p class="text-2xl font-black ${assignment.es_confirmada ? 'text-white/40' : 'text-gold'} leading-none">${enrollments.length}</p>
                        <p class="text-[9px] font-bold text-gold/60 uppercase">Estudiantes</p>
                    </div>
                    <span class="material-symbols-outlined text-white/40 transition-transform group-open:rotate-180">expand_more</span>
                </div>
            </summary>
            <div class="p-4 bg-background-dark/30 border-t border-white/5">
                <div class="space-y-1">
                    ${studentRows || '<p class="text-center py-4 text-white/20 text-xs">No hay estudiantes inscritos en esta sección</p>'}
                </div>
            </div>
        </details>
    `;
}

// Exponer globalmente
window.initStudents = initStudents;

import { supabase } from '../../../config/supabase-client.js';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

export async function initReports() {
    console.log('[Reports] Module Initialized');
    window.generatePDF = generatePDF;
    window.openReportModal = openReportModal;
}

// Open Config Modal for parametric reports
async function openReportModal(type) {
    const modalId = `reportModal_${type}`;
    let modal = document.getElementById(modalId);

    // Remove existing if any (to reset state easier)
    if (modal) modal.remove();

    // Create container
    modal = document.createElement('div');
    modal.id = modalId;
    modal.className = 'fixed inset-0 bg-black/80 flex items-center justify-center z-50 backdrop-blur-sm';

    let contentHtml = '';
    let title = '';

    if (type === 'students') {
        title = 'Nómina Estudiantil';
        contentHtml = `
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-white/60 mb-2 uppercase tracking-widest">Año Académico</label>
                    <select id="report_year_select" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-gold/50">
                        <option value="all">Todos los años</option>
                        <option value="1">1er Año</option>
                        <option value="2">2do Año</option>
                        <option value="3">3er Año</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-white/60 mb-2 uppercase tracking-widest">Estado</label>
                    <select id="report_status_select" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-gold/50">
                        <option value="1">Activos</option>
                        <option value="all">Todos</option>
                    </select>
                </div>
            </div>
        `;
    } else if (type === 'grades_student') {
        title = 'Boletín de Notas';
        contentHtml = `
            <div class="space-y-4">
                 <div>
                    <label class="block text-xs font-bold text-white/60 mb-2 uppercase tracking-widest">Cédula del Estudiante</label>
                    <div class="flex gap-2">
                        <input id="report_student_cedula" type="text" placeholder="Ej: V-12345678" 
                            class="flex-1 bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-gold/50 placeholder:text-white/20">
                        <button onclick="checkStudentName()" class="bg-white/10 px-4 rounded-xl hover:bg-white/20 transition-colors">
                            <span class="material-symbols-outlined text-white">search</span>
                        </button>
                    </div>
                     <p id="report_student_name_preview" class="text-xs text-gold font-bold mt-2 h-4"></p>
                </div>
            </div>
        `;
    } else if (type === 'grades_subject') {
        title = 'Acta de Evaluación';
        // Need to fetch subjects first? We can do a quick fetch inside the generator, 
        // but for UI, let's load them or just ask for ID/Code? 
        // Better: Dropdown of subjects.

        // Fetch subjects on open
        contentHtml = `
             <div class="space-y-4">
                <div id="report_loading_subjects" class="text-center py-4">
                    <span class="animate-spin material-symbols-outlined text-gold">sync</span>
                </div>
                <div id="report_subject_container" class="hidden">
                    <label class="block text-xs font-bold text-white/60 mb-2 uppercase tracking-widest">Seleccionar Materia</label>
                    <select id="report_subject_select" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-gold/50 max-h-64">
                         <!-- Options loaded via JS -->
                    </select>
                </div>
            </div>
        `;
        // Async loader
        setTimeout(loadSubjectsForReport, 100);
    }

    modal.innerHTML = `
        <div class="bg-primary-dark rounded-2xl border border-white/10 max-w-md w-full mx-4 shadow-2xl overflow-hidden transform transition-all scale-100">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-black/20">
                 <h3 class="text-sm font-black text-white uppercase tracking-widest">${title}</h3>
                 <button onclick="document.getElementById('${modalId}').remove()" class="size-8 rounded-full hover:bg-white/5 text-white/40 hover:text-white transition-all">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
            <div class="p-6">
                ${contentHtml}
            </div>
            <div class="p-6 border-t border-white/5 bg-black/20 flex justify-end">
                <button onclick="window.runReportGeneration('${type}')" 
                    class="bg-gold text-primary-dark px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-white transition-colors shadow-lg shadow-gold/10">
                    Generar PDF
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
}

// Helper to check student for Boletin
window.checkStudentName = async function () {
    const input = document.getElementById('report_student_cedula');
    const preview = document.getElementById('report_student_name_preview');
    if (!input || !preview) return;

    const cedula = input.value.trim();
    if (cedula.length < 5) return;

    preview.textContent = 'Buscando...';

    const { data, error } = await supabase
        .from('estudiantes')
        .select('nombres, apellidos')
        .ilike('cedula', `%${cedula}%`)
        .maybeSingle();

    if (data) {
        preview.textContent = `${data.nombres} ${data.apellidos}`;
        preview.className = 'text-xs text-green-400 font-bold mt-2 h-4';
    } else {
        preview.textContent = 'Estudiante no encontrado';
        preview.className = 'text-xs text-red-400 font-bold mt-2 h-4';
    }
}

// Helper to load subjects
async function loadSubjectsForReport() {
    const loader = document.getElementById('report_loading_subjects');
    const container = document.getElementById('report_subject_container');
    const select = document.getElementById('report_subject_select');

    if (!select) return;

    const { data: subjects } = await supabase
        .from('materias')
        .select('id, nombre, codigo, año_materia')
        .eq('estado_id', 1)
        .order('año_materia')
        .order('nombre');

    if (subjects) {
        select.innerHTML = subjects.map(s =>
            `<option value="${s.id}">${s.año_materia}º - ${s.codigo} - ${s.nombre}</option>`
        ).join('');

        loader?.classList.add('hidden');
        container?.classList.remove('hidden');
    }
}

// Main Dispatcher from Modal
window.runReportGeneration = async function (type) {
    const btn = document.querySelector(`#reportModal_${type} button[onclick*="runReportGeneration"]`);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = 'Generando...';
    }

    try {
        if (type === 'students') {
            const year = document.getElementById('report_year_select').value;
            const status = document.getElementById('report_status_select').value;
            await generateStudentReport(year, status);
        } else if (type === 'grades_student') {
            const cedula = document.getElementById('report_student_cedula').value;
            await generateGradesReport(cedula);
        } else if (type === 'grades_subject') {
            const subjectId = document.getElementById('report_subject_select').value;
            await generateSubjectReport(subjectId);
        }

        // Close modal
        document.getElementById(`reportModal_${type}`).remove();

    } catch (e) {
        console.error(e);
        alert('Error generando reporte: ' + e.message);
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Generar PDF';
        }
    }
}

// Direct Generation (No modal needed)
async function generatePDF(type) {
    if (type === 'teachers') {
        await generateTeacherReport();
    }
}

/* ==================================================================================
   PDF GENERATORS
   ================================================================================== */

function getBasePDF() {
    const doc = new jsPDF();

    // Header helper
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text("Colegio Bíblico Unido de Higuerote", 105, 20, { align: 'center' });

    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text("Control de Estudios", 105, 26, { align: 'center' });

    doc.line(14, 30, 196, 30); // Horizontal line

    return doc;
}

// 1. TEACHERS REPORT
async function generateTeacherReport() {
    const doc = getBasePDF();

    // Fetch Data
    const { data: teachers, error } = await supabase
        .from('docentes')
        .select(`
            cedula, nombres, apellidos, telefono, especialidad,
            usuarios (correo)
        `)
        .eq('estado_id', 1)
        .order('apellidos');

    if (error) throw error;

    const rows = teachers.map(t => [
        t.cedula,
        `${t.apellidos} ${t.nombres}`,
        t.especialidad || 'Sin especialidad',
        t.telefono || '-',
        t.usuarios?.correo || '-'
    ]);

    doc.setFontSize(12);
    doc.setFont("helvetica", "bold");
    doc.text("DIRECTORIO DOCENTE", 14, 40);
    doc.setFontSize(10);
    doc.text(`Fecha: ${new Date().toLocaleDateString()}`, 14, 46);

    // Use functional approach: autoTable(doc, options)
    autoTable(doc, {
        startY: 50,
        head: [['Cédula', 'Nombre Completo', 'Especialidad', 'Teléfono', 'Correo']],
        body: rows,
        theme: 'grid',
        headStyles: { fillColor: [201, 169, 97], textColor: 0, fontStyle: 'bold' }, // Gold color
        styles: { fontSize: 9 },
    });

    doc.save('CBUH_Docentes.pdf');
}

// 2. STUDENTS REPORT
async function generateStudentReport(year, status) {
    const doc = getBasePDF();

    let query = supabase
        .from('estudiantes')
        .select(`
            cedula, nombres, apellidos, telefono, año_actual, estado_id,
            usuarios (correo)
        `)
        .order('apellidos');

    if (year !== 'all') query = query.eq('año_actual', year);
    if (status !== 'all') query = query.eq('estado_id', status);

    const { data: students, error } = await query;
    if (error) throw error;

    const rows = students.map(s => [
        s.cedula,
        `${s.apellidos} ${s.nombres}`,
        `${s.año_actual}º Año`,
        s.telefono || '-',
        s.usuarios?.correo || '-'
    ]);

    doc.setFontSize(12);
    doc.setFont("helvetica", "bold");
    const yearText = year === 'all' ? 'TODOS LOS AÑOS' : `${year}º AÑO ACADÉMICO`;
    doc.text(`NÓMINA ESTUDIANTIL - ${yearText}`, 14, 40);
    doc.setFontSize(10);
    doc.text(`Total Estudiantes: ${students.length}`, 14, 46);

    autoTable(doc, {
        startY: 50,
        head: [['Cédula', 'Nombre Completo', 'Año', 'Teléfono', 'Correo']],
        body: rows,
        theme: 'grid',
        headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' }, // Blue
        styles: { fontSize: 9 },
    });

    doc.save(`CBUH_Matricula_${year === 'all' ? 'General' : year + 'Anio'}.pdf`);
}

// 3. GRADE REPORT (BOLETIN)
async function generateGradesReport(cedula) {
    if (!cedula) return;

    // Fetch Student
    const { data: student, error: stError } = await supabase
        .from('estudiantes')
        .select('*, usuarios(correo)')
        .ilike('cedula', `%${cedula}%`)
        .single();

    if (stError || !student) throw new Error("Estudiante no encontrado");

    // Fetch Grades
    const { data: grades, error: gError } = await supabase
        .from('inscripciones')
        .select(`
            carga_academica:carga_academica_id (
                materia:materia_id (nombre, codigo, año_materia),
                docente:docente_id (nombres, apellidos)
            ),
            calificaciones (nota_final, nota_reparacion, nota_corte)
        `)
        .eq('estudiante_id', student.id);

    if (gError) throw gError;

    const doc = getBasePDF();

    // Student Info Block
    doc.setFontSize(11);
    doc.setFont("helvetica", "bold");
    doc.text("BOLETÍN INFORMATIVO", 105, 40, { align: 'center' });

    doc.setFontSize(10);
    doc.text(`Estudiante: ${student.apellidos} ${student.nombres}`, 14, 50);
    doc.text(`Cédula: ${student.cedula}`, 14, 56);
    doc.text(`Año Actual: ${student.año_actual}º Año`, 140, 50);
    doc.text(`Fecha: ${new Date().toLocaleDateString()}`, 140, 56);

    // Filter valid grades
    const cleanGrades = grades.map(g => {
        const mat = g.carga_academica?.materia;
        const prof = g.carga_academica?.docente;
        const cal = g.calificaciones?.[0];

        // Logic to determine final grade to show
        const final = cal?.nota_reparacion !== null && cal?.nota_reparacion !== undefined
            ? cal.nota_reparacion
            : (cal?.nota_final || '-');

        const status = (Number(final) >= 10) ? 'APROBADO' : (final === '-' ? 'CURSANDO' : 'REPROBADO');

        return [
            mat?.codigo || '?',
            mat?.nombre || 'Desconocida',
            prof ? `${prof.nombres.split(' ')[0]} ${prof.apellidos.split(' ')[0]}` : 'Sin Docente',
            final,
            status
        ];
    });

    autoTable(doc, {
        startY: 65,
        head: [['Cód.', 'Materia', 'Docente', 'Nota', 'Estado']],
        body: cleanGrades,
        theme: 'plain',
        headStyles: { fillColor: [40, 167, 69], textColor: 255, fontStyle: 'bold' }, // Green
        styles: { fontSize: 10, cellPadding: 3 },
        columnStyles: {
            0: { fontStyle: 'bold' }, // Code
            4: { fontStyle: 'bold' }  // Status
        }
    });

    // Calculate Average
    const validScores = cleanGrades
        .map(row => Number(row[3]))
        .filter(n => !isNaN(n));

    if (validScores.length > 0) {
        const avg = (validScores.reduce((a, b) => a + b, 0) / validScores.length).toFixed(2);
        // Use autoTable.previous.finalY instead of doc.lastAutoTable.finalY if needed,
        // but typically doc.lastAutoTable is still populated by the plugin even if called functionally.
        // Let's use more robust check
        const finalY = (doc.lastAutoTable && doc.lastAutoTable.finalY) || 120;

        doc.setFont("helvetica", "bold");
        doc.text(`PROMEDIO ACADÉMICO: ${avg}`, 14, finalY + 10);
    }

    doc.save(`Boletin_${student.cedula}.pdf`);
}

// 4. SUBJECT GRADE REPORT (ACTA)
async function generateSubjectReport(subjectId) {
    if (!subjectId) return;

    // 1. Get Subject Info
    const { data: subject, error: sError } = await supabase
        .from('materias')
        .select('nombre, codigo, año_materia')
        .eq('id', subjectId)
        .single();

    if (sError) throw sError;

    // 2. Get Students enrolled in this subject (via inscripciones -> carga -> materia_id)
    // Complex query: We need inscripciones where carga_academica.materia_id = subjectId

    // Step 1: Find cargas for this subject
    const { data: cargas } = await supabase
        .from('cargas_academicas')
        .select('id, docente:docente_id(nombres, apellidos)')
        .eq('materia_id', subjectId)
        .eq('estado_id', 1); // Active loads

    if (!cargas || cargas.length === 0) throw new Error("No hay carga académica activa para esta materia");

    const cargaIds = cargas.map(c => c.id);
    const teacherName = cargas[0].docente
        ? `${cargas[0].docente.nombres} ${cargas[0].docente.apellidos}`
        : 'Sin Docente Asignado';

    // Step 2: Get inscriptions
    const { data: inscriptions, error: iError } = await supabase
        .from('inscripciones')
        .select(`
            estudiante:estudiante_id (cedula, nombres, apellidos),
            calificaciones (nota_final, nota_reparacion)
        `)
        .in('carga_academica_id', cargaIds)
        .order('estudiante(apellidos)'); // This order might not work directly in nested, handle in sort js

    if (iError) throw iError;

    // JS Sort logic because Supabase nested sort is tricky
    inscriptions.sort((a, b) => a.estudiante.apellidos.localeCompare(b.estudiante.apellidos));

    const doc = getBasePDF();

    // Header
    doc.setFontSize(12);
    doc.setFont("helvetica", "bold");
    doc.text("ACTA DE EVALUACIÓN", 105, 40, { align: 'center' });

    doc.setFontSize(10);
    doc.text(`Materia: ${subject.nombre} (${subject.codigo})`, 14, 50);
    doc.text(`Docente: ${teacherName}`, 14, 56);
    doc.text(`Año: ${subject.año_materia}º`, 140, 50);
    doc.text(`Total: ${inscriptions.length} Estudiantes`, 140, 56);

    const rows = inscriptions.map((i, index) => {
        const cal = i.calificaciones?.[0];
        const final = cal?.nota_reparacion !== null && cal?.nota_reparacion !== undefined
            ? cal.nota_reparacion
            : (cal?.nota_final || '-');

        return [
            index + 1,
            i.estudiante.cedula,
            `${i.estudiante.apellidos} ${i.estudiante.nombres}`,
            final,
            // Optional: Observation column could be added
        ];
    });

    autoTable(doc, {
        startY: 65,
        head: [['#', 'Cédula', 'Estudiante', 'Nota Final']],
        body: rows,
        theme: 'grid',
        headStyles: { fillColor: [155, 89, 182], textColor: 255, fontStyle: 'bold' }, // Purple
        styles: { fontSize: 10 },
        columnStyles: {
            0: { cellWidth: 10 },
            3: { halign: 'center', fontStyle: 'bold' }
        }
    });

    // Signature Area
    const finalY = (doc.lastAutoTable && doc.lastAutoTable.finalY) || 120;
    doc.line(70, finalY + 40, 140, finalY + 40);
    doc.text("Firma del Docente", 105, finalY + 45, { align: 'center' });

    doc.save(`Acta_${subject.codigo}.pdf`);
}

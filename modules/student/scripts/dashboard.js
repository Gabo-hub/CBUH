import '../../../assets/css/tailwind.css'
import { supabase } from '../../../config/supabase-client.js'
import { timeAgo } from '../../../shared/scripts/utils.js'

let currentUser = null
let currentProfile = null

// Initialize User
async function init() {
    const { data: { session } } = await supabase.auth.getSession()
    if (!session) {
        window.location.href = '/auth/login.html'
        return
    }

    currentUser = session.user

    try {
        // Get complete user profile
        const { data: profile, error } = await supabase
            .from('usuarios')
            .select(`
                *,
                rol:roles(nombre),
                sede:sedes(nombre, codigo)
            `)
            .eq('auth_id', currentUser.id)
            .single()

        if (error) throw error

        // Check if Student (Rol ID 4 - asumed or check database)
        // Usually: 1=SuperAdmin, 2=AdminSede, 3=Docente, 4=Estudiante
        if (profile.rol_id !== 4) {
            // If not student, kick out or redirect (or handle gracefully if multiple roles allowed)
            // window.location.href = '/'
        }

        currentProfile = profile

        // Fetch Student Specific Data (from 'estudiantes' table)
        const { data: studentData, error: studentError } = await supabase
            .from('estudiantes')
            .select(`
                *,
                estado:estados_registro!estado_id(nombre)
            `)
            .eq('usuario_id', profile.id)
            .maybeSingle()

        if (studentError) console.error('Error fetching student data:', studentError)

        currentProfile.estudiante = studentData

        // Expose Context
        window.studentContext = {
            user: currentUser,
            profile: currentProfile, // simplified usage
            studentId: studentData ? studentData.id : null,
            ...currentProfile // spread for easier access
        }

        // Initialize Layout
        await loadLayout();

        // Load Default Tab
        await switchTab('dashboard');

        setupEventListeners();

    } catch (e) {
        console.error('Init Error:', e)
        alert('Error inicializando sesión: ' + e.message)
    }
}

async function loadLayout() {
    try {
        await Promise.all([
            window.loadComponent('/modules/shared/components/layout/sidebar-student.html', 'layout-sidebar'),
            window.loadComponent('/modules/shared/components/layout/header.html', 'layout-header')
        ]);

        // Update Header Info
        const displayNombre = currentProfile.estudiante
            ? `${currentProfile.estudiante.nombres} ${currentProfile.estudiante.apellidos}`
            : currentProfile.usuario;

        const headerTitle = document.getElementById('header-title');
        // Student Header usually simpler text
        if (headerTitle) headerTitle.innerHTML = 'HOLA, <span class="text-gold">' + displayNombre.toUpperCase() + '</span>';

        const nameDisplay = document.getElementById('admin-name');
        if (nameDisplay) nameDisplay.textContent = displayNombre;

        const photoUrl = currentProfile.url_foto;
        if (photoUrl) {
            const hImg = document.getElementById('header-mobile-profile-img');
            const hIcon = document.getElementById('header-mobile-profile-icon');
            if (hImg) { hImg.src = photoUrl; hImg.classList.remove('hidden'); }
            if (hIcon) hIcon.classList.add('hidden');
        }

    } catch (e) {
        console.error('Layout Load Error:', e);
    }
}


window.loadDashboardData = async function () {
    const context = window.studentContext;
    if (!context || !context.estudiante) return;

    const e = context.estudiante;

    // Fill Sidebar Profile Info (if sidebar is already loaded)
    // Actually sidebar is static mostly, but dashboard-tab has profile info too.

    // Fill Dashboard Tab Profile Section
    const profileImg = document.getElementById('dashboard-profile-img');
    const profileIcon = document.getElementById('dashboard-profile-icon');

    if (context.url_foto && profileImg) {
        profileImg.src = context.url_foto;
        profileImg.classList.remove('hidden');
        if (profileIcon) profileIcon.classList.add('hidden');
    }

    const nameEl = document.getElementById('dashboard-student-name');
    if (nameEl) nameEl.textContent = `${e.nombres} ${e.apellidos}`;

    const careerEl = document.getElementById('dashboard-student-career');
    // Assuming 'carrera' column or just hardcoded for CBUH for now
    // Or we fetch it. But let's check DB schema later. 
    if (careerEl) careerEl.textContent = "Estudiante CBUH";

    // Fill Info Grid
    const grid = document.getElementById('student-info-grid');
    if (grid) {
        // Calculate Age
        const calculateAge = (dateStr) => {
            if (!dateStr) return 'N/A';
            const diff = Date.now() - new Date(dateStr).getTime();
            const ageDt = new Date(diff);
            return Math.abs(ageDt.getUTCFullYear() - 1970);
        }

        const age = calculateAge(e.fecha_nacimiento);

        grid.innerHTML = `
            <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                <div class="flex items-center gap-2 text-white/40 mb-2">
                    <span class="material-symbols-outlined text-sm text-gold/60">id_card</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Cédula</span>
                </div>
                <p class="text-xl font-bold text-white">${e.cedula}</p>
            </div>
            <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                <div class="flex items-center gap-2 text-white/40 mb-2">
                    <span class="material-symbols-outlined text-sm text-gold/60">cake</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Edad</span>
                </div>
                <p class="text-xl font-bold text-white">${age} Años</p>
            </div>
            <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                <div class="flex items-center gap-2 text-white/40 mb-2">
                    <span class="material-symbols-outlined text-sm text-gold/60">call</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Teléfono</span>
                </div>
                <p class="text-xl font-bold text-white">${e.telefono || 'N/A'}</p>
            </div>
            <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                <div class="flex items-center gap-2 text-white/40 mb-2">
                    <span class="material-symbols-outlined text-sm text-gold/60">location_on</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Dirección</span>
                </div>
                <p class="text-base font-bold text-white leading-tight">${e.direccion || 'N/A'}</p>
            </div>
            <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                <div class="flex items-center gap-2 text-white/40 mb-2">
                    <span class="material-symbols-outlined text-sm text-gold/60">event</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Fecha de Nacimiento</span>
                </div>
                <p class="text-base font-bold text-white leading-tight">${e.fecha_nacimiento || 'N/A'}</p>
            </div>
            <div class="bg-card-dark p-6 rounded-2xl border border-white/5 flex flex-col gap-1">
                <div class="flex items-center gap-2 text-white/40 mb-2">
                    <span class="material-symbols-outlined text-sm text-gold/60">mail</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gold/60">Correo</span>
                </div>
                <p class="text-base font-bold text-white truncate" title="${context.correo}">${context.correo}</p>
            </div>
        `;
    }

    // Load Stats (Placeholder or Real if query available)
    loadStudentStats(e.id);

    // Load Today's Classes
    loadTodayClasses(e.id);

    // Identify Active Subject for the Dashboard Header or Card
    loadActiveSubjectInfo(e.id);
}

async function loadActiveSubjectInfo(studentId) {
    try {
        const { data: enrollments } = await supabase
            .from('inscripciones')
            .select(`
                id,
                carga:cargas_academicas!carga_academica_id (
                    materia:materias (nombre, codigo, orden_secuencia)
                )
            `)
            .eq('estudiante_id', studentId)
            .eq('estado_id', 1);

        if (enrollments && enrollments.length > 0) {
            // Sequential logic
            const active = enrollments.sort((a, b) =>
                (a.carga?.materia?.orden_secuencia || 0) - (b.carga?.materia?.orden_secuencia || 0)
            )[0];

            const activeNameEl = document.getElementById('active-subject-name');
            const activeCodeEl = document.getElementById('active-subject-code');

            if (activeNameEl) activeNameEl.textContent = active.carga.materia.nombre.toUpperCase();
            if (activeCodeEl) activeCodeEl.textContent = active.carga.materia.codigo;
        }
    } catch (e) {
        console.error('Error loading active subject info:', e);
    }
}

async function loadStudentStats(studentId) {
    // Implement aggregate query
    // This is calculating average grade.
    try {
        const { data: gradesData, error } = await supabase
            .from('calificaciones')
            .select('nota_final, inscripcion:inscripciones!inner(estudiante_id)')
            .eq('inscripciones.estudiante_id', studentId);

        if (gradesData && gradesData.length > 0) {
            const grades = gradesData.map(g => g.nota_final).filter(n => n !== null);
            const avg = grades.length ? (grades.reduce((a, b) => a + b, 0) / grades.length).toFixed(2) : '--';

            const statAvg = document.getElementById('stat-avg');
            if (statAvg) statAvg.textContent = avg;

            const statApproved = document.getElementById('stat-approved');
            // Assuming > 10 is approved (CBUH scale 0-20, usually 10 is pass)
            const approved = grades.filter(n => n >= 10).length;
            if (statApproved) statApproved.textContent = approved;
        }

    } catch (e) { console.error('Stats error', e); }
}

async function loadTodayClasses(studentId) {
    const list = document.getElementById('today-classes-list');
    if (!list) return;

    // Logic: Find enrollments -> find academic loads -> find schedules for day
    const day = new Date().getDay() || 7; // 1=Mon, 7=Sun

    try {
        // 1. Get enrolled loads
        const { data: enrollments } = await supabase
            .from('inscripciones')
            .select('carga_academica_id')
            .eq('estudiante_id', studentId)
            .eq('estado_id', 1);

        if (!enrollments || enrollments.length === 0) {
            list.innerHTML = '<p class="text-center text-white/40 py-4">No hay clases hoy</p>';
            return;
        }

        const loadIds = enrollments.map(e => e.carga_academica_id);

        // 2. Get schedules for these loads for today
        const { data: schedules } = await supabase
            .from('horarios')
            .select(`
                hora_inicio,
                hora_fin,
                aula,
                carga:cargas_academicas (
                    materia:materias(nombre),
                    docente:docentes(nombres, apellidos)
                )
            `)
            .in('carga_academica_id', loadIds)
            .eq('dia_semana', day)
            .order('hora_inicio');

        if (!schedules || schedules.length === 0) {
            list.innerHTML = '<p class="text-center text-white/40 py-4">No hay clases hoy</p>';
        } else {
            // Render
            list.innerHTML = schedules.map(s => `
                < div class="flex items-center gap-4 p-4 rounded-xl bg-card-dark border border-white/5 group hover:border-gold/30 transition-all" >
                    <div class="w-16 flex flex-col items-center justify-center border-r border-white/10">
                        <p class="text-sm font-black text-white">${s.hora_inicio.slice(0, 5)}</p>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-white text-base">${s.carga.materia.nombre}</p>
                        <p class="text-[10px] text-gold uppercase font-bold tracking-widest">
                            ${s.aula || 'Aula ?'} • Prof. ${s.carga.docente.nombres}
                        </p>
                    </div>
                </div >
            `).join('');
        }

    } catch (e) {
        console.error('Schedule Load Error', e);
        list.innerHTML = '<p class="text-red-400 text-center">Error cargando horario</p>';
    }
}

// Config Data Loading
window.loadConfigData = async function () {
    const context = window.studentContext;
    if (!context) return;

    // Populate form fields
    const fields = {
        'config-username': context.user.email, // using email as username display
        'config-email': context.user.email,
        'config-phone': context.estudiante?.telefono,
        'config-address': context.estudiante?.direccion,
        'config-birthdate': context.estudiante?.fecha_nacimiento,
        // Specialty/Bio are teacher specific? Maybe add 'Resumen' for student too if column exists?
        // Students usually dont have profile bio in this DB schema.
        'config-display-name': `${context.estudiante?.nombres} ${context.estudiante?.apellidos} `,
        'config-display-email': context.user.email
    };

    for (const [id, val] of Object.entries(fields)) {
        const el = document.getElementById(id);
        if (el) {
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') el.value = val || '';
            else el.textContent = val || '';
        }
    }

    // Setup Save Button
    const saveBtn = document.getElementById('btn-save-config');
    if (saveBtn) saveBtn.onclick = handleSaveConfig;

    // Photo upload handled if needed
    const photoInput = document.getElementById('photo-upload');
    if (photoInput) photoInput.onchange = handlePhotoUpload;
}

async function handleSaveConfig() {
    const btn = document.getElementById('btn-save-config');
    // Collect data
    const phone = document.getElementById('config-phone')?.value;
    const address = document.getElementById('config-address')?.value;
    const birthdate = document.getElementById('config-birthdate')?.value;

    if (!window.studentContext.estudiante) return;

    try {
        btn.disabled = true;
        btn.innerHTML = 'Guardando...';

        const { error } = await supabase
            .from('estudiantes')
            .update({
                telefono: phone,
                direccion: address,
                fecha_nacimiento: birthdate || null
            })
            .eq('id', window.studentContext.estudiante.id);

        if (error) throw error;

        if (window.NotificationSystem) NotificationSystem.show('Datos actualizados', 'success');

        // Update context to reflect changes immediately
        window.studentContext.estudiante.telefono = phone;
        window.studentContext.estudiante.direccion = address;
        window.studentContext.estudiante.fecha_nacimiento = birthdate;

    } catch (e) {
        alert('Error: ' + e.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Guardar Cambios';
    }
}

async function handlePhotoUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    try {
        const btn = document.querySelector('label[for="photo-upload"]');
        if (btn) btn.style.pointerEvents = 'none'; // Disable while uploading

        if (window.NotificationSystem) NotificationSystem.show('Subiendo imagen...', 'info');

        const fileExt = file.name.split('.').pop();
        const fileName = `${window.studentContext.profile.id} -${Date.now()}.${fileExt} `;
        const filePath = `profiles / ${fileName} `;

        // 1. Upload to storage
        const { error: uploadError } = await supabase.storage
            .from('avatars')
            .upload(filePath, file, { cacheControl: '3600', upsert: true });

        if (uploadError) throw uploadError;

        // 2. Get Public URL
        const { data: { publicUrl } } = supabase.storage
            .from('avatars')
            .getPublicUrl(filePath);

        // 3. Update Usuarios table
        const { error: userError } = await supabase
            .from('usuarios')
            .update({ url_foto: publicUrl })
            .eq('id', window.studentContext.profile.id);

        if (userError) throw userError;

        // 4. Update UI & Local state
        window.studentContext.profile.url_foto = publicUrl;

        // Refresh User Interface
        // Update Config Preview
        const configImg = document.getElementById('config-profile-img');
        const configIcon = document.getElementById('config-profile-icon');
        if (configImg) {
            configImg.src = publicUrl;
            configImg.classList.remove('hidden');
            if (configIcon) configIcon.classList.add('hidden');
        }

        // Update Header/Sidebar Layout
        // This function re-reads profile url from context
        loadLayout();

        if (window.NotificationSystem) NotificationSystem.show('Foto de perfil actualizada', 'success');

    } catch (error) {
        console.error('Upload error:', error);
        if (window.NotificationSystem) NotificationSystem.show('Error al subir imagen: ' + error.message, 'error');
        else alert('Error al subir imagen: ' + error.message);
    } finally {
        const btn = document.querySelector('label[for="photo-upload"]');
        if (btn) btn.style.pointerEvents = 'auto';
    }
}

function setupEventListeners() {
    document.getElementById('logout-btn')?.addEventListener('click', async () => {
        await supabase.auth.signOut();
        window.location.href = '/auth/login.html';
    });

    // Mobile sidebar toggles
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const closeBtn = document.getElementById('close-sidebar-btn');

    if (closeBtn) {
        closeBtn.onclick = () => {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden', 'opacity-0');
        }
    }
}


init();

<!-- Encabezado eliminado ya que ahora es global -->
<div class="p-8 space-y-8 max-w-7xl mx-auto w-full">
    <form id="registrationForm" enctype="multipart/form-data" class="space-y-8">
        <div class="bg-primary-dark rounded-3xl p-8 border border-white/10 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gold/5 blur-[100px] rounded-full -mr-20 -mt-20">
            </div>
            <div class="relative z-10 flex flex-col lg:flex-row gap-8 items-start">
                <div class="flex flex-col items-center shrink-0 w-full lg:w-48">
                    <div class="relative group">
                        <div id="photoPreviewContainer"
                            class="size-44 rounded-2xl border-2 border-dashed border-gold/40 flex flex-col items-center justify-center bg-card-dark overflow-hidden relative">
                            <span id="photoPlaceholderIcon"
                                class="material-symbols-outlined text-4xl text-gold/30 mb-2">person</span>
                            <p id="photoPlaceholderText"
                                class="text-[10px] text-white/40 font-bold uppercase text-center px-4">Foto Tipo Carnet
                            </p>
                            <img id="photoPreview" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                            <input id="student_photo" name="foto_perfil" accept="image/*"
                                class="absolute inset-0 opacity-0 cursor-pointer" type="file" />
                        </div>
                        <button type="button" onclick="document.getElementById('student_photo').click()"
                            class="absolute -bottom-3 -right-3 size-10 bg-gold text-primary-dark rounded-full flex items-center justify-center shadow-xl hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined font-bold">add_a_photo</span>
                        </button>
                    </div>
                    <p class="mt-4 text-[10px] font-bold text-gold uppercase tracking-widest text-center">FOTO REQUERIDA
                    </p>
                </div>
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
                    <div class="lg:col-span-3 pb-2 border-b border-white/5">
                        <h3 class="text-xs font-black text-gold uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">person</span>
                            Datos Personales
                        </h3>
                    </div>
                    <!-- Campos del formulario -->
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-bold text-white/40 uppercase tracking-widest block">Nombres</label>
                        <input name="nombres" required
                            class="w-full rounded-xl border-none p-3 text-sm focus:ring-2 focus:ring-gold outline-none shadow-inner bg-white/5 text-white"
                            placeholder="Ej. Juan Gabriel" type="text" />
                    </div>
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-bold text-white/40 uppercase tracking-widest block">Apellidos</label>
                        <input name="apellidos" required
                            class="w-full rounded-xl border-none p-3 text-sm focus:ring-2 focus:ring-gold outline-none shadow-inner bg-white/5 text-white"
                            placeholder="Ej. Pérez García" type="text" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-white/40 uppercase tracking-widest block">Cédula de
                            Identidad</label>
                        <input name="cedula" required
                            class="w-full rounded-xl border-none p-3 text-sm focus:ring-2 focus:ring-gold outline-none shadow-inner bg-white/5 text-white"
                            placeholder="V-00000000" type="text" />
                    </div>
                    <div class="lg:col-span-3 pt-4 pb-2 border-b border-white/5">
                        <h3 class="text-xs font-black text-gold uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">contact_page</span>
                            Contacto y Residencia
                        </h3>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-white/40 uppercase tracking-widest block">Número de
                            Teléfono</label>
                        <input name="telefono"
                            class="w-full rounded-xl border-none p-3 text-sm focus:ring-2 focus:ring-gold outline-none shadow-inner bg-white/5 text-white"
                            placeholder="04XX-0000000" type="tel" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-white/40 uppercase tracking-widest block">Correo
                            Electrónico</label>
                        <input name="correo"
                            class="w-full rounded-xl border-none p-3 text-sm focus:ring-2 focus:ring-gold outline-none shadow-inner bg-white/5 text-white"
                            placeholder="usuario@ejemplo.com" type="email" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-white/40 uppercase tracking-widest block">Fecha de
                            Nacimiento</label>
                        <input name="fecha_nacimiento"
                            class="w-full rounded-xl border-none p-3 text-sm focus:ring-2 focus:ring-gold outline-none shadow-inner bg-white/5 text-white"
                            type="date" />
                    </div>
                    <div class="space-y-2 lg:col-span-1">
                        <label class="text-[10px] font-bold text-white/40 uppercase tracking-widest block">Lugar de
                            Nacimiento</label>
                        <input name="lugar_nacimiento"
                            class="w-full rounded-xl border-none p-3 text-sm focus:ring-2 focus:ring-gold outline-none shadow-inner bg-white/5 text-white"
                            placeholder="Ciudad, Estado" type="text" />
                    </div>
                    <div class="space-y-2 lg:col-span-2">
                        <label class="text-[10px] font-bold text-white/40 uppercase tracking-widest block">Dirección de
                            Habitación</label>
                        <input name="direccion"
                            class="w-full rounded-xl border-none p-3 text-sm focus:ring-2 focus:ring-gold outline-none shadow-inner bg-white/5 text-white"
                            placeholder="Av. Principal, Edificio, Apto, Ciudad" type="text" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de documentación -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-tighter">Documentación <span
                            class="text-gold">Requerida</span></h2>
                    <p class="text-sm text-white/40">Sube las imágenes legibles de los siguientes documentos</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Documento 1 -->
                <div
                    class="bg-primary-dark p-6 rounded-2xl border border-white/10 hover:border-gold/30 transition-all group relative">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="size-12 bg-gold/10 rounded-xl flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-primary-dark transition-all">
                            <span class="material-symbols-outlined text-2xl">badge</span>
                        </div>
                        <div id="status_cedula"
                            class="px-2 py-1 rounded bg-orange-500/10 text-orange-400 text-[9px] font-black uppercase tracking-wider">
                            Pendiente</div>
                    </div>
                    <h4 class="text-white font-bold text-sm uppercase mb-1">Copia de Cédula</h4>
                    <p class="text-white/40 text-[10px] mb-4 leading-tight">Copia legible por ambas caras en un solo
                        archivo.</p>
                    <label
                        class="block w-full text-center py-2.5 bg-card-dark border border-white/10 rounded-lg text-[10px] font-black text-gold uppercase tracking-widest hover:bg-gold hover:text-primary-dark transition-all cursor-pointer">
                        <span id="label_cedula">Subir Archivo</span>
                        <input name="doc_cedula" accept="image/*,application/pdf" class="hidden doc-input"
                            data-type="cedula" type="file" />
                    </label>
                </div>
                <!-- Documento 2 -->
                <div
                    class="bg-primary-dark p-6 rounded-2xl border border-white/10 hover:border-gold/30 transition-all group relative">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="size-12 bg-gold/10 rounded-xl flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-primary-dark transition-all">
                            <span class="material-symbols-outlined text-2xl">school</span>
                        </div>
                        <div id="status_titulo_bachiller"
                            class="px-2 py-1 rounded bg-orange-500/10 text-orange-400 text-[9px] font-black uppercase tracking-wider">
                            Pendiente</div>
                    </div>
                    <h4 class="text-white font-bold text-sm uppercase mb-1">Título de Bachiller</h4>
                    <p class="text-white/40 text-[10px] mb-4 leading-tight">Foto original del título por la cara
                        frontal.</p>
                    <label
                        class="block w-full text-center py-2.5 bg-card-dark border border-white/10 rounded-lg text-[10px] font-black text-gold uppercase tracking-widest hover:bg-gold hover:text-primary-dark transition-all cursor-pointer">
                        <span id="label_titulo_bachiller">Subir Archivo</span>
                        <input name="doc_titulo_bachiller" accept="image/*,application/pdf" class="hidden doc-input"
                            data-type="titulo_bachiller" type="file" />
                    </label>
                </div>
                <!-- Documento 3 -->
                <div
                    class="bg-primary-dark p-6 rounded-2xl border border-white/10 hover:border-gold/30 transition-all group relative">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="size-12 bg-gold/10 rounded-xl flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-primary-dark transition-all">
                            <span class="material-symbols-outlined text-2xl">description</span>
                        </div>
                        <div id="status_notas_certificadas"
                            class="px-2 py-1 rounded bg-orange-500/10 text-orange-400 text-[9px] font-black uppercase tracking-wider">
                            Pendiente</div>
                    </div>
                    <h4 class="text-white font-bold text-sm uppercase mb-1">Notas Certificadas</h4>
                    <p class="text-white/40 text-[10px] mb-4 leading-tight">Documento oficial con sello y firma de la
                        institución.</p>
                    <label
                        class="block w-full text-center py-2.5 bg-card-dark border border-white/10 rounded-lg text-[10px] font-black text-gold uppercase tracking-widest hover:bg-gold hover:text-primary-dark transition-all cursor-pointer">
                        <span id="label_notas_certificadas">Subir Archivo</span>
                        <input name="doc_notas_certificadas" accept="image/*,application/pdf" class="hidden doc-input"
                            data-type="notas_certificadas" type="file" />
                    </label>
                </div>
                <!-- Documento 4 -->
                <div
                    class="bg-primary-dark p-6 rounded-2xl border border-white/10 hover:border-gold/30 transition-all group relative">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="size-12 bg-gold/10 rounded-xl flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-primary-dark transition-all">
                            <span class="material-symbols-outlined text-2xl">child_care</span>
                        </div>
                        <div id="status_partida_nacimiento"
                            class="px-2 py-1 rounded bg-orange-500/10 text-orange-400 text-[9px] font-black uppercase tracking-wider">
                            Pendiente</div>
                    </div>
                    <h4 class="text-white font-bold text-sm uppercase mb-1">Partida Nacimiento</h4>
                    <p class="text-white/40 text-[10px] mb-4 leading-tight">Acta de nacimiento actualizada y legible.
                    </p>
                    <label
                        class="block w-full text-center py-2.5 bg-card-dark border border-white/10 rounded-lg text-[10px] font-black text-gold uppercase tracking-widest hover:bg-gold hover:text-primary-dark transition-all cursor-pointer">
                        <span id="label_partida_nacimiento">Subir Archivo</span>
                        <input name="doc_partida_nacimiento" accept="image/*,application/pdf" class="hidden doc-input"
                            data-type="partida_nacimiento" type="file" />
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-6">
            <button type="reset" id="btnCancelRegistration"
                class="px-8 py-4 border border-white/10 rounded-2xl text-xs font-black text-white uppercase tracking-widest hover:bg-white/5 transition-all">
                Limpiar Formulario
            </button>
            <button type="submit" id="btnSubmitRegistration"
                class="px-12 py-4 bg-gold text-primary-dark rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-gold/20 hover:scale-[1.02] transition-all flex items-center gap-3">
                <span class="material-symbols-outlined font-black">how_to_reg</span>
                Finalizar Inscripción
            </button>
        </div>
    </form>

    <script>
        // Previsualización de Foto
        document.getElementById('student_photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('photoPreview');
                    const icon = document.getElementById('photoPlaceholderIcon');
                    const text = document.getElementById('photoPlaceholderText');
                    
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    icon.classList.add('hidden');
                    text.classList.add('hidden');
                    document.getElementById('photoPreviewContainer').classList.remove('border-dashed');
                }
                reader.readAsDataURL(file);
            }
        });

        // Feedback de archivos subidos
        document.querySelectorAll('.doc-input').forEach(input => {
            input.addEventListener('change', function() {
                const type = this.getAttribute('data-type');
                const statusLabel = document.getElementById('status_' + type);
                const textLabel = document.getElementById('label_' + type);
                
                if (this.files && this.files.length > 0) {
                    statusLabel.innerHTML = 'Cargado';
                    statusLabel.classList.remove('bg-orange-500/10', 'text-orange-400');
                    statusLabel.classList.add('bg-green-500/10', 'text-green-400');
                    textLabel.innerHTML = this.files[0].name.substring(0, 15) + '...';
                }
            });
        });

        // Envío del Formulario
        document.getElementById('registrationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btnSubmit = document.getElementById('btnSubmitRegistration');
            const originalText = btnSubmit.innerHTML;
            
            try {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="animate-spin material-symbols-outlined">sync</span> Registrando...';
                
                const response = await fetch('actions/register_student.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    NotificationSystem.show('¡Estudiante inscrito con éxito!', 'success');
                    this.reset();
                    // Limpiar previsualizaciones
                    document.getElementById('photoPreview').classList.add('hidden');
                    document.getElementById('photoPlaceholderIcon').classList.remove('hidden');
                    document.getElementById('photoPlaceholderText').classList.remove('hidden');
                    document.getElementById('photoPreviewContainer').classList.add('border-dashed');
                    
                    document.querySelectorAll('.doc-input').forEach(input => {
                        const type = input.getAttribute('data-type');
                        const statusLabel = document.getElementById('status_' + type);
                        const textLabel = document.getElementById('label_' + type);
                        statusLabel.innerHTML = 'Pendiente';
                        statusLabel.classList.add('bg-orange-500/10', 'text-orange-400');
                        statusLabel.classList.remove('bg-green-500/10', 'text-green-400');
                        textLabel.innerHTML = 'Subir Archivo';
                    });
                } else {
                    NotificationSystem.show(result.message || 'Error en el registro', 'error');
                }
            } catch (error) {
                NotificationSystem.show('Error de conexión al procesar el registro', 'error');
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalText;
            }
        });
    </script>
</div>
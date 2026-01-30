<!-- MODAL VER -->
<div id="viewStudentModal" class="fixed inset-0 z-50 hidden">
    <!-- Fondo -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModal('viewStudentModal')"></div>
    <!-- Contenido -->
    <div
        class="absolute md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 w-full md:w-[700px] h-full md:h-auto bg-primary-dark md:rounded-3xl border md:border-white/10 shadow-2xl overflow-hidden flex flex-col">
        <div class="p-0 overflow-y-auto custom-scrollbar flex-1">
            <!-- Profile Header / Photo -->
            <div
                class="relative h-48 bg-gradient-to-br from-primary-dark via-black to-primary-dark border-b border-white/10 flex items-center justify-center overflow-hidden">
                <div
                    class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10">
                </div>
                <div class="relative">
                    <div
                        class="size-32 rounded-full bg-card-dark border-4 border-gold/50 shadow-2xl overflow-hidden flex items-center justify-center text-5xl font-bold text-gold">
                        <span id="view_avatar_initials">--</span>
                        <img id="view_avatar_preview" class="w-full h-full object-cover hidden">
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-10">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Info Personal -->
                    <div class="lg:col-span-2 space-y-8">
                        <section>
                            <div class="flex items-center gap-3 mb-5 border-b border-white/5 pb-2">
                                <span class="material-symbols-outlined text-gold text-lg">person</span>
                                <h4 class="text-xs font-black text-white uppercase tracking-[0.2em]">Perfil del
                                    Estudiante</h4>
                            </div>
                            <div class="grid grid-cols-2 gap-y-6">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-white/30 uppercase tracking-widest">Nombre
                                        Completo</p>
                                    <p class="text-white font-bold" id="view_fullname">--</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-white/30 uppercase tracking-widest">Cédula de
                                        Identidad</p>
                                    <p class="text-white font-mono font-bold" id="view_cedula">--</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-white/30 uppercase tracking-widest">Fecha de
                                        Nacimiento</p>
                                    <p class="text-white/80 text-sm" id="view_birth_date">--</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-white/30 uppercase tracking-widest">Estado
                                        Académico</p>
                                    <span id="view_status_badge"
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase">--</span>
                                </div>
                                <div class="col-span-2 space-y-1">
                                    <p class="text-[9px] font-black text-white/30 uppercase tracking-widest">Lugar de
                                        Nacimiento</p>
                                    <p class="text-white/80 text-sm" id="view_birth_place">--</p>
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="flex items-center gap-3 mb-5 border-b border-white/5 pb-2">
                                <span class="material-symbols-outlined text-gold text-lg">contact_mail</span>
                                <h4 class="text-xs font-black text-white uppercase tracking-[0.2em]">Contacto y
                                    Ubicación</h4>
                            </div>
                            <div class="grid grid-cols-2 gap-y-6">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-white/30 uppercase tracking-widest">Teléfono
                                        Movil</p>
                                    <p class="text-white/80 text-sm" id="view_phone">--</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-white/30 uppercase tracking-widest">Correo
                                        Electrónico</p>
                                    <p class="text-white/80 text-sm italic" id="view_email">--</p>
                                </div>
                                <div class="col-span-2 space-y-1">
                                    <p class="text-[9px] font-black text-white/30 uppercase tracking-widest">Dirección
                                        de Domicilio</p>
                                    <p class="text-white/80 text-sm leading-relaxed" id="view_address">--</p>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Sidebar: Documentos -->
                    <div class="space-y-6">
                        <div
                            class="bg-black/20 rounded-2xl border border-white/5 p-6 flex flex-col h-full min-h-[300px]">
                            <h4 class="text-[10px] font-black text-gold uppercase tracking-[0.2em] mb-4">Expediente
                                Digital</h4>
                            <div id="view_docs_container"
                                class="space-y-3 flex-1 overflow-y-auto custom-scrollbar pr-2">
                                <div class="animate-pulse flex flex-col gap-3">
                                    <div class="h-10 bg-white/5 rounded-lg"></div>
                                    <div class="h-10 bg-white/5 rounded-lg"></div>
                                </div>
                            </div>
                            <div class="mt-6 pt-6 border-t border-white/5">
                                <p class="text-[8px] text-white/20 uppercase font-bold text-center">Inscripción validada
                                    vía Supabase Storage</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDITAR -->
<div id="editStudentModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModal('editStudentModal')"></div>
    <div
        class="absolute md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 w-full md:w-[700px] h-full md:h-auto bg-primary-dark md:rounded-3xl border md:border-white/10 shadow-2xl overflow-hidden flex flex-col">
        <div class="p-6 border-b border-white/10 flex justify-between items-center bg-black/20">
            <h3 class="text-lg font-black text-white uppercase tracking-tight">Editar <span
                    class="text-gold">Estudiante</span></h3>
            <button onclick="closeModal('editStudentModal')"
                class="size-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-white/60 hover:text-white transition-all">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form action="actions/update_student.php" method="POST" class="flex-1 flex flex-col max-h-[85vh]">
            <input type="hidden" name="student_id" id="edit_id">

            <div class="p-0 overflow-y-auto custom-scrollbar flex-1">
                <!-- Banner / Avatar Section -->
                <div
                    class="relative h-32 bg-gradient-to-r from-primary-dark to-black border-b border-white/10 flex items-center justify-center">
                    <div class="absolute -bottom-10 left-8 flex items-end gap-4">
                        <div class="relative group cursor-pointer">
                            <div
                                class="size-24 rounded-full bg-card-dark border-4 border-primary-dark shadow-2xl overflow-hidden flex items-center justify-center text-3xl font-bold text-gold">
                                <span id="edit_avatar_initials">--</span>
                                <img id="edit_avatar_preview" class="w-full h-full object-cover hidden">
                                <div
                                    class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-2xl">photo_camera</span>
                                </div>
                            </div>
                            <input type="file" name="foto_perfil" id="edit_photo_input" accept="image/*"
                                class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                        <div class="mb-4">
                            <h4 class="text-white font-black text-lg uppercase tracking-tight" id="edit_header_name">
                                Nombre de Estudiante</h4>
                            <p class="text-gold text-[10px] font-black uppercase tracking-[0.2em]"
                                id="edit_header_cedula">Cédula: --</p>
                        </div>
                    </div>
                </div>

                <div class="p-10 pt-16 space-y-8">
                    <!-- Personal Info -->
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="size-6 rounded-lg bg-gold/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-gold text-sm font-black">person</span>
                            </span>
                            <h4 class="text-xs font-black text-white uppercase tracking-[0.2em]">Información Personal
                            </h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label
                                    class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Nombres</label>
                                <input type="text" name="nombres" id="edit_nombres" required
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label
                                    class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Apellidos</label>
                                <input type="text" name="apellidos" id="edit_apellidos" required
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5 text-white/30 cursor-not-allowed">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Cédula
                                    (No editable)</label>
                                <input type="text" name="cedula" id="edit_cedula" readonly
                                    class="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-white/40 outline-none">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Fecha
                                    de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" id="edit_fecha_nacimiento"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Lugar
                                    de Nacimiento</label>
                                <input type="text" name="lugar_nacimiento" id="edit_lugar_nacimiento"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                            </div>
                        </div>
                    </section>

                    <!-- Contact Info -->
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="size-6 rounded-lg bg-gold/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-gold text-sm font-black">mail</span>
                            </span>
                            <h4 class="text-xs font-black text-white uppercase tracking-[0.2em]">Contacto y Acceso</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label
                                    class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Teléfono</label>
                                <input type="tel" name="telefono" id="edit_telefono"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Correo
                                    Electrónico</label>
                                <input type="email" name="correo" id="edit_correo"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Login
                                    (Usuario)</label>
                                <input type="text" name="usuario_login" id="edit_usuario_login"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Nueva
                                    Clave (Opcional)</label>
                                <input type="password" name="nueva_clave" placeholder="Dejar en blanco..."
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5 md:col-span-2">
                                <label
                                    class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Dirección
                                    Completa</label>
                                <textarea name="direccion" id="edit_direccion" rows="2"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none transition-all resize-none"></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Academic Status -->
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="size-6 rounded-lg bg-gold/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-gold text-sm font-black">school</span>
                            </span>
                            <h4 class="text-xs font-black text-white uppercase tracking-[0.2em]">Estado Académico</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Año
                                    Actual</label>
                                <select name="anio_actual" id="edit_anio"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none cursor-pointer">
                                    <option value="1">1er Año</option>
                                    <option value="2">2do Año</option>
                                    <option value="3">3er Año</option>
                                    <option value="4">4to Año</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label
                                    class="text-[9px] font-black text-white/40 uppercase tracking-widest ml-1">Situación
                                    Administrativa</label>
                                <select name="estado_id" id="edit_estado"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-gold/50 outline-none cursor-pointer">
                                    <option value="1">Activo</option>
                                    <option value="2">Suspendido</option>
                                    <option value="3">Retirado</option>
                                </select>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="p-6 border-t border-white/10 bg-black/20 flex justify-end gap-4">
                <button type="button" onclick="closeModal('editStudentModal')"
                    class="px-6 py-2 rounded-lg border border-white/10 text-white/60 hover:text-white font-bold text-xs uppercase tracking-widest transition-all">Cancelar</button>
                <button type="submit"
                    class="px-6 py-2 rounded-lg bg-gold text-primary-dark font-black text-xs uppercase tracking-widest hover:bg-white transition-all shadow-lg">Guardar
                    Cambios</button>
            </div>
        </form>
    </div>
</div>
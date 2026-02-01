
import { supabase } from '../../../config/supabase-client.js';

let isInitialized = false;

export async function initSettings() {
    if (isInitialized) return;
    console.log('[Settings] Module Initialized');

    await loadSettings();
    setupEventListeners();

    isInitialized = true;
}

async function loadSettings() {
    try {
        const sedeId = window.adminContext?.sedeId;
        if (!sedeId) {
            console.warn('[Settings] No Sede ID found in context.');
            return;
        }

        const { data, error } = await supabase
            .from('configuraciones')
            .select('*')
            .eq('sede_id', sedeId);

        if (error) {
            console.warn('[Settings] Error loading configs:', error);
            return;
        }

        if (!data) return;

        // Map data to UI
        const configMap = {};
        data.forEach(item => {
            configMap[item.clave] = item.valor;
        });

        // Toggles
        setToggle('toggle_inscripciones', configMap['inscripciones_abiertas']);
        setToggle('toggle_notas', configMap['carga_notas_abierta']);
        setToggle('toggle_horarios', configMap['edicion_horarios_abierta']);

        // Inputs
        setInput('conf_nombre_sede', configMap['nombre_sede']);
        setInput('conf_periodo', configMap['periodo_actual']);

    } catch (e) {
        console.error(e);
    }
}

function setToggle(id, value) {
    const el = document.getElementById(id);
    if (el) el.checked = (value === 'true');
}

function setInput(id, value) {
    const el = document.getElementById(id);
    if (el && value) el.value = value;
}

function setupEventListeners() {
    // Toggles (Auto Save)
    setupToggleListener('toggle_inscripciones', 'inscripciones_abiertas');
    setupToggleListener('toggle_notas', 'carga_notas_abierta');
    setupToggleListener('toggle_horarios', 'edicion_horarios_abierta');

    // Forms
    const formInst = document.getElementById('form_institution');
    if (formInst) {
        formInst.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = formInst.querySelector('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Guardando...';
            btn.disabled = true;

            await saveSetting('nombre_sede', document.getElementById('conf_nombre_sede').value);
            await saveSetting('periodo_actual', document.getElementById('conf_periodo').value);

            btn.innerHTML = 'Guardado!';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 1500);
        });
    }

    // Security Form
    const formSec = document.getElementById('form_security');
    if (formSec) {
        formSec.addEventListener('submit', async (e) => {
            e.preventDefault();
            const pass = document.getElementById('conf_new_pass').value;
            if (!pass) return;

            if (!confirm('¿Estás seguro de cambiar tu contraseña?')) return;

            const { error } = await supabase.auth.updateUser({ password: pass });
            if (error) {
                alert('Error: ' + error.message);
            } else {
                alert('Contraseña actualizada correctamente');
                document.getElementById('conf_new_pass').value = '';
            }
        });
    }
}

function setupToggleListener(elementId, dbKey) {
    const el = document.getElementById(elementId);
    if (el) {
        el.addEventListener('change', async () => {
            await saveSetting(dbKey, el.checked.toString());
        });
    }
}

async function saveSetting(key, value) {
    const sedeId = window.adminContext?.sedeId;
    if (!sedeId) {
        alert('Error: No se ha identificado la sede del administrador.');
        return;
    }

    try {
        // Upsert based on clave AND sede_id
        // NOTE: This requires a unique constraint on (clave, sede_id) in the DB
        const { error } = await supabase
            .from('configuraciones')
            .upsert({
                clave: key,
                valor: value,
                sede_id: sedeId
            }, {
                onConflict: 'clave, sede_id', // Needs composite key
                ignoreDuplicates: false
            });

        if (error) throw error;
    } catch (e) {
        console.error('Error saving setting:', e);
        // Fallback or alert if table structure is old
        alert('Error al guardar. Asegúrate de actualizar la base de datos (columna sede_id).');
    }
}

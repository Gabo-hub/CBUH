// ============================================
// component-loader.js - Sistema de Carga de Componentes
// ============================================
// Permite cargar componentes HTML de forma dinámica
// Implementa caching para mejorar performance
// Soporta lazy loading de tabs y modales

/**
 * Cache de componentes cargados
 * @type {Map<string, string>}
 */
const componentCache = new Map();

/**
 * Set de tabs ya cargados (para evitar recargas)
 * @type {Set<string>}
 */
const loadedTabs = new Set();

/**
 * Contador de componentes cargados (para debugging)
 * @type {number}
 */
let componentsLoaded = 0;

/**
 * Carga un componente HTML desde una ruta y lo inserta en un contenedor
 * @param {string} path - Ruta relativa del componente (ej: './components/modals/new-teacher-modal.html')
 * @param {string} targetId - ID del elemento donde insertar el HTML
 * @param {boolean} useCache - Si debe usar cache o forzar recarga
 * @returns {Promise<void>}
 * @throws {Error} Si no se puede cargar el componente
 */
export async function loadComponent(path, targetId, useCache = true) {
    try {
        console.log(`[ComponentLoader] Loading: ${path} into #${targetId}`);

        // Verificar si el target existe
        const target = document.getElementById(targetId);
        if (!target) {
            throw new Error(`Target element #${targetId} not found`);
        }

        // Usar cache si está disponible
        if (useCache && componentCache.has(path)) {
            console.log(`[ComponentLoader] Using cached version of ${path}`);
            target.innerHTML = componentCache.get(path);
            return;
        }

        // Cargar componente desde el servidor
        const response = await fetch(path);

        if (!response.ok) {
            throw new Error(`Failed to load component: ${response.status} ${response.statusText}`);
        }

        const html = await response.text();

        // Guardar en cache si está habilitado
        if (useCache) {
            componentCache.set(path, html);
            console.log(`[ComponentLoader] Cached ${path}`);
        }

        // Insertar en el DOM
        target.innerHTML = html;
        componentsLoaded++;

        console.log(`[ComponentLoader] ✓ Loaded ${path} (${componentsLoaded} total)`);

    } catch (error) {
        console.error(`[ComponentLoader] ✗ Error loading ${path}:`, error);

        // Mostrar mensaje de error en el contenedor
        const target = document.getElementById(targetId);
        if (target) {
            target.innerHTML = `
                <div class="p-8 text-center">
                    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-6">
                        <span class="material-symbols-outlined text-red-400 text-4xl">error</span>
                        <p class="text-red-400 font-bold mt-4">Error al cargar componente</p>
                        <p class="text-white/40 text-sm mt-2">${error.message}</p>
                    </div>
                </div>
            `;
        }

        throw error;
    }
}

/**
 * Carga un modal específico en el contenedor de modales
 * Los modales siempre se recargan (no usan cache) para evitar problemas con event listeners
 * @param {string} modalName - Nombre del modal sin extensión (ej: 'new-teacher-modal')
 * @returns {Promise<void>}
 */
export async function loadModal(modalName) {
    console.log(`[ComponentLoader] Loading modal: ${modalName}`);

    // Asegurarse de que existe el contenedor de modales
    let modalContainer = document.getElementById('modals-container');
    if (!modalContainer) {
        modalContainer = document.createElement('div');
        modalContainer.id = 'modals-container';
        document.body.appendChild(modalContainer);
    }

    // Los modales no usan cache para evitar problemas con event listeners
    await loadComponent(
        `./components/modals/${modalName}.html`,
        'modals-container',
        false // No cache
    );
}

/**
 * Carga un tab específico con lazy loading
 * Solo carga el tab la primera vez que se accede
 * @param {string} tabName - Nombre del tab (ej: 'profesores', 'students')
 * @returns {Promise<void>}
 */
export async function loadTab(tabName) {
    // Si ya está cargado, no hacer nada
    if (loadedTabs.has(tabName)) {
        console.log(`[ComponentLoader] Tab ${tabName} already loaded, skipping`);
        return;
    }

    console.log(`[ComponentLoader] Loading tab: ${tabName}`);

    // Cargar el contenido del tab
    await loadComponent(
        `./components/tabs/${tabName}-tab.html`,
        `tab-${tabName}`,
        true // Usar cache
    );

    // Marcar como cargado
    loadedTabs.add(tabName);
}

/**
 * Pre-carga un componente en background (útil para tabs frecuentes)
 * @param {string} path - Ruta del componente a pre-cargar
 * @returns {Promise<void>}
 */
export async function preloadComponent(path) {
    if (componentCache.has(path)) {
        console.log(`[ComponentLoader] Component ${path} already in cache`);
        return;
    }

    console.log(`[ComponentLoader] Preloading: ${path}`);

    try {
        const response = await fetch(path);
        if (response.ok) {
            const html = await response.text();
            componentCache.set(path, html);
            console.log(`[ComponentLoader] ✓ Preloaded ${path}`);
        }
    } catch (error) {
        console.warn(`[ComponentLoader] Failed to preload ${path}:`, error);
    }
}

/**
 * Limpia el cache de componentes
 * Útil para forzar recarga después de actualizaciones
 * @param {string} [specificPath] - Si se especifica, solo limpia ese componente
 */
export function clearCache(specificPath = null) {
    if (specificPath) {
        componentCache.delete(specificPath);
        console.log(`[ComponentLoader] Cleared cache for: ${specificPath}`);
    } else {
        componentCache.clear();
        loadedTabs.clear();
        console.log(`[ComponentLoader] Cleared all cache`);
    }
}

/**
 * Obtiene estadísticas del cache
 * @returns {Object} Estadísticas del cache
 */
export function getCacheStats() {
    return {
        componentsInCache: componentCache.size,
        loadedTabs: Array.from(loadedTabs),
        totalComponentsLoaded: componentsLoaded
    };
}

// Exponer funciones globalmente para uso en onclick handlers y otros scripts
window.loadComponent = loadComponent;
window.loadModal = loadModal;
window.loadTab = loadTab;
window.preloadComponent = preloadComponent;
window.clearComponentCache = clearCache;
window.getComponentCacheStats = getCacheStats;

console.log('[ComponentLoader] ✓ Component loader initialized');

/**
 * Cliente WebSocket del escenario.
 * El backend es la fuente de verdad; aquí solo se representa el estado
 * recibido en tiempo real (nombre de presentación, categoría, fase, estado y cronómetro).
 */
export function inicializarEscenario() {
    const raiz = document.querySelector('[data-escenario-raiz], [data-escenario-echo]');

    if (!raiz || !window.Echo) {
        return;
    }

    const activoEl = raiz.querySelector('[data-escenario-activo]');
    const vacioEl = raiz.querySelector('[data-escenario-vacio]');
    const estadoEl = raiz.querySelector('[data-estado]');
    const categoriaFaseEl = raiz.querySelector('[data-categoria-fase]');
    const nombreEl = raiz.querySelector('[data-nombre]');
    const displayEl = raiz.querySelector('[data-cronometro-display]');
    const tiempoMaxDisplayEl = raiz.querySelector('[data-tiempo-max-display]');
    const tiempoMaxTextoEl = raiz.querySelector('[data-tiempo-max-texto]');

    let presentacionId = parseInt(raiz.dataset.escenarioId || raiz.dataset.escenarioEcho || '0', 10) || null;
    let corriendo = raiz.dataset.cronometroCorriendo === '1';
    let segundosRestantes = parseInt(raiz.dataset.segundosRestantes || '0', 10);
    let tiempoMaximo = parseInt(raiz.dataset.tiempoMaximo || '10', 10);

    function formatear(segundos) {
        const seguros = Math.max(0, Math.ceil(segundos));
        const horas = Math.floor(seguros / 3600);
        const minutos = Math.floor((seguros % 3600) / 60);
        const segs = seguros % 60;
        const mmss = `${String(minutos).padStart(2, '0')}:${String(segs).padStart(2, '0')}`;

        return horas > 0 ? `${String(horas).padStart(2, '0')}:${mmss}` : mmss;
    }

    function renderizar() {
        if (displayEl) {
            displayEl.textContent = formatear(segundosRestantes);
            displayEl.classList.toggle('text-red-400', corriendo && segundosRestantes <= 30);
        }
    }

    function actualizarEstilosEstado(estado) {
        if (!estadoEl) return;
        
        // Quitar clases previas de color
        estadoEl.classList.remove(
            'bg-emerald-500/20', 'text-emerald-300',
            'bg-amber-500/20', 'text-amber-300',
            'bg-slate-500/20', 'text-slate-300',
            'bg-indigo-500/20', 'text-indigo-300'
        );

        if (estado === 'PARTICIPANDO') {
            estadoEl.classList.add('bg-emerald-500/20', 'text-emerald-300');
        } else if (estado === 'EN_ESCENARIO') {
            estadoEl.classList.add('bg-amber-500/20', 'text-amber-300');
        } else {
            estadoEl.classList.add('bg-slate-500/20', 'text-slate-300');
        }
    }

    function aplicarCronometro(datos) {
        if (typeof datos.corriendo === 'boolean') {
            corriendo = datos.corriendo;
        }
        if (typeof datos.segundos_restantes === 'number') {
            segundosRestantes = datos.segundos_restantes;
        }
        if (typeof datos.tiempo_maximo === 'number') {
            tiempoMaximo = datos.tiempo_maximo;
            if (tiempoMaxDisplayEl) {
                tiempoMaxDisplayEl.textContent = `/ ${tiempoMaximo}:00`;
            }
            if (tiempoMaxTextoEl) {
                tiempoMaxTextoEl.textContent = `Tiempo máximo de presentación: ${tiempoMaximo} minuto(s)`;
            }
        }
        renderizar();
    }

    function aplicarEstadoCambiado(datos) {
        if (!datos || !datos.presentacion_id) return;

        presentacionId = parseInt(datos.presentacion_id, 10);

        // Si la presentación pasa a estar en escenario o participando o finalizada, mostrar el bloque activo
        if (datos.estado === 'EN_ESCENARIO' || datos.estado === 'PARTICIPANDO' || datos.estado === 'FINALIZADO') {
            if (activoEl) activoEl.classList.remove('hidden');
            if (vacioEl) vacioEl.classList.add('hidden');
        }

        if (estadoEl) {
            estadoEl.textContent = datos.estado_label ?? datos.estado ?? '';
            actualizarEstilosEstado(datos.estado);
        }

        if (nombreEl && datos.nombre_presentacion) {
            nombreEl.textContent = datos.nombre_presentacion;
        }

        if (categoriaFaseEl) {
            const cat = datos.categoria || '';
            const fase = datos.fase_label || datos.fase || '';
            categoriaFaseEl.textContent = cat && fase ? `${cat} · ${fase}` : (cat || fase || '');
        }

        if (typeof datos.tiempo_maximo === 'number') {
            tiempoMaximo = datos.tiempo_maximo;
            if (tiempoMaxDisplayEl) {
                tiempoMaxDisplayEl.textContent = `/ ${tiempoMaximo}:00`;
            }
            if (tiempoMaxTextoEl) {
                tiempoMaxTextoEl.textContent = `Tiempo máximo de presentación: ${tiempoMaximo} minuto(s)`;
            }
        }

        // Actualizar cronómetro según el estado recibido
        if (typeof datos.corriendo === 'boolean') {
            corriendo = datos.corriendo;
        } else if (datos.estado === 'PARTICIPANDO') {
            corriendo = true;
        } else if (datos.estado === 'EN_ESCENARIO' || datos.estado === 'FINALIZADO') {
            corriendo = false;
        }

        if (typeof datos.segundos_restantes === 'number') {
            segundosRestantes = datos.segundos_restantes;
        } else if (datos.estado === 'EN_ESCENARIO') {
            segundosRestantes = tiempoMaximo * 60;
        }

        renderizar();
    }

    // Intervalo de decremento segundo a segundo
    window.setInterval(() => {
        if (!corriendo) {
            return;
        }
        if (segundosRestantes > 0) {
            segundosRestantes -= 1;
        }
        renderizar();
    }, 1000);

    // Escuchar el canal privado 'presentaciones'
    window.Echo.private('presentaciones')
        .listen('.presentacion.estado.cambiado', (datos) => {
            aplicarEstadoCambiado(datos);
        })
        .listen('.cronometro.actualizado', (datos) => {
            if (presentacionId && parseInt(datos.presentacion_id, 10) !== presentacionId) {
                return;
            }
            aplicarCronometro(datos);
            if (datos.estado_label && estadoEl) {
                estadoEl.textContent = datos.estado_label;
                actualizarEstilosEstado(datos.estado);
            }
            if (datos.nombre_presentacion && nombreEl) {
                nombreEl.textContent = datos.nombre_presentacion;
            }
        });

    renderizar();
}
/**
 * Cliente WebSocket del escenario y evaluación de jurados.
 * Sincroniza en tiempo real los datos de la presentación, el cronómetro principal,
 * la habilitación dinámica de calificación y el aviso cuando se llama al siguiente participante.
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
    const progressEl = raiz.querySelector('[data-cronometro-progress]');
    const avisoFinalizadoEl = raiz.querySelector('[data-aviso-finalizado]');

    // Elementos del Aviso de Siguiente Participante
    const avisoSiguienteModal = document.getElementById('avisoSiguienteParticipante');
    const avisoSiguienteTexto = document.getElementById('avisoSiguienteTexto');
    const avisoSiguienteContador = document.getElementById('avisoSiguienteContador');

    let presentacionId = parseInt(raiz.dataset.escenarioId || raiz.dataset.escenarioEcho || '0', 10) || null;
    let corriendo = raiz.dataset.cronometroCorriendo === '1';
    let segundosRestantes = parseInt(raiz.dataset.segundosRestantes || '0', 10);
    let tiempoMaximo = parseInt(raiz.dataset.tiempoMaximo || '10', 10);
    let recargaPendiente = false;

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
            displayEl.classList.toggle('text-rose-400', corriendo && segundosRestantes <= 30);
            displayEl.classList.toggle('text-white', !corriendo || segundosRestantes > 30);
        }

        if (progressEl) {
            const totalSeg = Math.max(1, tiempoMaximo * 60);
            const porcentaje = Math.max(0, Math.min(100, (segundosRestantes / totalSeg) * 100));
            progressEl.style.width = `${porcentaje}%`;
        }
    }

    function actualizarEstilosEstado(estado) {
        if (!estadoEl) return;
        
        estadoEl.classList.remove(
            'bg-emerald-500/20', 'text-emerald-300', 'border-emerald-500/30',
            'bg-amber-500/20', 'text-amber-300', 'border-amber-500/30',
            'bg-slate-500/20', 'text-slate-300', 'border-slate-600',
            'bg-rose-500/20', 'text-rose-300', 'border-rose-500/30'
        );

        if (estado === 'PARTICIPANDO') {
            estadoEl.classList.add('bg-emerald-500/20', 'text-emerald-300', 'border', 'border-emerald-500/30');
        } else if (estado === 'EN_ESCENARIO') {
            estadoEl.classList.add('bg-amber-500/20', 'text-amber-300', 'border', 'border-amber-500/30');
        } else if (estado === 'FINALIZADO') {
            estadoEl.classList.add('bg-slate-500/20', 'text-slate-300', 'border', 'border-slate-600');
        } else {
            estadoEl.classList.add('bg-slate-500/20', 'text-slate-300', 'border', 'border-slate-600');
        }
    }

    const btnCalificar = document.getElementById('btnConfirmarCalificacion');
    const btnCalificarTexto = document.getElementById('btnCalificarTexto');

    function habilitarBotonCalificar() {
        if (!btnCalificar) return;
        btnCalificar.disabled = false;
        btnCalificar.classList.remove('bg-slate-800', 'border', 'border-slate-700', 'text-slate-400', 'cursor-not-allowed', 'opacity-60');
        btnCalificar.classList.add('bg-gradient-to-r', 'from-[#5c0618]', 'to-rose-700', 'hover:from-[#720a20]', 'hover:to-rose-600', 'text-white', 'cursor-pointer', 'shadow-lg');
        if (btnCalificarTexto) {
            btnCalificarTexto.textContent = 'Registrar calificación oficial';
        }
        const spanIcono = btnCalificar.querySelector('span:first-child');
        if (spanIcono) spanIcono.textContent = '✓';
    }

    function deshabilitarBotonCalificar() {
        if (!btnCalificar) return;
        btnCalificar.disabled = true;
        btnCalificar.classList.remove('bg-gradient-to-r', 'from-[#5c0618]', 'to-rose-700', 'hover:from-[#720a20]', 'hover:to-rose-600', 'text-white', 'cursor-pointer', 'shadow-lg');
        btnCalificar.classList.add('bg-slate-800', 'border', 'border-slate-700', 'text-slate-400', 'cursor-not-allowed', 'opacity-60');
        if (btnCalificarTexto) {
            btnCalificarTexto.textContent = 'Esperando inicio de presentación para calificar';
        }
        const spanIcono = btnCalificar.querySelector('span:first-child');
        if (spanIcono) spanIcono.textContent = '⏳';
    }

    function aplicarCronometro(datos) {
        if (typeof datos.corriendo === 'boolean') {
            corriendo = datos.corriendo;
            if (corriendo) {
                habilitarBotonCalificar();
            }
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

        // Si el cronómetro fue detenido o finalizado
        if (!corriendo && datos.cronometro_finalizado_at) {
            if (estadoEl) {
                estadoEl.textContent = 'Finalizado';
                actualizarEstilosEstado('FINALIZADO');
            }
            if (avisoFinalizadoEl) {
                avisoFinalizadoEl.classList.remove('hidden');
                avisoFinalizadoEl.classList.add('block');
            }
            habilitarBotonCalificar();
        }

        renderizar();
    }

    function notificarSiguienteParticipante(datos) {
        if (recargaPendiente) return;
        recargaPendiente = true;

        if (avisoSiguienteModal) {
            if (avisoSiguienteTexto && datos.nombre_presentacion) {
                avisoSiguienteTexto.textContent = `El administrador ha llamado a "${datos.nombre_presentacion}" al escenario.`;
            }
            avisoSiguienteModal.classList.remove('hidden');

            let segundosEspera = 4;
            const intervaloAviso = window.setInterval(() => {
                segundosEspera -= 1;
                if (avisoSiguienteContador) {
                    avisoSiguienteContador.textContent = `Cargando en ${segundosEspera}s...`;
                }
                if (segundosEspera <= 0) {
                    window.clearInterval(intervaloAviso);
                    window.location.reload();
                }
            }, 1000);
        } else {
            window.location.reload();
        }
    }

    function aplicarEstadoCambiado(datos) {
        if (!datos || !datos.presentacion_id) return;

        const nuevoId = parseInt(datos.presentacion_id, 10);

        // Si cambió la presentación en el escenario, avisar al jurado para que verifique su nota
        // y recargar en 4 segundos
        if (presentacionId && nuevoId !== presentacionId) {
            notificarSiguienteParticipante(datos);
            return;
        }

        presentacionId = nuevoId;

        // Mostrar bloque activo
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
            const depto = datos.departamento ? ` · ${datos.departamento}` : '';
            categoriaFaseEl.textContent = cat && fase ? `${cat} · ${fase}${depto}` : (cat || fase || '');
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

        // Gestión del estado
        if (datos.estado === 'FINALIZADO') {
            corriendo = false;
            segundosRestantes = 0;
            if (avisoFinalizadoEl) {
                avisoFinalizadoEl.classList.remove('hidden');
                avisoFinalizadoEl.classList.add('block');
            }
            habilitarBotonCalificar();
        } else if (datos.estado === 'PARTICIPANDO') {
            corriendo = true;
            if (avisoFinalizadoEl) {
                avisoFinalizadoEl.classList.add('hidden');
                avisoFinalizadoEl.classList.remove('block');
            }
            habilitarBotonCalificar();
        } else if (datos.estado === 'EN_ESCENARIO') {
            corriendo = false;
            segundosRestantes = tiempoMaximo * 60;
            if (avisoFinalizadoEl) {
                avisoFinalizadoEl.classList.add('hidden');
                avisoFinalizadoEl.classList.remove('block');
            }
            deshabilitarBotonCalificar();
        }

        if (typeof datos.segundos_restantes === 'number') {
            segundosRestantes = datos.segundos_restantes;
        }

        renderizar();
    }

    // Intervalo de decremento segundo a segundo
    window.setInterval(() => {
        // Cronómetro de presentación
        if (corriendo) {
            if (segundosRestantes > 1) {
                segundosRestantes -= 1;
            } else if (segundosRestantes === 1) {
                segundosRestantes = 0;
                corriendo = false;
                
                // Actualizar estado visual a FINALIZADO
                if (estadoEl) {
                    estadoEl.textContent = 'Finalizado';
                    actualizarEstilosEstado('FINALIZADO');
                }
                if (avisoFinalizadoEl) {
                    avisoFinalizadoEl.classList.remove('hidden');
                    avisoFinalizadoEl.classList.add('block');
                }
                habilitarBotonCalificar();
            }
            renderizar();
        }
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
        })
        .listen('.calificacion.registrada', (datos) => {
            if (presentacionId && parseInt(datos.presentacion_id, 10) === presentacionId) {
                const juradoItem = document.querySelector(`[data-jurado-usuario-id="${datos.jurado_id}"]`);
                if (juradoItem) {
                    const badge = juradoItem.querySelector('[data-jurado-estado-badge]');
                    if (badge) {
                        badge.textContent = 'Voto emitido';
                        badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300';
                    }
                }
            }
        });

    renderizar();
}
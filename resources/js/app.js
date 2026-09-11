import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

import { inicializarEscenario } from './escenario-echo';

document.addEventListener('DOMContentLoaded', inicializarEscenario);

Alpine.start();
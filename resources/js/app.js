import './bootstrap';

import Alpine from 'alpinejs';

import focus from '@alpinejs/focus';
import agenda from './agenda';

window.Alpine = Alpine;

Alpine.plugin(focus);
Alpine.data('agenda', agenda);

Alpine.start();

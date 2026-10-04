import './bootstrap';

import intersect from '@alpinejs/intersect';
import eliteSearch from './search';

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(intersect);
    window.Alpine.data('eliteSearch', eliteSearch);
});

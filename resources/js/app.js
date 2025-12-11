import './bootstrap';
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import Chart from 'chart.js/auto';

// Make Alpine available globally
window.Alpine = Alpine;
window.Sortable = Sortable;
window.Chart = Chart;

// Start Alpine
Alpine.start();

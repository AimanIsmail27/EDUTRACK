import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse'; // Import the plugin

Alpine.plugin(collapse); // Register the plugin

window.Alpine = Alpine;
Alpine.start();
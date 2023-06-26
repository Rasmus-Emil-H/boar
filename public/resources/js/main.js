import utilities from './modules/utilities.js';
import components from './modules/components.js';
import http from './modules/http.js';
import behavior from './modules/behaviour.js';

document.addEventListener('DOMContentLoaded', function() {
  window.boar = {
    utilities,
    components,
    http
  }
  behavior.init();
});
/**
 * JS
 * Split properly
 */

import serviceWorker from "./modules/serviceworkerInit.js";
import utilities     from "./modules/utilities.js";
import components    from "./modules/components.js";
import http          from "./modules/http.js";
import behavior      from "./modules/behaviour.js";

document.addEventListener("DOMContentLoaded", function() {

  serviceWorker.init();
  
  window.boar = {
    utilities,
    components,
    http,
    serviceWorker
  }

  /**
   * Init application requirements
   */

  behavior.init();

});
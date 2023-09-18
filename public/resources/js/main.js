/**
 * JS
 * Split properly
 */

import serviceWorker from "./modules/serviceworkerInit.js";
import utilities     from "./modules/utilities.js";
import components    from "./modules/components.js";
import http          from "./modules/http.js";
import behavior      from "./modules/behaviour.js";
import indexedDB     from "./modules/indexedDB.js";

document.addEventListener("DOMContentLoaded", function() {

  /**
   * Register serviceworker
   */
  
  serviceWorker.init();
  
  window.boar = {
    utilities,
    components,
    http,
    serviceWorker,
    indexedDB
  }

  /**
   * Init application requirements
   */

  behavior.init();

});
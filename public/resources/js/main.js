/**
 * JS
 * Split properly
 */

import serviceWorker from "./modules/serviceworkerInit.js";
import utilities     from "./modules/utilities.js";
import components    from "./modules/components.js";
import http          from "./modules/http.js";
import behavior      from "./modules/behaviour.js";
import geolocation   from "./modules/geolocation.js";
import bluetooth     from "./modules/bluetooth.js";

document.addEventListener("DOMContentLoaded", function() {

  serviceWorker.init();
  
  window.boar = {
    utilities,
    components,
    http,
    serviceWorker,
    geolocation,
    bluetooth
  }

  /**
   * Init application requirements
   */

  behavior.init();
  geolocation.init();

});
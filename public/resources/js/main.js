/**
 * JS
 * Split properly
 */

const modulesToImport = [
  "../../serviceworkerInit.js",
  "./modules/utilities.js",
  "./modules/components.js",
  "./modules/http.js",
  "./modules/behaviour.js"
];

document.addEventListener("DOMContentLoaded", async function() {

  window.boar = {};
  
  for (const modulePath of modulesToImport) {
    try {
      const module = await import(modulePath);
      const moduleName = modulePath.split('/').pop().replace('.js', '');
      window.boar[moduleName] = module.default;
    } catch (error) {
      console.log(error);
    }
  }

  // await window.boar.serviceworkerInit.init();
  await window.boar.behaviour.init();

});
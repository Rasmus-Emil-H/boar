/**
|----------------------------------------------------------------------------
| Javascript bootstrap
|----------------------------------------------------------------------------
|
| @author RE_WEB
|
*/

const modulesToImport = [
  "../../serviceworkerInit.js",
  "./modules/utilities.js",
  "./modules/components.js",
  "./modules/behaviour.js",
  "./modules/websocket.js"
];

document.addEventListener("DOMContentLoaded", async function() {

  window.boar = {};
  
    for (const modulePath of modulesToImport) {
        try {
            const module = await import(modulePath);
            const moduleName = modulePath.split('/').pop().replace('.js', '');
            window.boar[moduleName] = module.default;
            if (modulePath.includes('websocket')) continue;
            Object.freeze(window.boar[moduleName]);
        } catch (error) {
            
        }
    }

    // await window.boar.serviceworkerInit.init();
    await window.boar.behaviour.init();

});
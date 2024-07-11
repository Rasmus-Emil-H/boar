/**
|----------------------------------------------------------------------------
| Javascript bootstrap
|----------------------------------------------------------------------------
|
| @author RE_WEB
|
*/

const version = '?t=1';

const modulesToImport = [
  "../../serviceworkerInit.js"+version,
  "./modules/utilities.js"+version,
  "./modules/components.js"+version,
  "./modules/behaviour.js"+version,
  "./modules/constants.js"+version,
  "./modules/websocket.js"+version
];

document.addEventListener("DOMContentLoaded", async function() {

  window.boar = {};
  
    for (const modulePath of modulesToImport) {
        try {
            const module = await import(modulePath);
            const moduleName = modulePath.split('/').pop().replace(`.js${version}`, '');
            window.boar[moduleName] = module.default;
            if (modulePath.includes('websocket')) continue;
            Object.freeze(window.boar[moduleName]);
        } catch (error) {
            
        }
    }

    // await window.boar.serviceworkerInit.init();
    await window.boar.behaviour.init();

});
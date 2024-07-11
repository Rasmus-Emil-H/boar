/**
|----------------------------------------------------------------------------
| Javascript bootstrap
|----------------------------------------------------------------------------
|
| @author RE_WEB
|
*/

const version = '?t=1';
const modulesDir = './modules/';

const modulesToImport = [
  `../../serviceworkerInit.js${version}`,
  `${modulesDir}utilities.js${version}`,
  `${modulesDir}components.js${version}`,
  `${modulesDir}behaviour.js${version}`,
  `${modulesDir}constants.js${version}`,
  `${modulesDir}websocket.js${version}`
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
    // await window.autologik.websocket.init();

    await window.boar.behaviour.init();
});
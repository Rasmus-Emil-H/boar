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
const modules = ['utilities', 'components', 'behaviour', 'constants', 'websocket'];
const allModules = modules.map(function(module) {
  return `${modulesDir}${module}.js${version}`;
});

const modulesToImport = [
  `../../serviceworkerInit.js${version}`,
  ...allModules
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
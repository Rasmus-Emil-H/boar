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
const jsExtension = '.js';
const appName = 'boar'

const modules = ['utilities', 'components', 'behaviour', 'constants', 'websocket'];
const allModules = modules.map(function(module) {
  return `${modulesDir}${module}${jsExtension}${version}`;
});

const modulesToImport = [
  `../../serviceworkerInit${jsExtension}${version}`,
  ...allModules
];

document.addEventListener("DOMContentLoaded", async function() {

  window[appName] = {};
  
    for (const modulePath of modulesToImport) {
        try {
            const module = await import(modulePath);
            const moduleName = modulePath.split('/').pop().replace(`${jsExtension}${version}`, '');
            window[appName][moduleName] = module.default;
            if (modulePath.includes('websocket')) continue;
            Object.freeze(window[appName][moduleName]);
        } catch (error) {
            
        }
    }

    // await window[appName].serviceworkerInit.init();
    // await window[appName].websocket.init();

    await window[appName].behaviour.init();
});
/**
|----------------------------------------------------------------------------
| Javascript bootstrap
|----------------------------------------------------------------------------
|
| @author RE_WEB
|
*/

const appName = 'boar';

window[appName] = {};

window[appName].getName = function() {
    return appName;
}

function online() {
    return navigator?.onLine;
}
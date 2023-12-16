let worker = {};

export default {
    init: function() {
        navigator.serviceWorker.register('/serviceworkerInstall.js', {scope: '/'})
            .then(function(registration) {
                console.log('Service Worker registered with scope:', registration.scope);
            })
            .catch(function(error) {
                console.table('Service Worker registration failed:', error, new Date());
            });
    },
    triggerEvent: function(action, resource) {
        if(typeof worker.postMessage === 'function') console.log(worker.postMessage({action, resource}));
    },
    unset: function() {
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for (let registration of registrations) registration.unregister();
        });
    }
}
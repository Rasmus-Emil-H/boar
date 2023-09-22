let worker = {};

export default {
    init: function() {
        const registerServiceWorker = async function() {
            if ("serviceWorker" in navigator) {
                try {
                    const registration = await navigator.serviceWorker.register("/resources/js/modules/serviceworkerInstall.js", {scope: "/resources/js/modules/"});
                    if (registration.active) worker = registration.active;
                    if (registration.installing) console.log("Is installing service worker...");
                } catch (error) {
                    console.error(`Service worker failed: ${error}`);
                }
            }
        };
        registerServiceWorker();
    },
    triggerEvent: function(action, resource) {
        if(typeof worker.postMessage === 'function') console.log(worker.postMessage({action, resource}));
    },
    unset: function() {
        console.log("Unsetting SW");
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for (let registration of registrations) {
              registration.unregister();
            }
        });
        console.log("Done unsetting SW");
    }
}
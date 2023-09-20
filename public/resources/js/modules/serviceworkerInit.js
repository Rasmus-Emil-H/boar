let worker = {};

export default {
    init: function() {
        const registerServiceWorker = async function() {
            if ("serviceWorker" in navigator) {
                try {
                    const registration = await navigator.serviceWorker.register("/resources/js/modules/serviceworkerInstall.js", {scope: "/resources/js/modules/"});
                    if (registration.active) worker = registration.active;
                } catch (error) {
                    console.error(`Registration failed with ${error}`);
                }
            }
        };
        registerServiceWorker();
    },
    triggerEvent: function(action, resource) {
        worker.postMessage({action, resource});
    },
    unset: function() {
        navigator.serviceWorker.getRegistrations().then(function (registrations) {
            for (let registration of registrations) {
              registration.unregister();
            }
        });
    }
}
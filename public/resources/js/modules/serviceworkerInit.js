export default {
    init: function() {
        const registerServiceWorker = async () => {
            if ("serviceWorker" in navigator) {
                try {
                    const registration = await navigator.serviceWorker.register("/resources/js/modules/serviceWorkerInstall.js", {
                        scope: "/resources/js/modules/",
                    });
                    if (registration.installing) {
                        console.log("Service worker installing");
                    } else if (registration.waiting) {
                        console.log("Service worker installed");
                    } else if (registration.active) {
                        console.log("Service worker active");
                    }
                } catch (error) {
                    console.error(`Registration failed with ${error}`);
                }
            }
        };
        registerServiceWorker();
    },
    getCurrentCaches: function() {
        caches.open("v1").then(function(cache) {
            cache.keys().then(function(keys) {
                keys.forEach(function(request) {
                    console.log(request.url);
                });
            });
        });
    }
}
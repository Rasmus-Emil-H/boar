const serviceworkerConfigs = {
    cacheResponseName: "v2",
    networkErrorMessage: "Network error happened",
    responseError: 408
};

const serviceWorkerManager = {
    async addResourcesToCache(resources) {
        const cache = await caches.open(serviceworkerConfigs.cacheResponseName);
        await cache.addAll(resources);
    },
    async putInCache(request, response) {
        const cache = await caches.open(serviceworkerConfigs.cacheResponseName);
        await cache.put(request, response);
    },
    async cacheFirst({request, preloadResponsePromise, fallbackUrl}) {
        const responseFromCache = await caches.match(request);
        if (responseFromCache) return responseFromCache;
    
        const preloadResponse = await preloadResponsePromise;
        if (preloadResponse) {
            putInCache(request, preloadResponse.clone());
            return preloadResponse;
        }
    
        try {
            const responseFromNetwork = await fetch(request);
            putInCache(request, responseFromNetwork.clone());
            return responseFromNetwork;
        } catch (error) {
            const fallbackResponse = await caches.match(fallbackUrl);
            if (fallbackResponse) return fallbackResponse;
            return new Response(serviceworkerConfigs.networkErrorMessage, {
                status: serviceworkerConfigs.responseError,
                headers: { "Content-Type": "text/plain" }
            });
        }
    },
    async enableNavigationPreload() {
        if (self.registration.navigationPreload) await self.registration.navigationPreload.enable();
    },
    async deleteCache(key) {
        await caches.delete(key);
    },
    async deleteOldCaches() {
        const cacheKeepList = [serviceworkerConfigs.cacheResponseName];
        const keyList = await caches.keys();
        const cachesToDelete = keyList.filter((key) => !cacheKeepList.includes(key));
        await Promise.all(cachesToDelete.map(serviceWorkerManager.deleteCache()));
    },
    async estimateStorageSpace() {
        if ('storage' in navigator && 'estimate' in navigator.storage) {
            navigator.storage.estimate().then(function (estimate) {
                console.log('Quota: ' + estimate.quota);
                console.log('Usage: ' + estimate.usage);
                console.log('Remaining: ' + (estimate.quota - estimate.usage) + ' bytes');
            });
        } else {
            console.log('Storage Estimation API is not supported in this browser.');
        }          
    }
};

self.addEventListener("activate", function(event) {
    event.waitUntil(serviceWorkerManager.deleteOldCaches());
    event.waitUntil(serviceWorkerManager.enableNavigationPreload());
});

self.addEventListener("install", function(event) {
    event.waitUntil(
        serviceWorkerManager.addResourcesToCache([
            "/",
            "/auth/login",
            "/home",
            "/favicon.ico",
            "/resources/images/lost.png",
            "/resources/images/lost.png"
        ]),
    );
});

self.addEventListener("fetch", function(event) {
    event.respondWith(serviceWorkerManager.cacheFirst({request: event.request, preloadResponsePromise: event.preloadResponse, fallbackUrl: "/favicon.ico"}));
});

self.addEventListener("message", function(event) {
    console.log(123);
});

self.addEventListener("offline", function(event) {
    console.log("Service worker is offline");
});
  
self.addEventListener("online", function(event) {
    console.log("Service worker is online");
});
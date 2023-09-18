const serviceworkerConfigs = {
    cacheResponseName: "v1",
    networkErrorMessage: "Network error happened",
    responseError: 408
};

const addResourcesToCache = async function(resources) {
    const cache = await caches.open(serviceworkerConfigs.cacheResponseName);
    await cache.addAll(resources);
};

const putInCache = async function(request, response) {
    const cache = await caches.open(serviceworkerConfigs.cacheResponseName);
    await cache.put(request, response);
};

const cacheFirst = async function({request, preloadResponsePromise, fallbackUrl}) {
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
            headers: {
                "Content-Type": "text/plain"
            },
        });
    }
};

const enableNavigationPreload = async function() {
    if (self.registration.navigationPreload) {
        await self.registration.navigationPreload.enable();
    }
};

const deleteCache = async function(key) {
    await caches.delete(key);
};

const deleteOldCaches = async function() {
    const cacheKeepList = [serviceworkerConfigs.cacheResponseName];
    const keyList = await caches.keys();
    const cachesToDelete = keyList.filter((key) => !cacheKeepList.includes(key));
    await Promise.all(cachesToDelete.map(deleteCache));
};

self.addEventListener("activate", function(event) {
    event.waitUntil(deleteOldCaches());
    event.waitUntil(enableNavigationPreload());
});

self.addEventListener("install", function(event) {
    event.waitUntil(
        addResourcesToCache([
            "/favicon.ico",
        ]),
    );
});

self.addEventListener("fetch", function(event) {
    event.respondWith(cacheFirst({request: event.request, preloadResponsePromise: event.preloadResponse, fallbackUrl: "/favicon.ico"}));
});
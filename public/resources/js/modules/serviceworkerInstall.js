const cacheResponseName = "v1";

const addResourcesToCache = async (resources) => {
    const cache = await caches.open(cacheResponseName);
    await cache.addAll(resources);
};

const putInCache = async (request, response) => {
    const cache = await caches.open(cacheResponseName);
    await cache.put(request, response);
};

const cacheFirst = async ({
    request,
    preloadResponsePromise,
    fallbackUrl
}) => {
    const responseFromCache = await caches.match(request);
    if (responseFromCache) {
        return responseFromCache;
    }

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
        if (fallbackResponse) {
            return fallbackResponse;
        }
        return new Response("Network error happened", {
            status: 408,
            headers: {
                "Content-Type": "text/plain"
            },
        });
    }
};

const enableNavigationPreload = async () => {
    if (self.registration.navigationPreload) {
        await self.registration.navigationPreload.enable();
    }
};

const deleteCache = async (key) => {
    await caches.delete(key);
};

const deleteOldCaches = async () => {
    const cacheKeepList = [cacheResponseName];
    const keyList = await caches.keys();
    const cachesToDelete = keyList.filter((key) => !cacheKeepList.includes(key));
    await Promise.all(cachesToDelete.map(deleteCache));
};


self.addEventListener("activate", (event) => {
    event.waitUntil(deleteOldCaches());
    event.waitUntil(enableNavigationPreload());
});

self.addEventListener("install", (event) => {
    event.waitUntil(
        addResourcesToCache([
            "/favicon.ico",
        ]),
    );
});

self.addEventListener("fetch", (event) => {
    event.respondWith(
        cacheFirst({
            request: event.request,
            preloadResponsePromise: event.preloadResponse,
            fallbackUrl: "/favicon.ico",
        }),
    );
});
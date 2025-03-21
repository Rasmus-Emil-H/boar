import IndexedDBManager from '/resources/js/modules/indexedDB.js';

const config = {
    INITIAL_TTL_ATTEMPTS: 3,
    clearGETCacheURL: 'clearGETCacheURL',
    caches: {
        GETCache: 'GETCache',
        POSTCache: 'POSTCache',
        fileCache: 'FileCache'
    },
    externalResources: [
        'maps', 'fontawesome'
    ],
    request: {
        validMethods: ['GET', 'POST'],
        skipablePOSTEvictionEndpoints: []
    },
    methods: {
        validateRequest: function (e) {
            const proceed = !config.externalResources.some(item => new RegExp(`/${item}/`).test(e.request.url));
            if (!proceed) return proceed;
            
            if (e.request.url === config.psudo.login && e.request.method === 'POST' && !navigator.onLine || e.request.url.includes('/push')) return false;
            if (!config.request.validMethods.includes(e.request.method)) return false;

            return true;
        },
        GET: async function (request) {
            try {
                const cache = await caches.open(config.caches.GETCache);
                const cachedResponse = await cache.match(request);

                const {quota, usage} = await navigator.storage.estimate();
                const freeSpace = quota - usage > 0;

                if (cachedResponse) {
                    try {
                        if (navigator?.onLine && freeSpace) {
                            fetch(request).then(async (networkResponse) => {
                                const networkResponseClone = networkResponse.clone();
                                if (networkResponse.ok && !networkResponse.redirected) {
                                    const cacheData = new Response(networkResponseClone.body, {
                                        status: networkResponseClone.status,
                                        statusText: networkResponseClone.statusText,
                                        headers: networkResponseClone.headers
                                    });

                                    cacheData.headers.append('Cachedts', Date.now());
                                    await cache.put(request, cacheData);
                                }
                            });
                        }
                    } catch(e) {
                        return cachedResponse;
                    }

                    return cachedResponse;
                }

                const networkResponse = await fetch(request);
                if (networkResponse.ok && !networkResponse.redirected && freeSpace) {
                    const networkResponseClone = networkResponse.clone();
                    const cacheData = new Response(networkResponseClone.body, {
                        status: networkResponseClone.status,
                        statusText: networkResponseClone.statusText,
                        headers: networkResponseClone.headers
                    });

                    cacheData.headers.append('Cachedts', Date.now());
                    await cache.put(request, cacheData);
                }

                return networkResponse;
            } catch (error) {
                const cachedResponse = await caches.match(request);
                if (cachedResponse) return cachedResponse;
                
                return new Response('Network error', { status: 200 });
            }
        },
        POST: async function(request) {
            const clonedRequest = request.clone();
            const body = await clonedRequest.formData();
        
            try {
                const response = await fetch(clonedRequest.url, {mode: 'cors', method: 'POST', body});

                // Dont do anything if "pointless" URLS are POSTed to
                const skip = config.request.skipablePOSTEvictionEndpoints.some(item => request.url.includes(item));
                if (skip) return response;

                if (response.status === 409 && navigator?.onLine) {
                    config.evictGETVersion(request.url);
                } else if (!response.ok || !config.psudo.qualifiedRequestResponsesCode.includes(response.status)) {
                    config.buildIndexDBRecord(request);
                } else if (response.ok) {
                    config.checkCustomGETCacheEviction(body);
                    config.evictGETVersion(request.url, request.referrer);
                }

                return response;
            } catch (error) {
                console.log(error);
                config.buildIndexDBRecord(request);
                
                return new Response(null, {status: 422, statusText: config.messages.errors.postRequest});
            }
        }
    },
    // Evict addtional custom urls if needed
    checkCustomGETCacheEviction: async function(body) {
        for (const [key, value] of body.entries())
            if (key === config.clearGETCacheURL)
                config.evictGETVersion(value);
    },
    evictGETVersion: async function(url, referrer = null) {
        if (!navigator.onLine) return;
        
        const cache = await caches.open(config.caches.GETCache);
        const cacheKeys = await cache.keys();

        for (const item of cacheKeys)
            if (item.url === url) await config.rerunGETRequest(item.url);

        /**
         * Keep refreshing trip so that the overall
         * Also nuke referrer is possible
         * facade remains updated
         */
        
        if (referrer && referrer.trim() !== url.trim()) await config.rerunGETRequest(referrer);
        await config.rerunGETRequest('/trip');
    },
    rerunGETRequest: async function(url) {
        if (!navigator.onLine) return;

        try {
            const cache = await caches.open(config.caches.GETCache);

            await cache.delete(url);

            const {quota, usage} = await navigator.storage.estimate();
            const freeSpace = quota - usage > 0;
            
            const networkResponse = await fetch(url);
            if (networkResponse.ok && !networkResponse.redirected && freeSpace) await cache.put(url, networkResponse.clone());

            return networkResponse;
        } catch (error) {
            
        }
    },
    buildIndexDBRecord: async function(request) {
        const idb = new IndexedDBManager();
        const fd = [...await request.formData()];
        
        const appendTTL = fd.some((item) => item[0] === 'ttl');
        if (!appendTTL) fd.push(['ttl', this.INITIAL_TTL_ATTEMPTS]);

        await idb.createRecord({url: request.url, method: request.method, mode: request.mode, body: fd});
    },
    messages: {
        offline: 'Application is offline',
        errors: {
            postRequest: 'An error occurred'
        }
    },
    actions: {
        message: {
            CACHE_FILE: 'cache-file',
            CACHE_PAGE: 'cache-page',
            CHECK_STATUS: 'check-status'
        }
    },
    psudo: {
        login: '/auth/login',
        origin: 'https://yourhost',
        qualifiedRequestResponsesCode: [200, 400, 401, 403, 404]
    }
};

Object.freeze(config);

export default config;

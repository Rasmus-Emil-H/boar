import IndexedDBManager from '/resources/js/modules/indexedDB.js';

const config = {    
    caches: {
        GETCache: 'GETCache',
        POSTCache: 'POSTCache',
        fileCache: 'FileCache'
    },
    externalResources: [
        'maps', 'fontawesome'
    ],
    request: {
        validMethods: ['GET', 'POST']
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
                        if (navigator?.onLine) {
                            fetch(request).then(async (networkResponse) => {
                                if (networkResponse.ok && !networkResponse.redirected) 
                                    await cache.put(request, networkResponse.clone());
                            });
                        }
                    } catch(e) {
                        return cachedResponse;
                    }

                    return cachedResponse;
                }

                const networkResponse = await fetch(request);
                if (networkResponse.ok && !networkResponse.redirected && freeSpace) await cache.put(request, networkResponse.clone());

                return networkResponse;
            } catch (error) {
                console.log(error);
                return new Response('Network error', { status: 200 });
            }
        },
        POST: async function(request) {
            const clonedRequest = request.clone();
            const formData = await clonedRequest.formData();
            const formDataToSend = new FormData();
        
            for (const [key, value] of formData.entries()) formDataToSend.append(key, value);
        
            try {
                const response = await fetch(clonedRequest.url, {mode: 'cors', method: 'POST', body: formDataToSend});

                if (response.status === 409 && navigator?.onLine) {
                    config.evictGETVersion(request.url);
                } else if (!response.ok || !config.psudo.qualifiedRequestResponsesCode.includes(response.status)) {
                    config.buildIndexDBRecord(request);
                } else if (response.ok) {
                    config.evictGETVersion(request.url);
                }

                return response;
            } catch (error) {
                config.buildIndexDBRecord(request);
                
                return new Response(null, {status: 422, statusText: config.messages.errors.postRequest});
            }
        }
    },
    evictGETVersion: async function(url) {
        if (!navigator.onLine) return;
        
        const cache = await caches.open(config.caches.GETCache);
        const cacheKeys = await cache.keys();

        for (const item of cacheKeys)
            if (item.url === url) await config.rerunGETRequest(item.url);

        /**
         * Keep refreshing trip so that the overall
         * facade remains updated
         */
        await config.rerunGETRequest('/trip');
    },
    rerunGETRequest: async function(url) {
        if (!navigator.onLine) return;

        try {
            const cache = await caches.open(config.caches.GETCache);

            console.log(`Deleting ${url}`);

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
        await idb.createRecord({url: request.url, method: request.method, mode: request.mode, body: [...await request.formData()]});
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
        origin: 'host',
        qualifiedRequestResponsesCode: [200, 400, 401, 403, 404]
    }
};

Object.freeze(config);

export default config;
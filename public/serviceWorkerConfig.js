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
            
            if (e.request.url === config.psudo.login && e.request.method === 'POST' && !online() || e.request.url.includes('/push')) return false;
            if (!config.request.validMethods.includes(e.request.method)) return false;

            return true;
        },
        GET: async function (request) {
            try {
                const cache = await caches.open(config.caches.GETCache);
                const cachedResponse = await cache.match(request);
                
                if (cachedResponse) {
                    fetch(request).then(async (networkResponse) => {
                        if (networkResponse.ok && !networkResponse.redirected) 
                            await cache.put(request, networkResponse.clone());
                    });

                    return cachedResponse;
                }
                
                const networkResponse = await fetch(request);
                if (networkResponse.ok && !networkResponse.redirected) 
                    await cache.put(request, networkResponse.clone());

                return networkResponse;
            } catch (error) {
                return new Response('Network error', { status: 500 });
            }
        },
        POST: async function(request) {
            const clonedRequest = request.clone();
            const formData = await clonedRequest.formData();
            const formDataToSend = new FormData();
        
            for (const [key, value] of formData.entries()) formDataToSend.append(key, value);
        
            try {
                const response = await fetch(clonedRequest.url, {mode: 'cors', method: 'POST', body: formDataToSend});

                if (!response.ok || !config.psudo.qualifiedRequestResponsesCode.includes(response.status)) config.buildIndexDBRecord(request);
                else config.evictGETVersion(request.url);

                return response;
            } catch (error) {
                console.log(error);
                config.buildIndexDBRecord(request);
                
                return new Response(null, {status: 422, statusText: config.messages.errors.postRequest});
            }
        }
    },
    evictGETVersion: async function(url) {
        const cache = await caches.open(config.caches.GETCache);
        const cacheKeys = await cache.keys();

        cacheKeys.map(async item => {
            if (item.url !== url) return;

            const cachedResponse = await cache.match(item);
            await cache.delete(cachedResponse.url);
        });
    },
    buildIndexDBRecord: async function(request) {
        const storePOSTRequestByIDB = new IndexedDBManager();
        await storePOSTRequestByIDB.createRecord({url: request.url, method: request.method, mode: request.mode, body: [...await request.formData()]});
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
        qualifiedRequestResponsesCode: [200, 400, 401, 403, 404, 409, 422]
    }
};

Object.freeze(config);

export default config;
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
                const clone = await request.clone();
        
                const response = await fetch(clone);
        
                if (response.ok && !response.redirected) {
                    const cache = await caches.open(config.caches.GETCache);
                    await cache.put(request, response.clone());
                }
        
                return response;
            } catch (error) {
                console.error('Fetch encountered an error:', error);

                const cachedResponse = await caches.match(request);
                if (cachedResponse) return cachedResponse;

                return new Response('Network error', { status: 500 });
            }
        },
        POST: async function(request) {
            const clonedRequest = request.clone();
            const formData = await clonedRequest.formData();
            const formDataToSend = new FormData();
        
            for (const [key, value] of formData.entries()) formDataToSend.append(key, value);
        
            try {
                const response = await fetch(clonedRequest.url, {method: 'POST', body: formDataToSend});

                if (!response.ok || !config.psudo.qualifiedRequestResponsesCode.includes(response.status)) {
                    const storePOSTRequestByIDB = new IndexedDBManager();
                    await storePOSTRequestByIDB.createRecord({url: request.url, method: request.method, mode: request.mode, body: [...await request.formData()]});
                }

                return response;
            } catch (error) {
                const storePOSTRequestByIDB = new IndexedDBManager();
                await storePOSTRequestByIDB.createRecord({url: request.url, method: request.method, mode: request.mode, body: [...await request.formData()]});
                
                return new Response(null, {status: 422, statusText: config.messages.errors.postRequest});
            }
        }
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
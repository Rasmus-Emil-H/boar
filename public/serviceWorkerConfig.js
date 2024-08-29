const config = {
    caches: {
        GETCache: 'GETCache',
        POSTCache: 'POSTCache',
        fileCache: 'FileCache'
    },
    request: {
        validMethods: ['GET', 'POST']
    },
    methods: {
        validateRequest: function (e) {
            if (e.request.url.includes('google')) return false;
            if (e.request.url === config.psudo.login && e.request.method === 'POST' && !navigator.onLine || e.request.url.includes('/push')) return false;
            if (!config.request.validMethods.includes(e.request.method)) return false;

            return true;
        },
        GET: async function(request) {
            const clone = request.clone();
            const cache = await caches.open(config.caches.GETCache);  
        
            // const cacheKeys = await cache.keys();
        
            // for (const requestKey of cacheKeys) {
            //     const cachedResponse = await cache.match(requestKey);
            //     if (cachedResponse) return cachedResponse;
            // }
        
            const response = await fetch(clone.url);
            await cache.put(request, response.clone());
            return response;
        },
        POST: async function(request) {
            const clonedRequest = request.clone();
            const formData = await clonedRequest.formData();
            const formDataToSend = new FormData();
        
            for (const [key, value] of formData.entries()) {
                formDataToSend.append(key, value);
            }
        
            try {
                return await fetch(clonedRequest.url, {
                    method: 'POST',
                    body: formDataToSend,
                });
            } catch (error) {
                return new Response(null, { status: 500, statusText: 'Failed to send POST request' });
            }
        }
    },
    messages: {
        offline: 'Application is offline. Cannot send cached POST requests... Once your application is online again, it will send these cached requests automatically.',
        errors: {
            postRequest: 'Error sending cached POST request. Status:'
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
        qualifiedRequestResponsesCode: [200, 400, 401, 403, 404, 409]
    }
};

Object.freeze(config);

export default config;
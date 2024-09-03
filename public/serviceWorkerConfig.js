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
            const test = config.externalResources.map(function(item) {
                const skip = new RegExp(`/${item}/`);
                return skip.test(e.request.url);
            });

            if (test) return false;

            if (e.request.url === config.psudo.login && e.request.method === 'POST' && !navigator.onLine || e.request.url.includes('/push')) return false;
            if (!config.request.validMethods.includes(e.request.method)) return false;

            return true;
        },
        GET: async function (request) {
            try {
                const clone = await request.clone();
                const cache = await caches.open(config.caches.GETCache);
        
                const response = await fetch(clone);
        
                if (response.ok && !response.redirected) await cache.put(request, response.clone());
        
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
            const cache = await caches.open(config.caches.GETCache);
        
            for (const [key, value] of formData.entries())
                formDataToSend.append(key, value);
        
            try {
                const response = await fetch(clonedRequest.url, {
                    method: 'POST',
                    body: formDataToSend,
                });

                if (!response.ok) cache.put(request, clonedRequest);

                return response;
            } catch (error) {
                return new Response(null, { status: 418, statusText: 'Failed to send POST request' });
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
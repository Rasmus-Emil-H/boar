const cacheName = 'v2';
const postCache = 'post-requests-cache';
const fileCache = 'file-cache';
const tempOfflineCache = 'offline-cache';
const login = '/auth/login/';

const actions = {
    message: {
        CACHE_FILE: 'cache-file',
        CACHE_PAGE: 'cache-page'
    }
};

const messages = {
    offline: 'Application is offline. Cannot send cached POST requests... Once your application is online again, it will send these cached requests automatically.',
    errors: {
        postRequest: 'Error sending cached POST request. Status:'
    }
}

self.addEventListener('install', e => {

});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if (cache !== cacheName) return caches.delete(cache);
                })
            );
        })
    );
});

self.addEventListener('fetch', e => {
    if ( e.request.url === login ) return;
    if(e.request.method === 'POST') e.respondWith(cachePostRequest(e.request));
    else {
        e.respondWith(
            fetch(e.request)
              .then(async res => {
                  const resClone = res.clone();
                  if (e.request.method !== 'POST') {
                      caches.open(cacheName).then(cache => {
                          cache.put(e.request, resClone);
                      });
                  }
                  return res;
              })
              .catch(err => caches.match(e.request).then(res => res))
        );
    }
});

async function setResponse(status, url) {
    return {status, headers: { 'Location': url, }};
}

async function cachePostRequest(request) {
    const requestClone = request.clone();
    const formData = await requestClone.formData();
    const cacheKey = `${requestClone.url}/${Date.now()}`;
    const cacheData = { request: request.clone(), formData: Object.fromEntries(formData.entries()), request: requestClone, url: request.url };
    const cache = await caches.open(postCache);
    await cache.put(cacheKey, new Response(JSON.stringify(cacheData)));
    sendCachedPostRequests();
    return new Response('OK', {status: 302, headers: { 'Location': request.url }});
}

async function sendCachedPostRequests() {
    if (!navigator.onLine) {
        console.log(messages.offline);
        return;
    }
    const cache = await caches.open(postCache);
    const cacheKeys = await cache.keys(); 
    for (const cacheKey of cacheKeys) {
        const cachedResponse = await cache.match(cacheKey);
        if (!cachedResponse) return;
        try {
            const cachedData = await cachedResponse.json();
            const fd = new FormData();
            for (const [key, value] of Object.entries(cachedData.formData)) fd.append(key, value);
            const response = await fetch(cachedData.url, { method: 'POST', body: fd });
            response.ok ? await cache.delete(cacheKey) : console.error(messages.errors.postRequest, response.status);
        } catch (error) {
            console.error(messages.errors.postRequest, error);
        }
    }
}

async function sendCachedFileRequests() {
    const cache = await caches.open(fileCache);
    const cacheKeys = await cache.keys();
    for(const cacheKey in cacheKeys) {
        const cacheResponse = await cache.match(cacheKey);
        if (!cacheResponse) return;
        try {
            await fetch(cacheResponse.url, { method: 'POST', body: cacheResponse.formData });
        } catch (error) {
            console.log("file sync err");
        }
    }
}

self.addEventListener('message', (event) => {
    if (event.data.action === actions.message.CACHE_PAGE) {
        const { url } = event.data;
        event.waitUntil(
            caches.open(cacheName).then((cache) => {
                return fetch(url)
                    .then((response) => {
                        const resClone = response.clone();
                        caches.open(cacheName).then(cache => {
                            cache.put(url.request, resClone);
                        });
                    })
                    .catch((e) => {
                        console.log(e);
                    });
            })
        );
    } else if (event.data.action === actions.message.CACHE_FILE) {
        const { file, url } = event.data;
        const formData = new FormData();
        formData.append('file', file);
    
        fetch(url, { method: 'POST', body: formData })
          .then((response) => {
            if (response.ok) {
                
            } else {
              return caches.open(fileCache)
                .then((cache) => {
                  const key = `file-${Date.now()}`;
                  return cache.put(key, new Response(file));
                })
                .catch((e) => {
                  console.error('Error storing file in cache:', e);
                });
            }
          })
          .catch((error) => {
            return caches.open(fileCache)
              .then((cache) => {
                const key = `file-${Date.now()}`;
                return cache.put(key, new Response(file));
              })
              .catch((e) => {
                console.error('Error storing file in cache:', e);
              });
          });
    }
});

self.addEventListener('online', event => {
    sendCachedFileRequests();
    sendCachedPostRequests();
});
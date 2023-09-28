const cacheName = 'v2';
const postCache = 'post-requests-cache';
const fileCache = 'file-cache';
const tempOfflineCache = 'offline-cache';

const actions = {
    message: {
        CACHE_FILE: 'cache-file',
        CACHE_PAGE: 'cache-page'
    }
};

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
    if ( e.request.url === '/auth/login/' ) return;
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

async function cachePostRequest(request) {
    const requestClone = request.clone();
    const requestBody = await requestClone.text();
    const cacheKey = 'post-requests-' + Date.now();
    const cacheData = {
      request: request.clone(),
      body: requestBody,
    };
    const cache = await caches.open(postCache);
    await cache.put(cacheKey, new Response(JSON.stringify(cacheData)));
    return new Response('POST request cached', { status: 200 });
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
        const { file } = event.data;
        event.waitUntil(
            caches.open(fileCache)
            .then((cache) => {
                const key = `file-${Date.now()}`;
                return cache.put(key, new Response(file));
            })
            .catch((e) => {
                console.log(e);
            })
        );
    }
});

self.addEventListener('online', event => {
    caches.open(tempOfflineCache).then(cache => {
        cache.keys().then(requests => {
            requests.forEach(request => {
                fetch(request).then(response => {
                    cache.delete(request);
                }).catch(error => {
                    console.error('Error sending cached request:', error);
                });
            });
        });
    });
});

self.addEventListener('offline', event => {
    console.log('Device is offline');
});
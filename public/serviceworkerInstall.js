const cacheName = 'v2';
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
    e.respondWith(
        fetch(e.request)
        .then(res => {
            const resClone = res.clone();
            if (e.request.method !== 'POST') {
                caches.open(cacheName).then(cache => {
                    cache.put(e.request, resClone);
                });
            } else if (e.request.method === 'POST' && !navigator.onLine) {
                e.respondWith(
                    caches.open(tempOfflineCache).then(cache => {
                        return cache.add(e.request.clone());
                    })
                );
            }
            return res;
        })
        .catch(err => caches.match(e.request).then(res => res))
    );
});

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
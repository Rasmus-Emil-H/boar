const cacheName = 'v2';
const fileCache = 'v2files';

self.addEventListener('install', e => {
  console.log('Service Worker: Installed');
});

self.addEventListener('activate', e => {
  console.log('Service Worker: Activated');
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
  console.log('Service Worker: Fetching', e);
  e.respondWith(
    fetch(e.request)
      .then(res => {
        const resClone = res.clone();
        caches.open(cacheName).then(cache => {
          cache.put(e.request, resClone);
        });
        return res;
      })
      .catch(err => caches.match(e.request).then(res => res))
  );
});

self.addEventListener('message', (event) => {
  if (event.data.action === 'cache-page') {
    const { url, content } = event.data;
    event.waitUntil(
        caches.open(cacheName).then((cache) => {
            return fetch(url)
                .then((response) => {
                    const resClone = response.clone();
                    caches.open(cacheName).then(cache => { cache.put(url.request, resClone); });
                })
                .catch((e) => {
                    console.log(e);
                });
        })
    );
  } else if (event.data.action === 'cache-file') {
    const { blob } = event.data;
    event.waitUntil(
      caches.open(fileCache).then((cache) => {
        const key = `file-${Date.now()}`;
        return cache.put(key, blob);
      })
    );
  }
});
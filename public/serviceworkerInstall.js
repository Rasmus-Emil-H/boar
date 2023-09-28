const cacheName = 'v2';
const postCache = 'post-requests-cache';
const fileCache = 'file-cache';
const tempOfflineCache = 'offline-cache';
const login = '/auth/login';

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

async function cachePostRequest(request) {
  const requestClone = request.clone();
  const formData = await requestClone.formData();
  const cacheKey = `${requestClone.url}/${Date.now()}`;
  const cacheData = { request: request.clone(), formData: Object.fromEntries(formData.entries()), url: request.url };
  const cache = await caches.open(postCache);
  await cache.put(cacheKey, new Response(JSON.stringify(cacheData)));
  sendCachedPostRequests();
  return new Response('POST request cached', { status: 200 });
}

async function sendCachedPostRequests() {
  if (!navigator.onLine) {
      console.log('Application is offline. Cannot send cached POST requests... Once your application is online again, it will send these cached requests automatically.');
      return;
  }
  const cache = await caches.open(postCache);
  const cacheKeys = await cache.keys(); 
  for (const cacheKey of cacheKeys) {
      const cachedResponse = await cache.match(cacheKey);
      if (cachedResponse) {
          try {
              const cachedData = await cachedResponse.json();
              const fd = new FormData();
              for (const [key, value] of Object.entries(cachedData.formData)) fd.append(key, value);
              const response = await fetch(cachedData.url, { method: 'POST', body: fd });
              response.ok ? await cache.delete(cacheKey) : console.error('Error sending cached POST request. Status:', response.status);
          } catch (error) {
              console.error('Error sending cached POST request:', error);
          }
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
  sendCachedPostRequests();
});

self.addEventListener('offline', event => {
  console.log('Device is offline');
});
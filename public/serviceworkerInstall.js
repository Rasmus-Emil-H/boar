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

const messages = {
  offline: 'Application is offline. Cannot send cached POST requests... Once your application is online again, it will send these cached requests automatically.',
  errors: {
      postRequest: 'Error sending cached POST request. Status:'
  }
}

self.addEventListener('install', e => {
  e.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', e => {
  e.waitUntil(self.clients.claim());
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
  if (e.request.url === login && e.request.method === 'POST') return;
  if (e.request.method === 'POST') e.respondWith(cachePostRequest(e.request));
  else {
      e.respondWith(
          fetch(e.request)
            .then(async res => {
              const resClone = res.clone();
              caches.open(cacheName).then(cache => {
                  cache.put(e.request, resClone);
              });
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
  const cacheData = { request: request.clone(), formData: Object.fromEntries(formData.entries()), request: requestClone, url: request.url };
  const cache = await caches.open(postCache);
  await cache.put(cacheKey, new Response(JSON.stringify(cacheData)));
  await sendCachedPostRequests();
  return new Response('OK', {status: 302, headers: { 'Location': request.url }});
}

function checkConnection() {
  if (!navigator.onLine) {
      console.log(messages.offline);
      return 1;
  }
  return 0;
}

async function synchroniseCaches() {
  await sendCachedFileRequests();
  await sendCachedPostRequests();
}

async function sendCachedPostRequests() {
  if(checkConnection() === 1) return;
  const cache = await caches.open(postCache);
  const cacheKeys = await cache.keys(); 
  for (const cacheKey of cacheKeys) {
      const cachedResponse = await cache.match(cacheKey);
      if (!cachedResponse) return;
      try {
          const cachedData = await cachedResponse.json();
          const body = new FormData();
          for (const [key, value] of Object.entries(cachedData.formData)) body.append(key, value);
          const response = await fetch(cachedData.url, { method: 'POST', body });
          response.ok ? cache.delete(cacheKey) : console.error(messages.errors.postRequest, response.status);
      } catch (error) {
          console.error(messages.errors.postRequest, error);
      }
  }
}

async function sendCachedFileRequests() {
  if(checkConnection() === 1) return;
  const cache = await caches.open(fileCache);
  const cacheKeys = await cache.keys();
  for (const cacheKey of cacheKeys) {
      const cacheResponse = await cache.match(cacheKey);
      try {
          const body = await cacheResponse.formData();
          await fetch(body.get('url'), { method: 'POST', body })
              .then(async res => {
                  await cache.delete(cacheKey);
                  return new Response('OK', {status: 302, headers: { 'Location': body.get('url') }});
              })
              .catch(err => {
                  console.log(err);
              });
      } catch (error) {
          console.log("file sync err", error);
      }
  }
}

self.addEventListener('message', (event) => {
  if (event.data.action === actions.message.CACHE_PAGE) {
      const { url } = event.data;
      if (!url) return;
      event.waitUntil(
          caches.open(cacheName).then((cache) => {
              return fetch(url)
                  .then((response) => {
                      const resClone = response.clone();
                      cache.put(url, resClone);
                  })
                  .catch((e) => {
                      console.log(e);
                  });
          })
      );
  } else if (event.data.action === actions.message.CACHE_FILE) {
      const formData = new FormData();
      for(let obj in event.data) formData.append(obj, event.data[obj]);
      return caches.open(fileCache)
          .then(async (cache) => {
              const key = `file-${Date.now()}`;
              await cache.put(key, new Response(formData));
              await sendCachedFileRequests();
              return new Response('OK', {status: 302, headers: { 'Location': formData.get('url') }});
          })
          .catch((e) => {
              console.error('Error storing file in cache:', e);
          });
  } else if(event.data.action === 'check-status') {
      synchroniseCaches();
  }
});

self.addEventListener('online', event => {
  synchroniseCaches();
});

self.addEventListener('offline', event => {
  console.log(event);
});
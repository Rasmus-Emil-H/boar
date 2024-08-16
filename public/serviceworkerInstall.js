const cacheName = 'v2';
const postCache = 'post-requests-cache';
const fileCache = 'file-cache';
const tempOfflineCache = 'offline-cache';
const login = '/auth/login';
const origin = 's://h';

const actions = {
    message: {
        CACHE_FILE: 'cache-file',
        CACHE_PAGE: 'cache-page',
        CHECK_STATUS: 'check-status'
    }
};

const messages = {
    offline: 'Application is offline. Cannot send cached POST requests... Once your application is online again, it will send these cached requests automatically.',
    errors: {
        postRequest: 'Error sending cached POST request. Status:'
    }
}

self.addEventListener('beforeinstallprompt', event => { 
    event.preventDefault(); 
    const installButton = document.querySelector('#installButton'); 
    if (!installButton) return 
    installButton.style.display = 'block'; 
    installButton.addEventListener('click', () => { 
        event.prompt(); 
    });
});

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

self.addEventListener('push', function(event) {
    console.log(event);
    const options = {
        body: 'Data',
        icon: '/resources/images/logo.png',
        badge: '/resources/images/logo.png',
        silent: false,
        actions: [
            { action: 'open_app', title: 'Open App' }
        ],
        requireInteraction: true,
    };

    console.log(event);

    event.waitUntil(
        self.registration.showNotification('Data', options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    event.waitUntil(
        clients.matchAll({type: 'window'}).then(function(clientList) {
            return clients.openWindow(origin);
        })
    );
});

self.addEventListener('fetch', e => {
    if (e.request.url === login && e.request.method === 'POST' && !navigator.onLine) return;
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
    const post = await sendCachedPostRequests();
    return new Response(post.body, {status: post.status, headers: post.headers});
}

function checkConnection() {
    return Number(navigator.onLine);
}

async function synchroniseCaches() {
  await sendCachedFileRequests();
  await sendCachedPostRequests();
}

async function sendCachedPostRequests() {
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
            const deleteCaches = [400, 401, 403, 409];
            response.ok || deleteCaches.includes(response.status) ? cache.delete(cacheKey) : console.log(messages.errors.postRequest, response.status);
            return response;
        } catch (error) {
            console.log(messages.errors.postRequest, error);
        }
    }
}

function respondToClient(msg) {
    self.clients.matchAll().then(clients => {
        clients.forEach(client => {
            client.postMessage(msg);
        });
    });
}

async function sendCachedFileRequests(fileKey) {
    const cache = await caches.open(fileCache);
    const cacheKeys = await cache.keys();
    for (const cacheKey of cacheKeys) {
        const cacheResponse = await cache.match(cacheKey);
        try {
            const body = await cacheResponse.formData();
            const date = new Date().toLocaleString().replaceAll('/', '-').replaceAll(',', '');
            if(fileKey) respondToClient({meta: body.get('meta'), data: {cachedPath: fileKey, 'Path': body.get('fileName'), id: body.get('EntityID'), UploadID: null, EntityID: body.get('EntityID'), Created: date, targetProp: 'uploads'}});
            const response = await fetch(body.get('url'), { method: 'POST', body });
            response.ok ? await cache.delete(cacheKey) : console.error(messages.errors.postRequest, response.status);
            return new Response(response.body, {status: response.status, headers: response.headers});
        } catch (error) {
            console.log("file sync err", error);
        }
    }
}

self.addEventListener('message', (event) => {
    if (!event.origin === origin) return;
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
                await sendCachedFileRequests(key);
            })
            .catch((e) => {
                console.error('Error storing file in cache:', e);
            });
    } else if(event.data.action === actions.message.CHECK_STATUS) synchroniseCaches();
});

self.addEventListener('online', event => {
    synchroniseCaches();
});

self.addEventListener('offline', event => {
    
});
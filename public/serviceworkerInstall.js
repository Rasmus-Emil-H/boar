/**
|----------------------------------------------------------------------------
| Static
|----------------------------------------------------------------------------
|
*/

const cacheName = 'v2';
const postCache = 'post-requests-cache';
const fileCache = 'file-cache';
const tempOfflineCache = 'offline-cache';
const login = '/auth/login';
const origin = ':/';

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

/**
|----------------------------------------------------------------------------
| The reason we're here
|----------------------------------------------------------------------------
|
*/

self.addEventListener('fetch', e => {
    if (e.request.url === login && e.request.method === 'POST' && !navigator.onLine || e.request.url.includes('/push')) return;
    if (e.request.method === 'POST') e.respondWith(handlePostRequest(e.request));
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

/**
|----------------------------------------------------------------------------
| Functions
|----------------------------------------------------------------------------
|
*/

async function handlePostRequest(request) {
    const clonedRequest = request.clone();
    const cacheKey = `${clonedRequest.url}|${Date.now()}`;

    const formData = await clonedRequest.formData();
    const serializedData = [];

    for (const [key, value] of formData.entries()) {
        if (value instanceof File) {
            const fileData = {
                key,
                name: value.name,
                type: value.type,
                data: await value.arrayBuffer()
            };
            serializedData.push(fileData);
        } else {
            serializedData.push({ key, value });
        }
    }

    const cache = await caches.open(postCache);
    await cache.put(cacheKey, new Response(JSON.stringify(serializedData)));

    const postResponse = await sendCachedPostRequests();
    if (postResponse) return postResponse;

    return new Response(null, { status: 500, statusText: "Failed to send cached POST request" });
}

async function sendCachedPostRequests() {
    const cache = await caches.open(postCache);
    const cacheKeys = await cache.keys();

    for (const requestKey of cacheKeys) {
        const cachedResponse = await cache.match(requestKey);
        if (!cachedResponse) continue;

        try {
            const cachedData = await cachedResponse.json();
            const formData = new FormData();

            for (const item of cachedData) {
                if (item.data) {
                    const file = new File([new Uint8Array(item.data)], item.name, { type: item.type });
                    formData.append(item.key, file);
                } else {
                    formData.append(item.key, item.value);
                }
            }

            const response = await fetch(requestKey.url, {method: 'POST', body: formData});

            const deleteCaches = [400, 401, 404, 403, 409];
            if (response.ok || deleteCaches.includes(response.status)) await cache.delete(requestKey);

            return response;
        } catch (error) {
            console.error('Error sending cached POST request:', error);
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

function checkConnection() {
    return Number(navigator.onLine);
}

async function synchroniseCaches() {
  await sendCachedFileRequests();
  await sendCachedPostRequests();
}

/**
|----------------------------------------------------------------------------
| Event listeners
|----------------------------------------------------------------------------
|
*/

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

self.addEventListener('activate', (e) => {
    e.waitUntil(self.clients.claim());
});

self.addEventListener('push', function(event) {
    console.log(event);
    const options = {
        body: 'Changed registered',
        icon: '/resources/images/logo.png',
        badge: '/resources/images/logo.png',
        requireInteraction: true,
        silent: false,
        actions: [
            { action: 'open_app', title: 'Open App' }
        ],
        requireInteraction: true,
    };

    console.log(event);

    event.waitUntil(
        self.registration.showNotification('Information has been updated', options)
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
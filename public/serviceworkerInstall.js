/**
|----------------------------------------------------------------------------
| Static Configuration
|----------------------------------------------------------------------------
|
*/

import config from './serviceWorkerConfig.js';

/**
|----------------------------------------------------------------------------
| Fetch Event Listener
|----------------------------------------------------------------------------
|
*/

self.addEventListener('fetch', (e) => {
    if (!config.methods.validateRequest(e)) return;

    const methodHandler = e.request.method;
    if (methodHandler === 'GET' || methodHandler === 'POST') e.respondWith(config.methods[methodHandler](e.request));
});

/**
|----------------------------------------------------------------------------
| Send Cached POST Requests
|----------------------------------------------------------------------------
|
*/

async function sendCachedPostRequests() {
    const cache = await caches.open(config.caches.POSTCache);  
    const cacheKeys = await cache.keys();

    for (const requestKey of cacheKeys) {
        const cachedResponse = await cache.match(requestKey);
        if (!cachedResponse) continue;

        try {
            const cachedData = await cachedResponse.json();
            const formData = new FormData();

            for (const item of cachedData) {
                if (item.data) {
                    const blob = new Blob([new Uint8Array(item.data)], { type: item.type });
                    const file = new File([blob], item.name, { type: item.type });
                    formData.append(item.key, file);
                } else {
                    formData.append(item.key, item.value);
                }
            }

            const response = await fetch(requestKey.url.split('%')[0], {
                method: 'POST',
                body: formData,
            });

            if (response.ok || config.psudo.qualifiedRequestResponsesCode.includes(response.status)) {
                await cache.delete(requestKey);
            }

            return response;
        } catch (error) {
            console.error(config.messages.errors.postRequest, error);  
        }
    }
}

/**
|----------------------------------------------------------------------------
| Helper functions
|----------------------------------------------------------------------------
|
*/

function respondToClient(msg) {
    const channel = new BroadcastChannel('sw-messages');
    channel.postMessage(msg);
}

function checkConnection() {
    return Number(navigator.onLine);
}

async function synchroniseCaches() {
    await sendCachedPostRequests();
    // Add any additional cache synchronization functions here
}

/**
|----------------------------------------------------------------------------
| Event listeners
|----------------------------------------------------------------------------
|
*/

self.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    const installButton = document.querySelector('#installButton');
    if (!installButton) return;

    installButton.style.display = 'block';
    installButton.addEventListener('click', () => {
        event.prompt();
    });
});

self.addEventListener('install', (e) => {
    e.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (e) => {
    e.waitUntil(self.clients.claim());
});

self.addEventListener('push', (event) => {
    respondToClient(event.data);
});

self.addEventListener('notificationclick', async (event) => {
    switch (event.action) {
        case 'open_app':
            clients.openWindow('/trip');
            break;
        case 'reply':
            await fetch(`/push/reply?id=${event.notification.data.id}`);
            break;
        default:
            break;
    }
});

self.addEventListener('message', (event) => {
    if (event.origin !== config.psudo.origin) return;  
    const action = event.data.action;

    if (action === config.actions.message.CACHE_PAGE) {  
        const { url } = event.data;
        if (!url) return;
        event.waitUntil(
            caches.open(config.caches.GETCache).then((cache) => {  
                return fetch(url)
                    .then((response) => {
                        cache.put(url, response.clone());
                    })
                    .catch((e) => {
                        console.log(e);
                    });
            })
        );
    } else if (action === config.actions.message.CACHE_FILE) {  
        const formData = new FormData();
        for (let obj in event.data) formData.append(obj, event.data[obj]);
        caches.open(config.caches.fileCache)  
            .then(async (cache) => {
                const key = `file-${Date.now()}`;
                await cache.put(key, new Response(formData));
                await sendCachedFileRequests(key);
            })
            .catch((e) => {
                console.error('Error storing file in cache:', e);
            });
    } else if (action === config.actions.message.CHECK_STATUS) {  
        synchroniseCaches();
    }
});

self.addEventListener('online', (event) => {
    synchroniseCaches();
});

self.addEventListener('offline', (event) => {
    // Handle offline state
});

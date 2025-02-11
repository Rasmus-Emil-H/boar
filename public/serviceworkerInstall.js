/**
|----------------------------------------------------------------------------
| Static Configuration
|----------------------------------------------------------------------------
|
*/

import config from './serviceWorkerConfig.js';
import IndexedDBManager from '/resources/js/modules/indexedDB.js';

/**
|----------------------------------------------------------------------------
| Fetch Event Listener
|----------------------------------------------------------------------------
|
*/

self.addEventListener('fetch', (e) => {
    if (!config.methods.validateRequest(e)) return;
    const methodHandler = e.request.method;
    e.respondWith(config.methods[methodHandler](e.request));
});

/**
|----------------------------------------------------------------------------
| Send Cached POST Requests
| This logic assumes that every single entry in the IDB was meant to be send to the server
| And is only a result of a network error
|----------------------------------------------------------------------------
|
*/

async function sendCachedPostRequests() {
    const db = new IndexedDBManager();
    const records = await db.getAllRecords();
    for (let i = 0; i < records.length; i++) {
        try {
            const request = records[i];
            const body = new FormData();
            for (let[key, value] of request.body) body.append(key, value);
            await fetch(request.url, {method: request.method, body});
            db.deleteRecord(request.id);
        } catch(e) {
            
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
            clients.openWindow('/home');
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
    sendCachedPostRequests();
});

self.addEventListener('offline', (event) => {
    // Handle offline state
});

window[appName].serviceWorkerInit = {
    init: async function() {
        try {
            if (!await navigator.serviceWorker.getRegistration()) await navigator.serviceWorker.register('/serviceworkerInstall.js', {scope: '/', type: 'module'});

            const permission = await Notification.requestPermission();

            if (permission !== 'granted') return;

            this.checkPubSub();
        } catch (error) {
            console.error('Service Worker registration or Push Notification subscription failed:', error);
        }
    },
    checkPubSub: async function() {
        try {
            const registration = await navigator.serviceWorker.getRegistration();
            const key = await this.urlB64ToUint8Array(await fetch('/push/subscribe').then(response => response.json()).then(json => json.responseJSON));
    
            const subscription = await registration.pushManager.getSubscription() || await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: key
            });
    
            this.sendSubscriptionToServer(subscription);
        } catch (e) {
            window[appName].components.toast('Your device did not subscribe to the application', window[appName].constants.mdbootstrap.ERROR_CLASS);
        }
    },
    sendSubscriptionToServer: async function(subscription) {
        await $.post('/push/subscribe', {
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(subscription)
        });
    },
    unregister: function() {
        navigator.serviceWorker.getRegistrations().then(registrations => {
            for (const registration of registrations)
                registration.unregister();
        });
    },
    urlB64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');
        
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
    
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    },    
    dispatchPushNotification: async function(body, icon = '/resources/images/pwalogo.png', tag) {
        const registration = await navigator.serviceWorker.getRegistration();
        await registration.showNotification(window[appName].getName(), {body, icon, vibrate: [200, 100, 200, 100, 200, 100, 200], tag});
    }
};

// window[appName].serviceWorkerInit.init();
// window[appName].serviceWorkerInit.unregister();
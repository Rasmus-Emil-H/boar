window[appName].serviceWorkerInit = {
    init: async function() {
        try {
            const registration = await navigator.serviceWorker.register('/serviceworkerInstall.js', {scope: '/'});
            const permission = await Notification.requestPermission();

            if (permission !== 'granted') return;

            const key = await this.urlB64ToUint8Array(await fetch('/push/getPublicKey').then(response => response.json()).then(json => json.responseJSON));

            const subscription = await registration.pushManager.getSubscription() || await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: key
            });

            return await this.sendSubscriptionToServer(subscription);
        } catch (error) {
            console.error('Service Worker registration or Push Notification subscription failed:', error);
        }
    },
    sendSubscriptionToServer: async function(subscription) {
        await $.post('/push/subscribe', {
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(subscription)
        }).always(function(res) {
            setTimeout(() => {
                window[appName].components.toast('Subscribed to receive notifications', window[appName].constants.mdbootstrap.SUCCESS_CLASS);
            }, 2000);
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

window[appName].serviceWorkerInit.init();

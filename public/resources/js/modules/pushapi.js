export default {
    init: function() {
        if (('PushManager' in window)) return;
    
        window.boar.components.toast(
            'Push API is not avaliable on your device', 
            window.boar.constants.mdbootstrap.ERROR_CLASS
        );
    },
    sendMessage: function(registration, body, tag, icon = "/resources/images/logo.png") {
        registration.showNotification("Boar push message", {body, icon, vibrate: [200, 100, 200, 100, 200, 100, 200], tag});
    }
}
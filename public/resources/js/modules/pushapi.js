export default {
    init: function() {
        if (('PushManager' in window)) return;
    
        window.autologik.components.toast(
            'Push API is not avaliable on your device', 
            window.boar.constants.mdbootstrap.ERROR_CLASS
        );
    }
}
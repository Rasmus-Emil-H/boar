export default {
    init: async function() {
        const permission = await Notification.requestPermission();
        const perm = permission !== 'granted' ? 'not granted' : 'granted';

        window.autologik.components.toast(
            `Notification permission ${perm}`, 
            window.boar.constants.mdbootstrap[perm === 'granted' ? 'SUCCESS_CLASS' : 'ERROR_CLASS']
        );
    }
}
class NotificationHandler {
    constructor(notificationType) {
        this.notificationType = notificationType;
    }

    showNotification(message) {
        if (!this.notificationType && !message) return;

        window[appName].components.toast(`${message ?? this.notificationType}`, window[appName].constants.mdbootstrap.ERROR_CLASS);
    }
}

export default NotificationHandler;
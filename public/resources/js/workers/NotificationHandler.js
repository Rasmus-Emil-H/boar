class NotificationHandler {
    constructor(notificationType) {
        this.notificationType = notificationType;
    }

    showNotification(message) {
        if (!this.notificationType && !message) return;

        window[appName].components.addPushNotification({
            message: `${message ?? this.notificationType}`,
            created: new Date().toLocaleTimeString(),
        });
    }
}

export default NotificationHandler;
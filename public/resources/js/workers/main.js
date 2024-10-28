import NotificationHandler from './NotificationHandler.js';
import WorkerManager from './WorkerManager.js';

class WorkerParent {
    constructor(options) {
        this.type = options.type;
        this.data = options.data;
        this.target = options.target;
        this.callback = options.cb;
        this.additionalData = options.additionalData ?? {};

        this.notificationHandler = new NotificationHandler(options.notificationType);
        this.workerManager = new WorkerManager(this.type, this.data);

        this.body = this.prepareData();

        this.initialize();
    }

    initialize() {
        this.notificationHandler.showNotification();
        this.startWorkerCycle();
    }

    prepareData() {
        const body = {};

        for (let [key, value] of this.data.entries()) body[key] = value;
        if (!Object.keys(body).includes('url')) body.url = location.href;

        return body;
    }

    startWorkerCycle() {
        this.workerManager.postMessage(this.body);

        this.workerManager.setOnMessage((e) => {
            this.notificationHandler.showNotification();
            if (this.callback) this.callback(this.target, { responseJSON: e.data, additionalData: this.additionalData });
        });
    }
}

export default WorkerParent;
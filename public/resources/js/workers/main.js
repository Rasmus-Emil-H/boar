import NotificationHandler from './NotificationHandler.js';
import WorkerManager from './WorkerManager.js';

class WorkerParent {
    constructor(options) {
        this.options = options;

        this.setProperties();

        this.notificationHandler = new NotificationHandler(options.notificationType);
        this.workerManager = new WorkerManager(this.type, this.data);

        this.body = this.prepareData();

        this.initialize();
    }

    setProperties() {
        this.type = this.options.type;
        this.data = this.options.data;
        this.target = this.options.target;
        this.callback = this.options.cb;
        this.additionalData = this.options.additionalData ?? {};
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
            if (!this.callback) return; 
            
            this.callback(this.target, { responseJSON: e.data, additionalData: this.additionalData });
        });
    }
}

export default WorkerParent;
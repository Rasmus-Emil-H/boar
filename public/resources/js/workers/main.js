class WorkerParent {

    implementationDir = '/resources/js/workers/implementations/';

    constructor(options) {
        this.type = options.type;
        this.data = options.data;
        this.target = options.target;
        this.body = {};
        this.notificationType = options.notificationType;
        this.cb = options.cb;
        this.worker = null;

        this.showInitialNotification();
        this.spawnWorker();
        this.setData();
        this.setDispatchCycle();
    }

    showInitialNotification() {
        if (!this.notificationType) return;

        this.dispatchNotification();
    }

    dispatchNotification(message) {
        window[appName].components.addPushNotification({message: `${message ?? this.notificationType}`, created: new Date().toLocaleTimeString()});
    }

    setData() {
        for(let [key,value] of this.data.entries()) this.body[key] = value;
    }

    checkURL() {
        const containsURL = Object.keys(this.body).some(el => el === 'url');
        if (!containsURL) this.body.url = location.href;        
    }

    setDispatchCycle() {
        this.checkURL();
        this.worker.postMessage(this.body);

        this.worker.onmessage = (e) => {
            this.dispatchNotification();
            if (this.cb) this.cb(this.target, {responseJSON: e.data});
        }
    }

    spawnWorker() {
        this.worker = new Worker(`${this.implementationDir}${this.type}.js`, {data: this.body});
    }
}

export default WorkerParent;
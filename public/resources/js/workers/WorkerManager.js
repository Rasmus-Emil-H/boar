class WorkerManager {
    constructor(type, data, implementationDir = '/resources/js/workers/implementations/') {
        this.type = type;
        this.data = data;
        this.implementationDir = implementationDir;
        this.worker = this.initializeWorker();
    }

    initializeWorker() {
        return new Worker(`${this.implementationDir}${this.type}.js`);
    }

    postMessage(body) {
        this.worker.postMessage(body);
    }

    setOnMessage(callback) {
        this.worker.onmessage = (e) => {
            callback(e);
        };
    }
}

export default WorkerManager;
const DatabaseManager = {
    db: null,

    open(databaseName, version, upgradeCallback) {
        return new Promise((resolve, reject) => {
            const request = window.indexedDB.open(databaseName, version);

            request.onerror = (event) => {
                reject(`Database error: ${event.target.error}`);
            };

            request.onsuccess = (event) => {
                this.db = event.target.result;
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                if (upgradeCallback) {
                    upgradeCallback(db);
                }
            };
        });
    },

    isConnected() {
        return this.db !== null;
    },

    create(objectStoreName, data) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject('Database not connected');
                return;
            }

            const transaction = this.db.transaction(objectStoreName, 'readwrite');
            const objectStore = transaction.objectStore(objectStoreName);
            const request = objectStore.add(data);

            request.onsuccess = () => {
                resolve('Record added successfully');
            };

            request.onerror = (event) => {
                reject(`Error adding record: ${event.target.error}`);
            };
        });
    },

    read(objectStoreName, key) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject('Database not connected');
                return;
            }

            const transaction = this.db.transaction(objectStoreName, 'readonly');
            const objectStore = transaction.objectStore(objectStoreName);
            const request = objectStore.get(key);

            request.onsuccess = (event) => {
                const result = event.target.result;
                if (result) {
                    resolve(result);
                } else {
                    reject(`Record not found for key: ${key}`);
                }
            };

            request.onerror = (event) => {
                reject(`Error reading record: ${event.target.error}`);
            };
        });
    },

    update(objectStoreName, key, newData) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject('Database not connected');
                return;
            }

            const transaction = this.db.transaction(objectStoreName, 'readwrite');
            const objectStore = transaction.objectStore(objectStoreName);
            const request = objectStore.put(newData, key);

            request.onsuccess = () => {
                resolve('Record updated successfully');
            };

            request.onerror = (event) => {
                reject(`Error updating record: ${event.target.error}`);
            };
        });
    },

    delete(objectStoreName, key) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject('Database not connected');
                return;
            }

            const transaction = this.db.transaction(objectStoreName, 'readwrite');
            const objectStore = transaction.objectStore(objectStoreName);
            const request = objectStore.delete(key);

            request.onsuccess = () => {
                resolve('Record deleted successfully');
            };

            request.onerror = (event) => {
                reject(`Error deleting record: ${event.target.error}`);
            };
        });
    },
};

/**
 * Example below if you don't wanna have to think 
 * 
*/

DatabaseManager.open('myDatabase', 1, (db) => {})
    .then(() => {
        console.log('Connected to the database');
        const data = {
            id: 1,
            name: 'John'
        };
        return DatabaseManager.create('myObjectStore', data);
    })
    .then((result) => {
        console.log(result);
        return DatabaseManager.read('myObjectStore', 1);
    })
    .then((data) => {
        console.log('Read data:', data);
        const newData = {
            id: 1,
            name: 'Updated John'
        };
        return DatabaseManager.update('myObjectStore', 1, newData);
    })
    .then((result) => {
        console.log(result);
        return DatabaseManager.delete('myObjectStore', 1);
    })
    .then((result) => {
        console.log(result);
    })
    .catch((error) => {
        console.error(error);
    });
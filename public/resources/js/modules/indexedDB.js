const DatabaseManager = {
    db: null,
    open(databaseName, version, upgradeCallback) {
        return new Promise(function(resolve, reject) {
            const request = window.indexedDB.open(databaseName, version);

            request.onerror = function(event) {
                reject(`Database error: ${event.target.error}`);
            };

            request.onsuccess = function(event) {
                this.db = event.target.result;
                resolve(this.db);
            };

            request.onupgradeneeded = function(event) {
                const db = event.target.result;
                if (upgradeCallback) upgradeCallback(db);
            };
        });
    },
    isConnected() {
        return this.db !== null;
    },
    create(objectStoreName, data) {
        return new Promise(function(resolve, reject) {
            if (!this.db) {
                reject("Database not connected");
                return;
            }

            const transaction = this.db.transaction(objectStoreName, "readwrite");
            const objectStore = transaction.objectStore(objectStoreName);
            const request = objectStore.add(data);

            request.onsuccess = function() {
                resolve("Record added successfully");
            };

            request.onerror = function(event) {
                reject(`Error adding record: ${event.target.error}`);
            };
        });
    },
    read(objectStoreName, key) {
        return new Promise(function(resolve, reject) {
            if (!this.db) {
                reject("Database not connected");
                return;
            }

            const transaction = this.db.transaction(objectStoreName, "readonly");
            const objectStore = transaction.objectStore(objectStoreName);
            const request = objectStore.get(key);

            request.onsuccess = function(event) {
                const result = event.target.result;
                result ? resolve(result) : reject(`Record not found for key: ${key}`);
            };

            request.onerror = function(event) {
                reject(`Error reading record: ${event.target.error}`);
            };
        });
    },
    update(objectStoreName, key, newData) {
        return new Promise(function(resolve, reject) {
            if (!this.db) {
                reject("Database not connected");
                return;
            }

            const transaction = this.db.transaction(objectStoreName, "readwrite");
            const objectStore = transaction.objectStore(objectStoreName);
            const request = objectStore.put(newData, key);

            request.onsuccess = function() {
                resolve("Record updated successfully");
            };

            request.onerror = function(event) {
                reject(`Error updating record: ${event.target.error}`);
            };
        });
    },
    delete(objectStoreName, key) {
        return new Promise(function(resolve, reject) {
            if (!this.db) {
                reject("Database not connected");
                return;
            }

            const transaction = this.db.transaction(objectStoreName, "readwrite");
            const objectStore = transaction.objectStore(objectStoreName);
            const request = objectStore.delete(key);

            request.onsuccess = function() {
                resolve("Record deleted successfully");
            };

            request.onerror = function(event) {
                reject(`Error deleting record: ${event.target.error}`);
            };
        });
    },
};

export default DatabaseManager;

/**
 * Example below if you don"t wanna have to think 
 * 
*/

/*

DatabaseManager.open("BoarIndexedDB", 1, function(db) {})
    .then(function() {
        const data = {id: 1, name: "Boar"};
        return DatabaseManager.create("ApplicationStore", data);
    })
    .then(function(result) {
        console.log(result);
        return DatabaseManager.read("ApplicationStore", 1);
    })
    .then(function(data) {
        const newData = {id: 1, name: "Updated Boar"};
        return DatabaseManager.update("ApplicationStore", 1, newData);
    })
    .then(function(result) {
        return DatabaseManager.delete("ApplicationStore", 1);
    })
    .catch(function(error) {
    });

*/
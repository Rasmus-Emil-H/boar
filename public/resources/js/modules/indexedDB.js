const indexedDBConfigs = {
    store: "Application",
    testConnection: function() {
        const indexedDB = window.indexedDB;
        if(!indexedDB) return false;
        const request = indexedDB.open("TEST", 1);
        return request;
    }
};

export default {
    create: async function(data) {
        return new Promise((resolve, reject) => {

            let request = indexedDBConfigs.testConnection();
            if(request === false || request.error !== null) reject('Inno DB Error');
    
            request.onupgradeneeded = function(request) {
                const db = request?.result;
                const store = db.createOjectStore(indexedDBConfigs.store, { keyPath: "id", autoIncrement: true });
            }
    
            request.onsuccess = function(){
                const db = request.result;
                const txn = db.transaction(indexedDBConfigs.store, "readwrite");
                const store = txn.objectStore(indexedDBConfigs.store);
                store.put({...data});
                txn.oncomplete = function(){
                    db.close();
                    resolve("success");
                }
            }
        });
    },
    delete: async function(id) {
        return new Promise((resolve, reject) => {

            let request = indexedDBConfigs.testConnection();
            if(request === false || request.error !== null) reject('Inno DB Error');

            request.onupgradeneeded = function(request) {
                const db = request?.result;
                const store = db.createOjectStore(indexedDBConfigs.store, { keyPath: "id", autoIncrement: true });
            }
    
            request.onsuccess = function(){
                const db = request.result;
                const txn = db.transaction(indexedDBConfigs.store, "readwrite");
                const store = txn.objectStore(indexedDBConfigs.store);
                id ? store.delete(id) : store.clear();
                txn.oncomplete = function(){
                    db.close();
                    resolve("success");
                }
            }

        });
    },
    get: async function(id) {
        return new Promise((resolve, reject) => {
            
            let request = indexedDBConfigs.testConnection();
            if(request === false || request.error !== null) reject('Inno DB Error');

            request.onupgradeneeded = function(request) {
                const db = request?.result;
                const store = db.createOjectStore(indexedDBConfigs.store, { keyPath: "id", autoIncrement: true });
            }
    
            request.onsuccess = function(){
                const db = request.result;
                const txn = db.transaction(indexedDBConfigs.store, "readwrite");
                const store = txn.objectStore(indexedDBConfigs.store);
                let query;
                query = id ? store.get(id) : store.getAll();
                query.onsucess = function() {
                     resolve(query.result);
                }
                txn.oncomplete = function(){
                    db.close();
                }
            }

        });
    }    
}
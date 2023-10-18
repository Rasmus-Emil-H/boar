const databaseName = 'db';
const objectStoreName = 'dbobjects';

const actions = {
  write: 'readwrite',
  read: 'readonly'
}

class IndexedDBManager {
  constructor() {
    this.db = null;
  }

  async openDatabase() {
    return new Promise((resolve, reject) => {
      const request = window.indexedDB.open(databaseName, 1);

      request.onupgradeneeded = (event) => {
        const db = event.target.result;
        if (!db.objectStoreNames.contains(objectStoreName)) {
          db.createObjectStore(objectStoreName, { keyPath: 'id', autoIncrement: true });
        }
      };

      request.onsuccess = (event) => {
        this.db = event.target.result;
        resolve(this.db);
      };

      request.onerror = (event) => {
        reject(event.target.error);
      };
    });
  }

  async createRecord(data) {
    const db = await this.openDatabase();
    return new Promise((resolve, reject) => {
      const transaction = db.transaction(objectStoreName, actions.write);
      const objectStore = transaction.objectStore(objectStoreName);

      const request = objectStore.add(data);

      request.onsuccess = (event) => {
        resolve(event.target.result);
      };

      request.onerror = (event) => {
        reject(event.target.error);
      };
    });
  }

  async readRecord(id) {
    const db = await this.openDatabase();
    return new Promise((resolve, reject) => {
      const transaction = db.transaction(objectStoreName, actions.read);
      const objectStore = transaction.objectStore(objectStoreName);

      const request = objectStore.get(id);

      request.onsuccess = (event) => {
        resolve(event.target.result);
      };

      request.onerror = (event) => {
        reject(event.target.error);
      };
    });
  }

  async updateRecord(id, newData) {
    const db = await this.openDatabase();
    return new Promise(async (resolve, reject) => {
      const transaction = db.transaction(objectStoreName, actions.write);
      const objectStore = transaction.objectStore(objectStoreName);
      const getRequest = objectStore.get(id);
      getRequest.onsuccess = function(event) {
        let data = event.target.result;
        if (newData.genericEntry) {
          data.genericEntries[newData.genericEntry].data = newData;
          var request = objectStore.put({ id, ...data });
        } else {
          var request = objectStore.put({ id, ...newData });
        } 

        request.onsuccess = (event) => {
          resolve(data);
        };

        request.onerror = (event) => {
          reject(event.target.error);
        };
      };
    });
  }

  async deleteRecord(id) {
    const db = await this.openDatabase();
    return new Promise((resolve, reject) => {
      const transaction = db.transaction(objectStoreName, actions.write);
      const objectStore = transaction.objectStore(objectStoreName);

      const request = objectStore.delete(id);

      request.onsuccess = () => {
        resolve();
      };

      request.onerror = (event) => {
        reject(event.target.error);
      };
    });
  }

  async getAllRecords(id) {
    const db = await this.openDatabase();
    return new Promise((resolve, reject) => {
      const transaction = db.transaction(objectStoreName, actions.read);
      const objectStore = transaction.objectStore(objectStoreName);

      const request = objectStore.getAll(id);

      request.onsuccess = (event) => {
        delete event.target.result[0].id;
        resolve({genericEntries: event.target.result[0]});
      };

      request.onerror = (event) => {
        reject(event.target.error);
      };
    });
  }
}

export default IndexedDBManager;
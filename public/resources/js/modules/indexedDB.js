const databaseName = 'db';
const objectStoreName = 'dbobjects';

const actions = {
  write: 'readwrite',
  read: 'readonly'
}

const messages = {
  invalidEntry: "Invalid entry"
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

      request.onerror = (event) => { reject(event.target.error); };
    });
  }

  async createRecord(data) {
    const db = await this.openDatabase();
    return new Promise((resolve, reject) => {
      const transaction = db.transaction(objectStoreName, actions.write);
      const objectStore = transaction.objectStore(objectStoreName);

      const request = objectStore.add(data);

      request.onsuccess = (event) => { resolve(event.target.result); };

      request.onerror = (event) => { reject(event.target.error); };
    });
  }

  async readRecord(id) {
    const db = await this.openDatabase();
    return new Promise((resolve, reject) => {
      const transaction = db.transaction(objectStoreName, actions.read);
      const objectStore = transaction.objectStore(objectStoreName);

      const request = objectStore.get(id);

      request.onsuccess = (event) => { resolve(event.target.result); };

      request.onerror = (event) => { reject(event.target.error); };
    });
  }

  async updateSpecificEntryWithinRecord(id, newData) {
    const db = await this.openDatabase();
    return new Promise(async (resolve, reject) => {
      const transaction = db.transaction(objectStoreName, actions.write);
      const objectStore = transaction.objectStore(objectStoreName);
      const getRequest = objectStore.get(id);
      getRequest.onsuccess = function(event) {
        if (!event.target.result[newData.data.id] || !event.target.result[newData.data.id].data[newData.data.targetProp]) {
          console.log(messages.invalidEntry);
          reject(messages.invalidEntry);
        };
        
        console.log(newData.data.targetProp, newData.data);

        event.target.result[newData.data.id].data[newData.data.targetProp][`${id}notSynced${Date.now()}`] = newData.data;
        console.log(event.target.result);
        let request = objectStore.put({id, ...event.target.result});

        request.onsuccess = (event) => { resolve(event);};
        request.onerror = (event) => { reject(event.target.error);};
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
        if (newData.trip) {
          data[newData.trip].data.information = newData;
          var request = objectStore.put({ id, ...data });
        } else {
          var request = objectStore.put({ id, ...newData });
        }
        
        request.onsuccess = (event) => { resolve(data); };
        request.onerror = (event) => { reject(event.target.error);};
      };
    });
  }

  async deleteRecord(id) {
    const db = await this.openDatabase();
    return new Promise((resolve, reject) => {
      const transaction = db.transaction(objectStoreName, actions.write);
      const objectStore = transaction.objectStore(objectStoreName);

      const request = objectStore.delete(id);

      request.onsuccess = () => { resolve(); };
      request.onerror = (event) => { reject(event.target.error);};
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
        resolve({trips: event.target.result[0]});
      };

      request.onerror = (event) => { reject(event.target.error); };
    });
  }
}

export default IndexedDBManager;
export default {
    init: async function() {
        const device = await navigator.bluetooth.requestDevice();
        const server = device.gatt.connect();
        const data   = await server.getPrimaryService();
        const measurement = await data.getCharataristics();
        await measurement.startNotifications();
        measurement.addEventListener("charataristicsvaluechanged", function(data) {
            console.log(data);
        });
    }
}
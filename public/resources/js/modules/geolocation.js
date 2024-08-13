export default {
    init: function() {
        // window.navigator.geolocation.getCurrentPosition(function(res) {
        //     console.log(res);
        // });

        window.addEventListener("devicemotion", function(event) {
            const x = event.accelerationIncludingGravity.x;
            const y = event.accelerationIncludingGravity.y;
            const z = event.accelerationIncludingGravity.z;
            const acceleration = event.acceleration;
            const rotation = event.rotationRate;
            console.log(`
                x: ${x} -
                y: ${y} -
                z: ${z} -
                acceleration: ${acceleration} -
                rotation: ${rotation} -
            `);
        });
    },
    promptLocationPermissions: function() {
        window.navigator.geolocation.getCurrentPosition(function(res) {
            return res;
        });
    },
    getLocation: function() {
        let lat = 0;
        let long = 0;

        if(!navigator.geolocation) return;

        return new Promise(function(resolve, reject) {
            navigator.geolocation.getCurrentPosition(async function(position) {
                lat =  await position.coords.latitude;
                long = await position.coords.longitude;
    
                resolve([lat,long]);
            });
        });
    },
    updateCoords: async function() {
        const response = await this.getLocation();
        localStorage.setItem('coords', JSON.stringify(response));
    }
}
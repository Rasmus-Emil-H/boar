window[appName].geolocation = {
    init: function() {
        window.navigator.geolocation.getCurrentPosition(function(res) {
            console.log(res);
        });
    },
    promptLocationPermissions: function() {
        window.navigator.geolocation.getCurrentPosition(function(res) {
            return res;
        });
    },
    getLocation: function() {
        if(!navigator.geolocation) return;

        return new Promise(function(resolve, reject) {
            navigator.geolocation.getCurrentPosition(async function(position) {
                resolve([position.coords.latitude, position.coords.longitude]);
            }, null, {enableHighAccuracy: true, maximumAge: 0});
        });
    },
    updateCoords: async function() {
        const response = await this.getLocation();
        localStorage.setItem('coords', JSON.stringify(response));
    }
}
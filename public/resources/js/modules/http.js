export default {
    fetch: function(url, method, headers, body) {
        return new Promise(function(resolve, reject) {
          fetch(url, {method, headers, body: JSON.stringify(body)}).then(function(res) {return res.json()}).then(function(data) { resolve(data); });
        });
    }
}
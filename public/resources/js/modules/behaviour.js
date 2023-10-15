export default {
    init: function() {
        $('form').on('submit', function(e) {
            e.preventDefault();
            $.post(location.href, $(this).serializeArray(), function() {

            });
        }); 
    },
    syncData: function(data) {
        const body = new FormData();
        for(let obj in data) body.append(obj, data[obj]);
        fetch(location.href, { method: 'POST', body })
            .then(res => {
                console.log(res);
            })
            .catch(err => {
                console.log(err);
            });
    }
}
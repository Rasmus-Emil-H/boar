export default {
    init: function() {
        $('form').on('submit', function(e) {
            e.preventDefault();
            let _href = $(this).find('#submit');
            let _text = _href.text();
            _href.html(boar.components.spinner());
            $.post($(this).find('.url').data('url'), boar.utilities.fetchFormInputs(this), function(r) {
                _href.text(_text);
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
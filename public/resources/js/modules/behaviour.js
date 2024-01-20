/**
|----------------------------------------------------------------------------
| Behavioral initializers is bootstrapped here
|----------------------------------------------------------------------------
|
| @author RE_WEB
|
*/

export default {
    init: function() {
        this.globalNodeEventListeners();
    },
    globalNodeEventListeners: function() {
        $('form').submit(function (e) {
            e.preventDefault();
            const form = $(this);
            const submitButton = form.find('[type="submit"]');
            const _text = submitButton.html();

            submitButton.attr('disabled', true);
            submitButton.html(autologik.components.loader());
            $.ajax({type: form.attr('method'), url: form.attr('action'), data: form.serialize(),
                success: function (response, status) {
                    if (response.redirect) window.location.replace(response.redirect);
                },
                error: function (xhr, status, error) {
                    
                }
            }).always(function() {
                submitButton.attr('disabled', false);
                submitButton.html(_text);
            });
        });
    }
}
export default {
    init: function() {
        $('form').on('submit', function(e) {
            e.preventDefault();
            let form = boar.utilities.initializeObjects('form input');
            console.log(form);
            $.post($(this).find('.url').data('url'), form, function(response) {

            });
        });
    }
}
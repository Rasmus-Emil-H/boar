export default {
    init: function() {
        $('form').on('submit', function(e) {
            e.preventDefault();
            $.post($(this).find('.url').data('url'), {}, function() {

            });
        });
    }
}
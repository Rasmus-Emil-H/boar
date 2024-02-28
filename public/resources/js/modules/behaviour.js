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

        const self = this;

        $('form').submit(function (e) {
            e.preventDefault();
            const form = $(this);
            const submitButton = form.find('[type="submit"]');
            const _text = submitButton.html();

            submitButton.attr('disabled', true);
            submitButton.html(boar.components.loader());
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

        $('input[type="file"]').on('change', function(e) {
            self.uploadFile(e.target);
        });
    },
    uploadFile: async function(target) {
        const csrf = $('[name="eg-csrf-token-label"]').val();
        const parent = $(target).closest('div.file-upload-parent');
        const entityType = parent.data('entityType');
        const entityID = parent.data('entityId');
        const type = parent.data('type');

        if (!csrf || !parent || !entityType || !entityType) {
            alert('Required field(s) are missing!'); 
            return;
        }
        
        const _backupNodes = parent.html();
        parent.html(boar.components.loader());

        let body = new FormData();
        body.append("file", target.files[0]);
        body.append('entityType', entityType);
        body.append('entityID', entityID);
        body.append('eg-csrf-token-label', csrf);
        body.append('type', type);

        await fetch('/file', {method: "POST", body})
            .then(function(response) {
                parent.html(_backupNodes);
            });
    }
}
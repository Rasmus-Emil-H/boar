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

        $('body').on('submit', 'form', function(e) {
            e.preventDefault();
            const form = $(this);
            return self.submitForm(form);
        });

        $('.globalFileUploader').on('change', async function(e) {
            await self.uploadFile(e.target);
        });
    },
    submitForm: function(form) {
        return new Promise(function(resolve, reject) {
            const submitButton = form.find('button[type="submit"]');
            const _text = submitButton.html();
    
            submitButton.attr('disabled', true);
            submitButton.html(boar.components.loader());
            
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: new FormData(form[0]),
                contentType: false,
                cache: false,
                processData: false,
                success: function(response, status) {
                    if (typeof response.responseJSON === 'object') 
                        boar.components.toast(response.responseJSON.message ?? 'Success', boar.constants.mdbootstrap.SUCCESS_CLASS);
                    else 
                        boar.components.toast(response.responseJSON, boar.constants.mdbootstrap.SUCCESS_CLASS);
                    resolve(response);
                },
                error: function(xhr, status, error) {
                    reject(xhr);
                }
            }).always(function(res) {
                submitButton.attr('disabled', false);
                submitButton.html(_text);
                resolve(res);
            });
        });
    },
    uploadFile: async function(target) {
        const csrf = $('[name="eg-csrf-token-label"]').val();
        const parent = $(target).closest('div.file-upload-parent');
        const entityType = parent.data('entityType');
        const entityID = parent.data('entityId');
        const type = parent.data('type');
    
        if (!csrf || !parent || !entityType || !entityID) {
            alert('Required field(s) are missing!');
            return;
        }
    
        const _backupNodes = parent.html();
        parent.html(boar.components.loader());
    
        const body = new FormData();
        body.append("file", target.files[0]);
        body.append('entityType', entityType);
        body.append('entityID', entityID);
        body.append('eg-csrf-token-label', csrf);
        body.append('type', type);
    
        try {
            const response = await fetch('/file', { method: "POST", body });
            if (!response.ok) throw new Error('Network response was not ok');
            const jsonResponse = await response.json();
            boar.components.toast(jsonResponse);
            parent.html(_backupNodes);
            return jsonResponse;
        } catch (error) {
            console.error('Error uploading file:', error);
            parent.html(_backupNodes);
            throw error;
        }
    }
}
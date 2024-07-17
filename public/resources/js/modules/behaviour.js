/**
|----------------------------------------------------------------------------
| Behavioral initializers is bootstrapped here
|----------------------------------------------------------------------------
|
| @author RE_WEB
|
*/

window[appName].behaviour = {
    init: function() {
        this.globalNodeEventListeners();
    },
    globalNodeEventListeners: function() {

        const self = this;

        $('body').on('submit', 'form', function(e) {
            if ($(e.target).attr('method') === 'GET') return;
            e.preventDefault();
            const form = $(this);
            return self.submitForm(form);
        });

        $('.globalFileUploader').on('change', async function(e) {
            await self.uploadFile(e.target);
        });
    },
    checkRequiredFormFields: async function(form) {
        let canProceed = true;

        await $(form).find('input:not(:hidden), select, textarea').each(function(i, e) {
            const el = $(e);
            if (el.attr('required') && !el.val()) {
                el.addClass('border border-danger');
                canProceed = false;
            }
        });

        return canProceed;
    },
    submitForm: async function(form) {
        const self = this;

        const checkRequiredFormFields = await self.checkRequiredFormFields(form[0]) 
        if (!checkRequiredFormFields) return;

        const fd = new FormData(form[0]);

        $(form[0]).find('[type="checkbox"]').each(function() {
            fd.set($(this).prop('name'), Number($(this).prop('checked')));
        });
        
        return new Promise(function(resolve, reject) {
            const submitButton = form.find('button[type="submit"]').last();
            const _text = submitButton.html();
            submitButton.attr('disabled', true);
            submitButton.html(window[appName].components.loader());

            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: fd,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response, status) {
                    self.checkSubmittedFormResponse(response);
                    resolve(response);
                },
                error: function(xhr, status, error) {
                    window[appName].components.toast(xhr.responseJSON, window[appName].constants.mdbootstrap.ERROR_CLASS);
                    reject(xhr);
                }
            }).always(function(res) {
                submitButton.attr('disabled', false);
                submitButton.html(_text);
                resolve(res);
            });
        });
    },
    checkSubmittedFormResponse: function(response) {
        if (response.responseJSON) {
            if (typeof response.responseJSON === 'object') 
                window[appName].components.toast(response.responseJSON.message ?? 'Success', window[appName].constants.mdbootstrap.SUCCESS_CLASS); 
            else 
                window[appName].components.toast(response.responseJSON, window[appName].constants.mdbootstrap.SUCCESS_CLASS);
        }
        
        if (response.redirect) window.location.replace(response.redirect);
    },
    referenceGETForm(form) {
        let href = '?';
        const fd = new FormData(form);
        for (const pair of fd.entries()) href += `&${pair[0]}=${pair[1]}`;
        location.href = href;
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
        parent.html(window[appName].components.loader());
    
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
            window[appName].components.toast(jsonResponse);
            parent.html(_backupNodes);
            return jsonResponse;
        } catch (error) {
            console.error('Error uploading file:', error);
            parent.html(_backupNodes);
            throw error;
        }
    }
}


window[appName].behaviour.init();
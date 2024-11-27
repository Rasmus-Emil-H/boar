/**
|----------------------------------------------------------------------------
| Behavioral initializers is bootstrapped here
|----------------------------------------------------------------------------
|
| @author RE_WEB
|
*/

import WorkerParent from '/resources/js/workers/main.js';

window[appName].behaviour = {
    init: function() {
        this.globalNodeEventListeners();
    },
    globalNodeEventListeners: function() {
        const self = this;

        $('body').on('submit', 'form', function(e) {
            if ($(e.target).attr('method') === 'GET') return;
            e.preventDefault();
            return self.submitForm($(this));
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

        if (!canProceed) {
            const translation = await window[appName].i18n.translate('You cant submit empty data');
            window[appName].components.toast(translation.responseJSON ?? 'You can\'t submit empty data', window[appName].constants.mdbootstrap.ERROR_CLASS);
        }

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

        if (!navigator.onLine) fd.set('InitialClientRequestCreatedTimestamp', Date.now());
        
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
                    window[appName].components.toast(xhr.responseJSON ?? error, window[appName].constants.mdbootstrap.ERROR_CLASS);
                    reject(xhr);
                }
            }).always(function(res) {
                submitButton.attr('disabled', false);
                submitButton.html(_text);
                resolve(res);
            });
        }).catch(function(promiseError) {
            console.log(promiseError);
        });
    },
    checkSubmittedFormResponse: function(response) {
        if (response.redirect) return window.location.replace(response.redirect);
        if (!response.responseJSON) return;

        window[appName].components.toast((
            typeof response.responseJSON === 'object' ? response.responseJSON.message ?? 'Success' : response.responseJSON 
        ), window[appName].constants.mdbootstrap.SUCCESS_CLASS);  
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
    
        if (!window.Worker) {
            try {
                const response = await fetch('/file', {method: "POST", body});
                const jsonResponse = await response.json();
                button.html(_backupNodes);
                return jsonResponse;
            } catch (error) {
                button.html(_backupNodes);
                throw error;
            }
        } else {
            button.html(_backupNodes);
            new WorkerParent({data: body, cb, target, type: 'std', notificationType: 'Uploading file'});
        }
    },
    displayPushUpdate: function() {
        console.log("do math");
    },
}


window[appName].behaviour.init();
export default {
    toast: async function(message) {
        const toast = `
          <div class="toast" id="customToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              <strong class="me-auto">Boar</strong>
              <small>${new Date().toLocaleDateString()}</small>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-black">
              ${message}
            </div>
          </div>
        `;

        await $('body').append(toast);
        bootstrap.Toast.getOrCreateInstance($('#customToast')).show();
    },
    initModal: function(title, body, id, hidefooter = null, buttonHider = null, buttonText = '', cb = null) {
        let modal = document.createElement('div');
        let modalID = Math.round(Math.random() * 100000);
        let reevalTitle = title;
        modal.innerHTML = `
            <div class="modal fade" id="generatedModalContainer${modalID}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">${reevalTitle}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">${body}</div>
                  <div class="modal-footer ${hidefooter ? 'd-none' : ''}">
                    <button type="button" class="btn btn-primary ${buttonHider === null ? 'd-none' : ''}" id="${id}">${buttonText}</button>
                    <button type="button" class="btn btn-secondary" onclick="DAL.removeGeneratedModal(event, '#generatedModalContainer${modalID}')">Close</button>
                  </div>
                </div>
              </div>
            </div>
        `;
        document.querySelector('body').appendChild(modal);
        $(`#generatedModalContainer${modalID}`).modal('show');
        if(!title) $('.modal-header').css({borderBottom: '0px'});
        if(cb) cb();
    },
    loader: function() {
      return `<div class="spinner-border text-primary d-flex justify-content-center" role="status"><span class="sr-only">Loading...</span></div>`;
    }
}
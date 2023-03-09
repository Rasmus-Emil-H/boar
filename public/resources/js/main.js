window.boar = {
    initModal: function(title, body, id, cb = null, hidefooter = null, buttonHider = null) {
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
                    <button type="button" class="btn btn-primary ${buttonHider === null ? 'd-none' : ''}" id="${id}">Create file</button>
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
    fetch: function(url, method, headers, body) {
        return new Promise(function(resolve, reject) {
            fetch(url, {method, headers, body: JSON.stringify(body)}).then(function(res) {return res.json()}).then(function(data) { resolve(data); });
        });
    },
    initializeObjects: function(root) {
        let totalObject = {};
        $(`${root}`).each(function(i,e) {
            let href = $(this);
            totalObject[href.data('id')] = {};
            $(this).find('input').each(function(i,e) {
                totalObject[`${href.data('id')}`][`${$(this).attr('name')}`] = $(this).val();
            });
        });
        return totalObject;
    }
}
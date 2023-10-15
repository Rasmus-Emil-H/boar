<h1>Home</h1>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        let listItems = document.querySelectorAll('li span');

        for(let i = 0; i < listItems.length; i++) {
            listItems[i].addEventListener('click', async function(e) {
                await window.boar.behaviour.syncData({id: e.target.parentNode.dataset.id, action: 'delete'});
                e.target.parentNode.remove();
            });
        }

        document.querySelector('#file-upload').addEventListener('change', async function(e) {
            const file = event.target.files[0];
            if(!file) return;
            await navigator.serviceWorker.controller.postMessage({ action: 'cache-file', file });
            window.boar.behaviour.syncData({file: file});
        });
    })
</script>
<div class="h-100">
    <div class="w-75 mx-auto mt-2">
        <div class="request">
            <div class="form-floating mx-auto my-auto">
                <form method="POST">
                    <textarea name="Input" class="w-100 rounded vh50 noresize p-2" placeholder="echo 123;" id="floatingTextarea2"></textarea>
                    <button type="submit" id="php" class="btn btn-success w-100">Debug</button>
                    <?= CSRFTokenInput(); ?>
                </form>
            </div>
        </div>
    </div>

    <div class="receive w-75 mx-auto mt-2">
        <div class="form-floating mx-auto my-auto" style="margin-left:2px !important;">
            <textarea name="Input" class="w-100 rounded vh50 noresize p-2 bg-white" disabled id="inject"></textarea>
        </div>
    </div>
</div>

<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        $('#php').on('click', async function(e) {
            e.preventDefault();
            const response = await window[appName].behaviour.submitForm($(e.target).closest('form'));
            $('#inject').html(response.php);
        })
    });
</script>
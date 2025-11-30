<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm w-100" style="max-width: 400px;">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label"><?= ths('Name'); ?></label>
                    <input id="name" type="text" name="name" class="form-control" required value="<?= hs($user->get('Name')); ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label"><?= ths('Email'); ?></label>
                    <input id="email" type="email" name="email" class="form-control" required value="<?= hs($user->get('Email')); ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label"><?= ths('Password'); ?></label>
                    <input id="password" type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                </div>

                <?= CSRFTokenInput(); ?>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><?= ths('Save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

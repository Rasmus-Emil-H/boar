<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4 shadow-lg" style="max-width: 420px; width: 100%;">
        <form method="POST" action="/auth/login">
            <h4 class="mb-4 text-center"><?= env('appName'); ?></h4>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    class="form-control"
                    required 
                    autofocus
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    class="form-control"
                    required
                >
            </div>

            <?= CSRFTokenInput(); ?>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">
                    <?= ths('Log in'); ?>
                </button>
                <a href="signup" class="btn btn-primary">
                    <?= ths('Create account'); ?>
                </a>
            </div>
        </form>
    </div>
</div>

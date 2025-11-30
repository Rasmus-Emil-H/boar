<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4 shadow-lg" style="max-width: 420px; width: 100%;">
        <form method="POST">
            <h4 class="mb-4 text-center"><?= env('appName'); ?></h4>

            <div class="mb-3">
                <input 
                    type="email" 
                    name="email" 
                    required 
                    class="form-control" 
                    placeholder="Email"
                    autofocus
                >
            </div>

            <div class="mb-3">
                <input 
                    type="text" 
                    name="name" 
                    required 
                    class="form-control" 
                    placeholder="Name"
                >
            </div>

            <div class="mb-3">
                <input 
                    type="password" 
                    name="password" 
                    required 
                    class="form-control" 
                    placeholder="Password"
                >
            </div>

            <?= CSRFTokenInput(); ?>

            <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <?= ths('Create account'); ?>
                </button>

                <a href="/" class="btn btn-outline-secondary">
                    <?= ths('Go back'); ?>
                </a>
            </div>
        </form>
    </div>
</div>

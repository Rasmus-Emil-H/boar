<div class="container height-full content-center">
    <div class="row">
        <div class="col-lg-4 col-xxl-4 mx-auto">
            <div class="card">
                <div class="card-header border-0 py-5">
                    <h3 class="text-center mb-0"><?= \app\core\src\miscellaneous\CoreFunctions::ths('Reset password'); ?></h3>
                </div>
                <div class="card-body pt-0">
                    <div class="tab-content" id="ex1-content">
                        <div class="tab-pane fade show active" id="ex1-tabs-1" role="tabpanel" aria-labelledby="ex1-tab-1">
                            <form class="login-form center flex-column" method="POST" action="/auth/resetPassword">
                                <div class="form-group">
                                    <label class="form-label mb-1"><?= \app\core\src\miscellaneous\CoreFunctions::ths('Email'); ?></label>
                                    <input autofocus type="email" required name="email" class="form-control form-control-lg">
                                </div>
                                <?= (new \app\core\src\tokens\CsrfToken())->insertHiddenToken(); ?>
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3"><?= \app\core\src\miscellaneous\CoreFunctions::ths('Request password reset'); ?></button>
                                <a href="/auth/login" class="btn btn-secondary btn-lg w-100"><?= \app\core\src\miscellaneous\CoreFunctions::ths('Back'); ?></a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
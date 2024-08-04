<style>

    html, body {
        height: 100%;
    }

    * {
        margin: 0 !important;
        padding: 0 !important;
        font-weight: 100;
    }

    .fullscreen {
        height: 100%;
        width: 100%;
    }

    .center {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

<div class="fullscreen center">
    <h4><?= $exception->getMessage(); ?></h4>
</div>
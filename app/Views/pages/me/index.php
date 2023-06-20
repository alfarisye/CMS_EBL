<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>

<main id="main" class="main">
    <section class="row">
        <?php if (session()->getFlashdata('message')) : ?>
            <div class="alert alert-warning" role="alert">
                <p><?= session()->getFlashdata('message') ?></p>
            </div>
        <?php endif ?>
        <div class="card">
            <div class="card-body mt-3">
                <form action="<?= site_url('me/upload') ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="username" value="<?= session()->get('username') ?>">
                    <div class="col-4">
                        <label class="form-label" for="user_profile">Upload User Image (max: 5 MB)</label>
                    </div>
                    <div class="col-8">
                        <input class="form-control p-1 text-xs rounded-sm shadow-sm" type="file" name="user_profile" id="user_profile" required>
                    </div>
                    <input class="mt-3 btn btn-primary" type="submit" value="Save" />
                </form>
            </div>
        </div>
    </section>
</main>

<?= $this->endSection() ?>
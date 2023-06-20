<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<main id="main" class="main" style="background-image:'<?= base_url('assets/img/batu.jpeg')?>'">
    <div class="pagetitle">
        <h1>Welcome</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/landing") ?>">Welcome</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card p-5">
                <div class="banner-left">
					<img src="<?= base_url('assets/img/logo.png')?>" width="250px">
                    <h1 class="text-center">WELCOME TO </h1>
                    <h2 class="text-center ">Coal Monitoring System <br> PT Energi Batubara Lestari </h2>
                    <img src="<?= base_url('assets/img/Batu.jpeg')?>" width="100%">
				</div>
                </div>
            </div>
        </div>
    </section>
</main>

<?= $this->endSection() ?>
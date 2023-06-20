<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->
<main id="main" class="main">
    <div>
        <div class="pagetitle">
            <h1>Purchasing - Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Purchasing</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("/inventory/dashboard") ?>">Dashboard</a></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Purchasing Dashboard </h5>
                    <div>
                        <?= view('pages/purchasing/part1'); ?>
                        <?= view('pages/purchasing/part1b'); ?>
                    </div>
                    <div>
                        <?= view('pages/purchasing/part2') ?> 
                    </div>
                </div>
            </div>
        </section>
    </div>

</main><!-- End #main -->


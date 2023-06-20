<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Update Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("sales") ?>">Sales</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/Invoice") ?>">Invoices</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Invoices</h5>
                    <div class="d-grid gap-2 col-6">
                        <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('sales/sales-inv') ?>" >                       
                            Sales Invoice
                        </a>
                        <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('despatchinvoice') ?>" >
                            Despatch Invoice
                        </a>
                        <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('sales/royaltyinvoice') ?>" >
                            Royalty Invoice
                        </a>
                        <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('demurage-invoice') ?>" >
                            Demurage Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?= $this->endSection() ?>
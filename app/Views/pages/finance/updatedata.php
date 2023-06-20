<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Update Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance") ?>">Finance</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/updatedata") ?>">Update Data</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update Data Finance</h5>
                    <div class="d-grid gap-2 col-6">
                        <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('finance/pph22') ?>" >                       
                            Bukti Potong PPh22 
                        </a>
                        <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('finance/cvpanlysis') ?>" >
                            Additional for CVP Analysis Report
                        </a>
                        <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('finance/rkap') ?>" >
                            Input RKAP
                        </a>
                         <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('finance/budgetfinance') ?>" >
                            Budget Finance
                        </a>
                        <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('sales/costmining') ?>" >
                            Cost Mining
                        </a>
                        <!-- <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('finance/updateproductiondata') ?>" >
                            Update Production Data
                        </a>
                        <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('finance/updateproductionvendor') ?>" >
                            Update Production Vendor
                        </a> -->
                        <!-- <a type="button" class="ms-2 my-2 btn btn-success" href="<?= site_url('finance/addcost') ?>" >
                            Add Cost Mining
                        </a> -->
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?= $this->endSection() ?>
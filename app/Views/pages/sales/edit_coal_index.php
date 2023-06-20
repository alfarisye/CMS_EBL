<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Sales - Coal Index - Edit Coal Index</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <!-- //! this will be the filter -->
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit Coal Index</h5>
                        <!-- notification -->
                            <?php if (session()->getFlashdata('message')) : ?>
                                <div class="alert alert-warning" role="alert">
                                    <p><?= session()->getFlashdata('message') ?></p>
                                </div>
                            <?php endif ?>
                        <!-- end notification -->
                        <form id="editcoalindex" action="<?= site_url("/sales/coal/update") ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="col-md-12 position-relative mb-3">
                                <label for="month" class="form-label">Month</label>
                                <select class="form-control" name="month" id="month">
                                    <option value="1" <?= 1 == $month_index['month_index'] ? 'selected' : null ?>>Januari</option>
                                    <option value="2" <?= 2 == $month_index['month_index'] ? 'selected' : null ?>>Februari</option>
                                    <option value="3" <?= 3 == $month_index['month_index'] ? 'selected' : null ?>>Maret</option>
                                    <option value="4" <?= 4 == $month_index['month_index'] ? 'selected' : null ?>>April</option>
                                    <option value="5" <?= 5 == $month_index['month_index'] ? 'selected' : null ?>>Mei</option>
                                    <option value="6" <?= 6 == $month_index['month_index'] ? 'selected' : null ?>>Juni</option>
                                    <option value="7" <?= 7 == $month_index['month_index'] ? 'selected' : null ?>>July</option>
                                    <option value="8" <?= 8 == $month_index['month_index'] ? 'selected' : null ?>>Agustus</option>
                                    <option value="9" <?= 9 == $month_index['month_index'] ? 'selected' : null ?>>September</option>
                                    <option value="10" <?= 10 == $month_index['month_index'] ? 'selected' : null ?>>Oktober</option>
                                    <option value="11" <?= 11 == $month_index['month_index'] ? 'selected' : null ?>>November</option>
                                    <option value="12" <?= 12 == $month_index['month_index'] ? 'selected' : null ?>>Desember</option>
                                </select>                            
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Index Type</label>
                                <div class="col-sm-12">
                                    <select name="index_type" id="index_type" class="form-select" aria-label="Default select example">
                                        <?php foreach ($coal_index as $coal_index) : ?>
                                            <option value="<?= $coal_index['index_type'] ?>"<?= $coal_index['index_type'] == $month_index['index_type'] ? 'selected' : null ?>><?= $coal_index['index_type']?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 position-relative mb-3">
                                <label for="yearIndex" class="form-label">Year Index</label>
                                <input class="form-control" type="number" id="yearIndex" name="yearIndex" placeholder="2023" value="<?= $month_index['year_index'];?>" >
                            </div>
                            <div class="col-md-12 position-relative mb-3">
                                <label for="typeIndex" class="form-label">Date Index</label>
                                <input class="form-control" type="date" id="dateIndex" name="dateIndex" placeholder="dateIndex" value="<?= $month_index['date_index'];?>" >
                            </div>
                            <div class="col-md-12 position-relative mb-3">
                                <label for="month" class="form-label">Index Qty</label>
                                <input type="text" class="form-control" id="index_qty" name="index_qty" value="<?= $month_index['index_qty'] ?>" required>
                            </div>
                            <div class="col-sm-4">
                                <input name="id_coalindex" type="text" class="form-control" value="<?= $month_index['id_coalindex'] ?>" hidden></input> 
                            </div>
                            <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>                               
                                <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                                <script>
                                    function getData(){
                                        window.location.href="<?= site_url() ?>"+`/sales/coal`;
                                    }   
                                </script>               
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
        </div>
    </section>
</main><!-- End #main -->

<?= $this->endSection() ?>
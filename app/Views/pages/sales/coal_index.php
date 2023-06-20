<?= $this->extend('templates/layout') ?>
<?php
$bulan = [
    1 => "Januari",
    2 => "Februari",
    3 => "Maret",
    4 => "April",
    5 => "Mei",
    6 => "Juni",
    7 => "Juli",
    8 => "Agustus",
    9 => "September",
    10 => "Oktober",
    11 => "November",
    12 => "Desember"
  ];
?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Coal Index</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <!-- <li class="breadcrumb-item"><a href="<?= site_url("finance") ?>">Finance</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/updatedata") ?>">Update Data</a></li> -->
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body pt-5">
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#coal">
                        <i class="bi bi-plus me-1"></i> Add Data
                    </button>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Month</th>
                                <th scope="col">Year</th>
                                <th scope="col">Index Type</th>
                                <th scope="col">Index Qty</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($t_coalindex as $form) : ?>
                                <tr>
                                    <td><?= $bulan[$form['month_index']] ?></td>
                                    <td><?= $form['year_index'] ?></td>
                                    <td><?= $form['index_type'] ?></td>
                                    <td><?= $form['index_qty'] ?></td>
                                    <td class="col"> 
                                        <a href="<?= site_url('/sales/coal/edit/') . urlencode($form['id_coalindex']) ?>" class="btn btn-primary btn-sm col">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a onclick="return confirm('Are you sure?')" href="<?= site_url('/sales/coal/delete/') ?><?= $form['id_coalindex'] ?>" class="btn btn-danger btn-sm col">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="modal fade" id="coal" tabindex="-1">
                        <form action="<?= site_url("/sales/coal/add") ?>" method="POST" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Data Coal Index</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-md-12 position-relative mb-3">
                                            <label for="month" class="form-label">Month</label>
                                            <select class="form-control" name="month" id="month">
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 position-relative mb-3">
                                            <label for="yearIndex" class="form-label">Year Index</label>
                                            <input class="form-control" type="number" id="yearIndex" name="yearIndex" placeholder="2023" value="<?= date('Y');?>" >
                                        </div>
                                        <div class="col-md-12 position-relative mb-3">
                                            <label for="typeIndex" class="form-label">Date Index</label>
                                            <input class="form-control" type="date" id="dateIndex" name="dateIndex" placeholder="dateIndex" value="<?= date('Y-m-d');?>" >
                                        </div>
                                        <div class="col-md-12 position-relative mb-3">
                                            <label for="typeIndex" class="form-label">Index Type</label>
                                            <select class="form-control" name="typeIndex" id="typeIndex">
                                                <option value="Ici420">Ici420</option>
                                                <option value="EBL">EBL</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 position-relative mb-3">
                                            <label for="indexQty" class="form-label">Kurs (dir/usd)</label>
                                            <input type="text" class="form-control" id="indexQty" name="indexQty" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button class="btn btn-primary" type="submit">Submit form</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?= $this->endSection() ?>
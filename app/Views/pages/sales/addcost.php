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
        <h1>Add Cost Contractor</h1>
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
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#cost">
                        <i class="bi bi-plus me-1"></i> Add Data
                    </button>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Id costmining</th>
                                <th scope="col">Year</th>
                                <th scope="col">Month</th>
                                <th scope="col">Contractor</th>
                                <th scope="col">Cost Type</th>
                                <th scope="col">Cost</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cost as $form) : ?>
                                <tr>
                                    <td><?= $form['id_costmining'] ?></td>
                                    <td><?= $form['year'] ?></td>
                                    <td><?= $bulan[$form['month']] ?></td>
                                    <td><?= $form['contractor_name'] ?></td>
                                    <td><?= $form['cost_type'] ?></td>
                                    <td><?= $form['cost'] ?></td>
                                    <td class="col">
                                        <a href="<?= site_url('/sales/costmining/edit/') . urlencode($form['id_costmining']) ?>" class="btn btn-primary btn-sm col">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a onclick="return confirm('Are you sure?')" href="<?= site_url('/sales/costmining/delete/') ?><?= $form['id_costmining'] ?>" class="btn btn-danger btn-sm col">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="modal fade" id="cost" tabindex="-1">
                        <form action="<?= site_url("/sales/costmining/add") ?>" method="POST" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Cost Contractor</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-md-12 position-relative mb-3">
                                            <label for="year" class="form-label">Year</label>
                                            <input type="text" class="form-control" id="year" name="year" required>
                                        </div>
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
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Contractor</label>
                                            <div class="col-sm-12">
                                                <select name="id_contractor" id="id_contractor" class="form-select" aria-label="Default select example">
                                                    <option selected>Please Select Contractors</option>
                                                    <?php foreach ($contractor as $contractor) : ?>
                                                        <option value="<?= $contractor['id'] ?>"><?= $contractor['contractor_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Cost Type</label>
                                            <div class="col-sm-12">
                                                <select name="id_costtype" id="id_costtype" class="form-select" aria-label="Default select example">
                                                    <option selected>Please Select Cost Type</option>
                                                    <?php foreach ($costtype as $costtype) : ?>
                                                        <option value="<?= $costtype['id_costtype'] ?>"><?= $costtype['cost_type'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 position-relative mb-3">
                                            <label for="cost" class="form-label">Cost</label>
                                            <input type="text" class="form-control" id="cost" name="cost" required>
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
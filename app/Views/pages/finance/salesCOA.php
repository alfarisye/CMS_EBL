<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Parameter COA</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance/salesandproduction") ?>">Sales and Production</a></li>
                <li class="breadcrumb-item active"><a href="#">Parameter COA</a></li>
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
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Maintain COA</div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <?php foreach ($ParamCOA as $data) : ?>
                                <!-- Custom Styled Validation with Tooltips -->
                                <div class="row mt-3 md-12">
                                    <div class="col-md-6 d-flex left-section">
                                        <label for="Cont1" class="form-label col-4 col-form-label-sm">Date Report :</label>
                                        <input name="Cont1" type="date" class="form-control" value="<?= $data['date_report'] ?>" readonly="readonly">
                                    </div>

                                    <div class="col-md-6 d-flex left-section">
                                        <label for="price" class="form-label col-4 col-form-label-sm">Source Cargo :</label>
                                        <input name="Cont1" type="form-control" class="form-control" value="<?= $data['source_cargo'] ?>" readonly="readonly">
                                    </div>
                                </div>
                                <div class="row mt-3 md-12">
                                    <div class="col-md-6 d-flex left-section">
                                        <label for="year" class="form-label col-4 col-form-label-sm">Contract Number :</label>
                                        <input name="Cont1" type="form-control" class="form-control" value="<?= $data['ContNo'] ?>" readonly="readonly">
                                    </div>

                                    <div class="col-md-6 d-flex left-section">
                                        <label for="price" class="form-label col-4 col-form-label-sm">Destination :</label>
                                        <input name="Cont1" type="form-control" class="form-control" value="<?= $data['destination'] ?>" readonly="readonly">
                                    </div>
                                </div>
                                <div class="row mt-3 md-12">
                                    <div class="col-md-6 d-flex left-section">
                                        <label for="year" class="form-label col-4 col-form-label-sm">Customer :</label>
                                        <input name="Cont1" type="form-control" class="form-control" value="<?= $data['BUYER'] ?>" readonly="readonly">
                                    </div>

                                    <div class="col-md-6 d-flex left-section">
                                        <label for="price" class="form-label col-4 col-form-label-sm">Date of Sampling :</label>
                                        <input name="Cont1" type="date" class="form-control" value="<?= $data['date_sampling'] ?>" readonly="readonly">
                                    </div>
                                </div>

                                <div class="row mt-3 md-12">
                                    <div class="col-md-6 d-flex left-section">
                                        <label for="year" class="form-label col-4 col-form-label-sm">Vessel :</label>
                                        <input name="Cont1" type="form-control" class="form-control" value="<?= $data['vessel'] ?>" readonly="readonly">
                                    </div>

                                    <div class="col-md-6 d-flex left-section">
                                        <label for="price" class="form-label col-4 col-form-label-sm">Loading Port :</label>
                                        <input name="Cont1" type="form-control" class="form-control" value="<?= $data['loading_port'] ?>" readonly="readonly">
                                    </div>
                                </div>

                                <div class="row mt-3 md-12">
                                    <div class="col-md-6 d-flex left-section">
                                        <label for="year" class="form-label col-4 col-form-label-sm">Quantity :</label>
                                        <input name="Cont1" type="form-control" class="form-control" value="<?= $data['quantity'] ?>" readonly="readonly">
                                    </div>

                                    <div class="col-md-6 d-flex left-section">
                                        <label for="price" class="form-label col-4 col-form-label-sm">Standard :</label>
                                        <input name="Cont1" type="form-control" class="form-control" value="<?= $data['standard'] ?>" readonly="readonly">
                                    </div>
                                </div>
                                <br>
                            <?php endforeach ?>
                            <br>
                            <div>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Activity</th>
                                        <th>Contract</th>
                                        <th>Preliminary</th>
                                        <th>Final</th>
                                        <th>Selisih</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php foreach ($activityCOA as $row) : ?>
                                                <p><?= $row["coa"] ?></p>
                                            <?php endforeach ?>
                                        </td>
                                        <?php foreach ($ParamCOA as $row) : ?>
                                            <td>
                                                <?php
                                                for ($i = 1; $i < 11; $i++) { ?>
                                                    <p><?= $row["Contract$i"] ?? '' ?></p>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php
                                                for ($i = 1; $i < 11; $i++) { ?>
                                                    <p><?= $row["preliminary$i"] ?? '' ?></p>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php
                                                for ($i = 1; $i < 11; $i++) { ?>
                                                    <p><?= $row["final$i"] ?? '' ?></p>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php
                                                for ($i = 1; $i < 11; $i++) { ?>
                                                    <p><?= $row["selisih$i"] ?? '' ?></p>
                                                <?php } ?>
                                            </td>
                                    </tr>
                                <?php endforeach ?>
                                </table>
                            </div>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<script>
    $(document).ready(function() {
        // stop enter key
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
</script>

<?= $this->endSection() ?>
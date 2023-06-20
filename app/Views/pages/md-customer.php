<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Group Email</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Master Data</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("master-data/customer") ?>">Customer</a></li>
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
                        <h5 class="card-title">Customer Data</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <div class="table-responsive">
                            <!-- Table users -->
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">Company Code</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Customer Number</th>
                                        <th scope="col">Country Key</th>
                                        <th scope="col">Customer Name 1</th>
                                        <th scope="col">Customer Name 2</th>
                                        <th scope="col">City</th>
                                        <th scope="col">Postal Code</th>
                                        <th scope="col">Region (State, Province, County)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer) : ?>
                                        <tr>
                                            <td><?= $customer['BUKRS'] ?></td>
                                            <td><?= $customer['ANRED'] ?></td>
                                            <td><?= $customer['KUNNR'] ?></td>
                                            <td><?= $customer['LAND1'] ?></td>
                                            <td><?= $customer['NAME1'] ?></td>
                                            <td><?= $customer['NAME2'] ?></td>
                                            <td><?= $customer['ORT01'] ?></td>
                                            <td><?= $customer['PSTLZ'] ?></td>
                                            <td><?= $customer['REGIO'] ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<script>
</script>

<?= $this->endSection() ?>
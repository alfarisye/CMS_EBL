<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>WBS List</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Master Data</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("/master-data/wbs_element/") ?>">WBS List</a></li>
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
                        <h5 class="card-title">WBS List</h5>
                        <div class="d-flex justify-content-between mb-4">
                            
                            <div class="d-flex left-section col-7">
                                <span class="mx-2 my-auto">WBS From</span>
                                <input type="text" name="beginwbs" id="beginwbs" placeholder="-- input WBS from --" class="form-control" value="<?= $_GET['begwbs'] ?? false ?>"/>
                                <span class="mx-2 my-auto">to</span>
                                <input type="text" name="endwbs" id="endwbs" placeholder="-- end input WBS --" class="form-control" value="<?= $_GET['endwbs'] ?? false ?>"/>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">
                                    Filter
                                </button>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getDataReset()">
                                    Reset
                                </button>
                                <script>
                                    function getData(){
                                        let datfrom= document.getElementById('beginwbs').value
                                        let datto= document.getElementById('endwbs').value
                                        window.location.href="<?= site_url() ?>"+`/master-data/wbs_element?begwbs=${datfrom}&endwbs=${datto}`;
                                    }
                                    function getDataReset(){
                                        window.location.href="<?= site_url() ?>"+`/master-data/wbs_element/`;
                                    }
                                </script>
                            </div> 
                        </div>
                        <div class="table-responsive">
                            <!-- Table users -->
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">WBS Element</th>
                                        <th scope="col">WBS Description</th>
                                        <th scope="col">Level</th>
                                        <th scope="col">Company Code</th>
                                        <th scope="col">Controlling Area</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $n = 0; foreach ($tdata as $row) : $n++; ?>
                                    <tr>
                                        <td><?= $n ?>.</td>
                                        <td><?= $row['POSID'] ?></td>
                                        <td><?= $row['POST1'] ?></td>
                                        <td><?= $row['STUFE'] ?></td>
                                        <td><?= $row['PBUKR'] ?></td>
                                        <td><?= $row['PKOKR'] ?></td>
                                        <td><?= $row['STATUS'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
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



<?= $this->endSection() ?>
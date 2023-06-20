<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Procurement - PR Tracking</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Procurement</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("pr-tracking") ?>">PR Tracking</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5>&nbsp;</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->
                        
                        <div class="d-flex justify-content-between mb-4">
                            
                            <div class="d-flex left-section col-7">
                                <span class="mx-2 my-auto col-1">Plant</span>
                                <select class="form-select" name="plant_cd" id="plant_cd">
                                    <option <?php if(($_GET['plnt']?? false) == ''){echo("selected");}?> value="">-- Pilih Plant --</option>
                                    <!-- onchange="filterCard() -->
                                    <?php foreach ($Plant as $row) : ?>
                                        <option <?php if(($_GET['plnt']?? false) == $row['WERKS']){echo("selected");}?> value="<?= $row['WERKS'] ?>"><?= $row['NAME1'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <!-- <input class="form-control" type="plant_cd" id="plant_cd" name="plant_cd" placeholder="Plant Code" value="" maxlength="4" style="margin-right: 10px;"> -->
                                <span class="mx-2 my-auto col-3">Purchase Document</span>
                                <input class="form-control" type="text" id="pr_number" name="pr_number" placeholder="PR No." value="<?= $_GET['pr_no']?? false ?>" maxlength="10" style="margin-right: 10px;">
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">
                                    Filter
                                </button>
                                <script>
                                    function getData(){
                                        let pr_number= document.getElementById('pr_number').value
                                        let plant_cd= document.getElementById('plant_cd').value
                                        window.location.href="<?= site_url() ?>"+`/pr-tracking?plnt=${plant_cd}&pr_no=${pr_number}`;
                                    }
                                </script>
                            </div>  
                            <div class="right-section">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-search me-1"></i> Search More
                                </button>
                            </div>
                        </div>

                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <!-- <form action="<?= site_url("pr-tracking") ?>" method="POST" class="needs-validation" novalidate> -->
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Search Purchase Requesition Number</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- <div class="col-md-12 mt-3">
                                                <label for="username" class="form-label">Date</label>
                                                <input class="form-control" type="date" id="tgl_awal" name="tgl_awal" placeholder="tgl_awal" value="<?= date('Y-m-d');?>" >
                                                <span class="mx-2 my-auto">to</span>
                                                <input class="form-control" type="date" id="tgl_akhir" name="tgl_akhir"  placeholder="tgl_akhir" value="<?= date('Y-m-d');?>">    
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Plant</label>
                                                <div class="col-sm-12">
                                                    <select name="Type" id="type" class="form-select" aria-label="Default select example" required>
                                                        <option>Select Plant</option>
                                                        
                                                            <option value="tes">tes</option>
                                                        
                                                    </select>
                                                </div>
                                            </div> -->
                                            <div class="table-responsive">
                                                <!-- Table users -->
                                                <table class="table table-bordered datatable">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">PR Date</th>
                                                            <th scope="col">PR Number</th>
                                                            <th scope="col">Plant</th>
                                                            <th scope="col">Select</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($cek_pr as $row) : ?>
                                                            <tr>                                       
                                                                <td><?= $row['BADAT'] ?></td>
                                                                <td><?= $row['BANFN'] ?></td>
                                                                <td><?= $row['PLANT'] ?></td>
                                                                <td>
                                                                    <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter">
                                                                    <a href="<?= site_url("pr-tracking?plnt={$row['WERKS']}&pr_no={$row['BANFN']}") ?>">
                                                                        <i class="bi bi-search me-1" style="background-color: #FFFFFF;"></i>
                                                                        </a>
                                                                    </button>
                                                                </td>
                                                             </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <!-- End Table with stripped rows -->
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            
                                        </div>
                                    </div>
                                </div>
                            <!-- </form> -->
                        </div>
                        
                        <div class="table-responsive" style="background-color: #D4E6F1;">
                            <h5 style="background-color: #FFFFFF;">PR Status</h5>
                            <!-- Table users -->
                            <table class="table table-bordered datatable" style="background-color: #FFFFFF;">
                                <thead style="background-color: #F2F4F4;">
                                    <tr>
                                        <th scope="col">Item</th>
                                        <th scope="col">PR Date</th>
                                        <th scope="col">Material</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">UoM</th>
                                        <th scope="col">Request By</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($v_pr as $datapr) : ?>
                                        <tr>                                       
                                            <td><?= $datapr['ITEM'] ?></td>
                                            <td><?= $datapr['BADAT'] ?></td>
                                            <td><?= $datapr['MATNR'] ?></td>
                                            <td><?= $datapr['TXZ01'] ?></td>
                                            <td><?= $datapr['MENGE'] ?></td>
                                            <td><?= $datapr['MEINS'] ?></td>
                                            <td><?= $datapr['AFNAM'] ?></td>
                                            <?php if ($datapr['FULL_RELEASE'] == 'X') : ?>
                                                <td style="background-color: #D5F5E3;"><?= $datapr['FULL_REL'] ?></td>
                                            <?php else : ?>
                                                <td style="background-color: #F5B7B1;"><?= $datapr['FULL_REL'] ?></td>
                                            <?php endif ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>

                        <div class="table-responsive" style="background-color: #D4E6F1;">
                            <h5 style="background-color: #FFFFFF;">PO Status</h5>
                            <!-- Table users -->
                            <table class="table table-bordered datatable" style="background-color: #FFFFFF;">
                                <thead style="background-color: #F2F4F4;">
                                    <tr>
                                        <th scope="col">Item</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">PO Date</th>
                                        <th scope="col">PO Number</th>
                                        <th scope="col">Vendor</th>
                                        <th scope="col">City</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">UoM</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">ETA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($v_po as $datapo) : ?>
                                        <tr>                                       
                                            <td><?= $datapo['ITEM'] ?></td>
                                            <td><?= $datapo['TXZ01'] ?></td>
                                            <td><?= $datapo['PODATE'] ?></td>
                                            <td><?= $datapo['EBELN'] ?></td>
                                            <td><?= $datapo['VENDOR'] ?></td>
                                            <td><?= $datapo['CITY'] ?></td>
                                            <td><?= $datapo['MENGE'] ?></td>
                                            <td><?= $datapo['MEINS'] ?></td>
                                            <?php if ($datapo['FULL_RELEASE'] == 'X') : ?>
                                                <td style="background-color: #D5F5E3;"><?= $datapo['FULL_REL'] ?></td>
                                            <?php else : ?>
                                                <td style="background-color: #F5B7B1;"><?= $datapo['FULL_REL'] ?></td>
                                            <?php endif ?>                                            
                                            <td><?= $datapo['ETA'] ?></td>                                            
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>

                        <div class="table-responsive" style="background-color: #D4E6F1;">
                            <h5 style="background-color: #FFFFFF;">GR Status</h5>
                            <!-- Table users -->
                            <table class="table table-bordered datatable" style="background-color: #FFFFFF;">
                                <thead style="background-color: #F2F4F4;">
                                    <tr>
                                        <th scope="col">Item</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">PO Number</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">UoM</th>
                                        <th scope="col">GR Number</th>
                                        <th scope="col">Receipt Date</th>
                                        <th scope="col">Receipt By</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">LT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($v_gr as $datagr) {?>
                                        <tr>                                       
                                            <td><?= $datagr['ITEM'] ?></td>
                                            <td><?= $datagr['TXZ01'] ?></td>
                                            <td><?= $datagr['EBELN'] ?></td>
                                            <td><?= $datagr['MENGE'] ?></td>
                                            <td><?= $datagr['MEINS'] ?></td>
                                            <td><?= $datagr['MBLNR'] ?></td>
                                            <td style="background-color: #FBEED5;"><?= $datagr['BUDAT'] ?></td>
                                            <td style="background-color: #C3FAF1;"><?= $datagr['WEMPF'] ?></td>
                                            <?php if ($datagr['STATUS'] == 'Full Supply') : ?>
                                                <td style="background-color: #D5F5E3;"><?= $datagr['STATUS'] ?></td>
                                            <?php else : ?>
                                                <td style="background-color: #F5B7B1;"><?= $datagr['STATUS'] ?></td>
                                            <?php endif ?>                                         
                                            <td><?= $datagr['LT_POGR'] ?></td>                                            
                                        </tr>
                                    <?php } ?>
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
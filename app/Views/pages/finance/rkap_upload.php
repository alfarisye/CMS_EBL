<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?> 
<main id="main" class="main">
 
    <div class="pagetitle">
        <h1>Update Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance/updatedata") ?>">Update Data</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/rkap") ?>">Input RKAP</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/rkap/upload") ?>">Upload</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Upload RKAP</h5>
                        <form class="d-flex left-section justify-content-between mb-7" action="<?= site_url("finance/rkap/get_upl") ?>" method="POST" enctype="multipart/form-data">
                            <div class="d-flex left-section col-5">
                                <input type="file" class="form-control" id="userfile" name="userfile">
                                <button type="submit" class="ms-2 my-2 btn btn-primary">
                                    Upload
                                </button>
                            </div>  
                        </form>
                        <br>
                        <?php if ($ready_save == true) {?>
                            <!-- <form action="<?= site_url("/finance/rkap/upl/") ?>" method="POST" enctype="multipart/form-data">
                                <div class="d-flex left-section col-5">
                                    <input  type="file" id="datadata" name="datadata" value="<?php $upl ?>" hidden>
                                    <button type="submit" class="ms-2 my-2 btn btn-primary">
                                        <i class="bi bi-plus me-1"></i> Save Data
                                    </button>
                                </div> 
                            </form> -->
                            <a class="btn btn-primary mb-2" onclick="saveData()">
                                <i class="bi bi-plus me-1"></i> Save Data
                            </a>
                               
                            <br>
                        <?php } ?>
                        <!-- Table users -->
                        <table class="table datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Year</th>
                                    <th scope="col">Month</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Shipment</th>
                                    <th scope="col">Price (Rp/MT)</th>
                                    <th scope="col">Quantity (MT)</th>
                                    <th scope="col">Cost (Rp/MT)</th>
                                    <th scope="col">Status Upload</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                $i = 0;
                                foreach ($upl as $row) : 
                                    if($i > 0){?>
                                    <tr>
                                        <td><?= $row['0'] ?></td>
                                        <td><?= $row['1'] ?></td>
                                        <td><?= $row['2'] ?></td>
                                        <td><?= $row['3'] ?></td>
                                        <td><?= $row['4'] ?></td>
                                        <td><?= $row['5'] ?></td>
                                        <td><?= $row['6'] ?></td>
                                        <td>
                                            <?php if (isset($row['7'])) {?>
                                                <?= $row['7'] ?>
                                            <?php }else{ ?>
                                                ready
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    
                                <?php } $i++; endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function saveData(){
            var data=<?php echo json_encode($upl); ?>;
            data=JSON.stringify(data);
            // fetch("<?= site_url("/finance/rkap/upl")?>"+`?data=${data}`).then(res=>
            //     res.text()).then(res2=>{
            //         console.log(res2)
            //     })
            // window.location.replace("<?= site_url("finance/rkap") ?>");
            window.location.replace("<?= site_url("/finance/rkap/upl")?>"+`?data=${data}`);
        }
        
    </script>

                <!-- </div>
            </div>
        </div>
    </section> -->

</main>

<?= $this->endSection() ?>
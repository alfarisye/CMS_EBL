<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">
 
    <div class="pagetitle">
        <h1>Update Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance/updatedata") ?>">Update Data</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance/budgetfinance") ?>">Input Budget Finance</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/budgetfinance/upload") ?>">Upload</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Input Budget Finance</h5>
                        <form class="d-flex left-section justify-content-between mb-7" action="<?= site_url("finance/budgetfinance/get_upl") ?>" method="POST" enctype="multipart/form-data">
                            <div class="d-flex left-section col-5">
                                <input type="file" class="form-control" id="userfile" name="userfile">
                                <button type="submit" class="ms-2 my-2 btn btn-primary">
                                    Upload
                                </button>
                            </div>  
                        </form>
                        <br>
                        <?php if ($ready_save == true) {?>
                            <!-- <?= site_url("/finance/budgetfinance/upl/") ?> -->
                            <a class="btn btn-primary mb-2" onclick="saveData()">
                                <i class="bi bi-save me-1"></i> Save Data
                            </a>
                               
                            <br>
                        <?php } ?>
                        <!-- Table users -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">GL Account</th>
                                    <th scope="col">GL Acc. Desc</th>
                                    <th scope="col">WBS Element</th>
                                    <th scope="col">WBS Elm. Desc</th>
                                    <th scope="col">Year</th>
                                    <th scope="col">Month</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Upload Status</th>
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
                                        <td><?= $row['7'] ?></td>
                                        <td><?= $row['8'] ?></td>
                                    </tr>
                                <?php } $i++; endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
    function saveData(){
        <?php
            $i = 0;
            foreach ($upl as $row) : 
                $upl[$i][0]='';
                $upl[$i][2]=''; 
                $upl[$i][4]=''; 
                $upl[$i][8]=''; 
            $i++;
            endforeach 
        ?>
        var data=<?php echo json_encode($upl); ?>;
        data=JSON.stringify(data);
        // fetch("<?= site_url("/finance/budgetfinance/upl")?>"+`?data=${data}`).then(res=>
        //     res.text()).then(res2=>{
        //         console.log(res2)
        //     })
        // window.location.replace("<?= site_url("finance/budgetfinance") ?>");
        window.location.replace("<?= site_url("/finance/budgetfinance/upl")?>"+`?data=${data}`);
    }
</script>

<?= $this->endSection() ?>
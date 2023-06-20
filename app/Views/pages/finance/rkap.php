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
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Input RKAP</h5>
                    

                        <!-- // ! Add contractor modal -->

                        <!-- <div class="row">
                            <div class="col-md-4">
                                <i class="ri-filter-2-line"></i>
                                <select class="form-control text-xs">
                                    <option> <i class="ri-filter-2-line"></i> By Month</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div class="col-md-4 offset-md-4"><br>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Add new data
                                </button>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="ri-upload-line"></i>
                                </button>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="ri-download-line"></i>
                                </button>
                            </div>
                        </div> -->
                        <div class="d-flex justify-content-between mb-4">
                            
                            <div class="d-flex left-section col-5">
                                <span class="mx-2 my-auto">Month</span>
                                <select class="form-control text-xs" name="idmonth" id="idmonth">
                                    <option <?php if(($_GET['idmth'] ?? false) == ''){echo("selected");}?> value=""> -- All Month --</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '01'){echo("selected");}?> value="01">(1) Januari</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '02'){echo("selected");}?> value="02">(2) Februari</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '03'){echo("selected");}?> value="03">(3) Maret</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '04'){echo("selected");}?> value="04">(4) April</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '05'){echo("selected");}?> value="05">(5) Mei</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '06'){echo("selected");}?> value="06">(6) Juni</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '07'){echo("selected");}?> value="07">(7) Juli</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '08'){echo("selected");}?> value="08">(8) Agustus</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '09'){echo("selected");}?> value="09">(9) September</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '10'){echo("selected");}?> value="10">(10) Oktober</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '11'){echo("selected");}?> value="11">(11) November</option>
                                    <option <?php if(($_GET['idmth'] ?? false) == '12'){echo("selected");}?> value="12">(12) Desember</option>                                    
                                </select>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">
                                    Filter
                                </button>
                                <script>
                                    function getData(){
                                        let id_cat= document.getElementById('idmonth').value
                                        window.location.href="<?= site_url() ?>"+`/finance/rkap?idmth=${id_cat}`;
                                    }
                                </script>
                            </div>  
                            <div class="right-section">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Tambah Data
                                </button>
                                <!-- <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" href="<?= site_url("/finance/rkap/upload/") ?>">
                                    <i class="bi bi-plus me-1"></i> Upload Data -->
                                    <a href="<?= site_url("/finance/rkap/upload/") ?>" class="btn btn-primary mb-2">
                                        <span class="bi bi-plus me-1">Upload Data</span>
                                    </a>
                                <!-- </button> -->
                            </div>
                        </div>
                        <br>

                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("finance/rkap/add") ?>" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"> Input RKAP</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="d-flex left-section mb-3">
                                                <label for="year" class="form-label col-2">Year</label>
                                                <input type="number" class="form-control" id="year" name="year" required>
                                                
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="month" class="form-label col-2">Month</label>
                                                <select class="form-control" name="month" id="month" required>
                                                <option value=""> -- pilih bulan --</option>
                                                <option value="01">(1) Januari</option>
                                                <option value="02">(2) Februari</option>
                                                <option value="03">(3) Maret</option>
                                                <option value="04">(4) April</option>
                                                <option value="05">(5) Mei</option>
                                                <option value="06">(6) Juni</option>
                                                <option value="07">(7) Juli</option>
                                                <option value="08">(8) Agustus</option>
                                                <option value="09">(9) September</option>
                                                <option value="10">(10) Oktober</option>
                                                <option value="11">(11) November</option>
                                                <option value="12">(12) Desember</option>       
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="ctype" class="form-label col-2">Chart Type</label>
                                                <select class="form-control" name="ctype" id="ctype" required>
                                                    <option value="">-- pilih Char Type --</option>
                                                    <?php foreach ($ctype as $key => $row) {
                                                        echo '<option value="'.$row['ctype'].'">'.$row['ctype'].'</option>';
                                                    }?>
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="shipment" class="form-label col-2">Shipment</label>
                                                <select class="form-control" name="shipment" id="shipment" required>
                                                    <option value="">-- pilih shipment --</option>
                                                    <!-- <option value="FOBB">FOBB</option>
                                                    <option value="MV">MV</option>
                                                    <option value="CIF">CIF</option> -->
                                                </select>
                                                <script>
                                                    $('#ctype').on('change', function() {
                                                        var val_sel = $(this).val();
                                                        $('#shipment').empty();
                                                        <?php foreach ($tshipment as $key => $row) { ?>
                                                            var ctype = "<?php echo $row['type'] ?>";
                                                            if ( ctype == val_sel ) {
                                                               $('#shipment').append("<option value='<?php echo $row['shipment'] ?>'> <?php echo $row['shipment'] ?></option>");
                                                            }
                                                        <?php  } ?>                                                        
                                                    });
                                                </script>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="price" class="form-label col-2">Price(RP/MT)</label>
                                                <input class="form-control" id="price" name="price">

                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="quantity" class="form-label col-2">Quantity(MT)</label>
                                                <input class="form-control" id="quantity" name="quantity">
                                                
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="cost" class="form-label col-2">Cost(RP/MT)</label>
                                                <input class="form-control" id="cost" name="cost">

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary" type="submit">Save Data</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- // ! edit contractor modal -->
                        <div class="modal fade" id="editForm" tabindex="-1">
                        <form action="<?= site_url("finance/rkap/update") ?>" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit RKAP</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <input type="hidden" id="id" name="id">
                                            <input type="hidden" id="idrev" name="idrev">
                                            <div class="d-flex left-section mb-3">
                                                <label for="edbgid" class="form-label col-2">Budget id</label>
                                                <input type="text" class="form-control" id="edbgid" name="edbgid" readonly>
                                                
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edyear" class="form-label col-2">Year</label>
                                                <input type="number" class="form-control" id="edyear" name="edyear" required>
                                                
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edmonth" class="form-label col-2">Month</label>
                                                <select class="form-control" name="edmonth" id="edmonth" required>
                                                <option value=""> -- pilih bulan --</option>
                                                <option value="01">(1) Januari</option>
                                                <option value="02">(2) Februari</option>
                                                <option value="03">(3) Maret</option>
                                                <option value="04">(4) April</option>
                                                <option value="05">(5) Mei</option>
                                                <option value="06">(6) Juni</option>
                                                <option value="07">(7) Juli</option>
                                                <option value="08">(8) Agustus</option>
                                                <option value="09">(9) September</option>
                                                <option value="10">(10) Oktober</option>
                                                <option value="11">(11) November</option>
                                                <option value="12">(12) Desember</option>       
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edctype" class="form-label col-2">Chart Type</label>
                                                <select class="form-control" name="edctype" id="edctype" required>
                                                    <option value="">-- pilih Char Type --</option>
                                                    <?php foreach ($ctype as $key => $row) {
                                                        echo '<option value="'.$row['ctype'].'">'.$row['ctype'].'</option>';
                                                    }?>
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edshipment" class="form-label col-2">Shipment</label>
                                                <select class="form-control" name="edshipment" id="edshipment" required>
                                                    <option value="">-- pilih shipment --</option>
                                                    <!-- <option value="FOBB">FOBB</option>
                                                    <option value="MV">MV</option>
                                                    <option value="CIF">CIF</option> -->
                                                    
                                                </select>
                                                <script>
                                                    $('#edctype').on('change', function() {
                                                        var val_sel = $(this).val();
                                                        console.log(val_sel);
                                                        $('#edshipment').empty();
                                                        <?php foreach ($tshipment as $key => $row) { ?>
                                                            var ctype = "<?php echo $row['type'] ?>";
                                                            if ( ctype == val_sel ) {
                                                               $('#edshipment').append("<option value='<?php echo $row['shipment'] ?>'> <?php echo $row['shipment'] ?></option>");
                                                            }
                                                        <?php  } ?>                                                        
                                                    });
                                                </script>
                                            </div>
                                            <!-- <div class="d-flex left-section mb-3">
                                                <label for="edshipment" class="form-label col-2">Shipment</label>
                                                <select class="form-control" name="edshipment" id="edshipment" required>
                                                    <option value="">-- pilih shipment --</option>
                                                    <option value="FOBB">FOBB</option>
                                                    <option value="MV">MV</option>
                                                    <option value="CIF">CIF</option>
                                                </select>
                                            </div> -->
                                            <div class="d-flex left-section mb-3">
                                                <label for="edprice" class="form-label col-2">Price(RP/MT)</label>
                                                <input class="form-control" id="edprice" name="edprice">

                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edquantity" class="form-label col-2">Quantity(MT)</label>
                                                <input class="form-control" id="edquantity" name="edquantity">
                                                
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edcost" class="form-label col-2">Cost(RP/MT)</label>
                                                <input class="form-control" id="edcost" name="edcost">

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary" type="submit">Save Data</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Table users -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Budget ID</th>
                                    <th scope="col">Year</th>
                                    <th scope="col">Month</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Shipment</th>
                                    <th scope="col">Price (Rp/MT)</th>
                                    <th scope="col">Quantity (MT)</th>
                                    <th scope="col">Cost (Rp/MT)</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Created by</th>
                                    <th scope="col">Create at</th>
                                    <th scope="col">Edited by</th>
                                    <th scope="col">Edited at</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                foreach ($rkap as $row) : ?>
                                    <tr>
                                        <td><?= $row['BDGID'] ?></td>
                                        <td><?= $row['GJAHR'] ?></td>
                                        <td><?= $row['MONAT'] ?></td>
                                        <td><?= $row['TYPE'] ?></td>
                                        <td><?= $row['SHPMN'] ?></td>
                                        <td><?= $row['PRC'] ?></td>
                                        <td><?= $row['QTY'] ?></td>
                                        <td><?= $row['COST'] ?></td>
                                        <td><?= $row['STATS'] ?></td>
                                        <td><?= $row['CRTDB'] ?></td>
                                        <td><?= $row['CRTDA'] ?></td>
                                        <td><?= $row['EDTBY'] ?></td>
                                        <td><?= $row['EDTAT'] ?></td>
                                        <td class="col">
                                            <a onclick="editForm(<?= $row['id'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('are you sure')" href="<?= site_url("/finance/rkap/delete/").$row['id'] ?>" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                                <!-- <span class="tipstextL">Delete</span> -->
                                            </a>
                                        </td> 
                                    </tr>
                                <?php endforeach ?>
                                <!-- <tr>
                                    <td>Bf-2022-222</td>
                                    <td>2022</td>
                                    <td>05</td>
                                    <td>FOBB</td>
                                    <td>13.123.213</td>
                                    <td>15000</td>
                                    <td>RO</td>
                                    <td>Shania</td>
                                    <td>18/05/1999</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered"> 
                                            <i class="ri-edit-2-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger">           
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>                           
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

                <!-- </div>
            </div>
        </div>
    </section> -->

</main>

<script>
    // RKMRK, GJAHR, DMBTR, COMMT
    function editForm(id) {
        fetch("<?= site_url('/finance/rkap/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);

                $('#edshipment').empty();
                <?php foreach ($tshipment as $key => $row) { ?>
                    var ctype = "<?php echo $row['type'] ?>";
                    if ( ctype == res.TYPE ) {
                        $('#edshipment').append("<option value='<?php echo $row['shipment'] ?>'> <?php echo $row['shipment'] ?></option>");
                    }
                <?php  } ?> 

                $('#edbgid').val(res.BDGID);
                $('#edyear').val(res.GJAHR);
                $('#edmonth').val(res.MONAT);
                $('#edctype').val(res.TYPE);
                $('#edshipment').val(res.SHPMN);
                $('#edprice').val(res.PRC);
                $('#edquantity').val(res.QTY);
                $('#edcost').val(res.COST);
                $('#idrev').val(res.id_rev);
                $('#id').val(id);

                
            })
            .then(() => {
                $('#editForm').modal('show');
            });
    }
</script>

<?= $this->endSection() ?>
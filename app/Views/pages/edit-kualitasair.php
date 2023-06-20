<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

<div class="pagetitle">
        <h1>K3LH - Kualitas Air - Edit Kualitas Air</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("/kualitasair") ?>">New Kualitas Air</a></li>
                <li class="breadcrumb-item active"><a href="#">Edit Kualitas Air</a></li>
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
                        <h5 class="card-title">Kualitas Air edit</h5>
                        <!-- notification -->
                <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
                        
            <form  action="<?= site_url("/kualitasair/update") ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                        <div class="col">
                        
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Location</label>
                                <div class="col-sm-4">  
                                    <input name="location" id="location" class="form-control" aria-label="Default select example" value="<?= $kuali_air['location']; ?>" required></input>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Tanggal</label>
                                <div class="col-sm-4">  
                                <input type="date" class="form-control" id="prd_date_text" name="date" value="<?= $kuali_air['date'] ?>" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Debit Air</label>
                                <div class="col-sm-4">  
                                    <input name="debit_air" id="debit_air" class="form-control" aria-label="Default select example" value="<?= $kuali_air['debit_air']; ?>" required></input>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <?php foreach ($air_para as $form) : ?>
                                                    <div class="col-md-12 mt-3">
                                                    <div class="sm-form">
                                                        <label class="form-label" for="<?= $form['nama_parameter'] ?>"><?= $form['nama_parameter'] ?></label>
                                                            <div class="col-sm-12">
                                                                <input type="number" id="<?= $form['id_Parameter'] ?>" value="<?= $form['id_Parameter'] ?>" name="idParameter[]" class="form-control" aria-label="Default select example" hidden></input>
                                                            <?php foreach ($para_kuali as $para) : ?>
                                                                <?php if($para['id_Parameter']==$form['id_Parameter']) { ?>
                                                                    <input name="valueParameter[]" id="value" class="form-control" aria-label="Default select example" value="<?= $para['value'] ?>"></input>
                                                                <?php } ?>
                                                            <?php endforeach;?>
                                                            </div> 
                                                    </div>                                                         
                                                    </div>
                                <?php endforeach;?>
                                </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    
                                    <input name="id_KualitasAir" type="text" class="form-control" value="<?= $kuali_air['id_KualitasAir'] ?>" hidden>
                                    
                                </div>
                            </div>
                            <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>
                                <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                                <script>
                                    function getData(){
                                        window.location.href="<?= site_url() ?>"+`/kualitasair`;
                                    }   
                                </script>                                                                                             
                        </div>
                </form>                    

</main><!-- End #main -->

<script>
    $(document).ready(function(){
        $('#type').change(function(){
            var id_type = $(this).val();

            var action = 'get_category';

            if(id_type != '')
            {
                $.ajax({
                    url:"<?php echo site_url('k3lh/action/'); ?>" + id_type,
                    method:"GET",                    
                    success:function(data)
                    {
                        console.log(data);
                        const obj = JSON.parse(data);
                        $('#Category').empty();
                        $.each(obj, function(key, value){
                            $('#Category').append('<option value="'+ value.Id_category  +'">'+ value.category +'</option>');
                        });

                    }
                });
            }
            else
            {
                $('#category').val('');
            }            
        });
    });
</script>
  
<?= $this->endSection() ?>
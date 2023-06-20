<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

<div class="pagetitle">
        <h1>Master Data - Edit - Form</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("/tambah-bml") ?>">New Form</a></li>
                <li class="breadcrumb-item active"><a href="#">Edit Form</a></li>
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
                        <h5 class="card-title">Form edit</h5>
                        <!-- notification -->
                <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
                        
            <form  action="<?= site_url("/tambah-manform/update") ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                        <div class="col">
                        
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Nama Form</label>
                                <div class="col-sm-4">  
                                    <input name="nama_form" id="nama_form" class="form-control" aria-label="Default select example" value="<?= $man_form['nama_form']; ?>" required></input>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Status</label>
                                <div class="col-sm-4">  
                                    <input name="status" id="status" class="form-control" aria-label="Default select example" value="<?= $man_form['status']; ?>" required></input>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    
                                    <input name="id_form" type="text" class="form-control" value="<?= $man_form['id_form'] ?>" hidden>
                                    
                                </div>
                            </div>
                            <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>
                                <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                                <script>
                                    function getData(){
                                        window.location.href="<?= site_url() ?>"+`/tambah-manform`;
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
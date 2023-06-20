<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

<div class="pagetitle">
        <h1>Master Data - Edit - Allocation CSR</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("/new-csr-allocation") ?>">New Allocation</a></li>
                <li class="breadcrumb-item active"><a href="#">Edit Allocation</a></li>
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
                        <h5 class="card-title">SHE Report edit</h5>
                        <!-- notification -->
                <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
                        
            <form  action="<?= site_url("/new-csr-allocation/update") ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                        <div class="col">
                        
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Add New Allocation</label>
                                <div class="col-sm-4">  
                                    <input name="allocation" id="allocation" class="form-control" aria-label="Default select example" value="<?= $new_allo['allocation']; ?>" required></input>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Upload File</label>     
                                <div class="col-sm-4"> 
                                        <div class="input-group">
                                        <input  type="file" id="userfile" name="userfile" class="form-control p-1 text-xs rounded-sm shadow-sm" placeholder="userfile" ></input>                                              
                                        <span class="input-group-text">*MAX 5MB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    
                                    <input name="id_allo" type="text" class="form-control" value="<?= $new_allo['id_allo'] ?>" hidden>
                                    
                                </div>
                            </div>
                            <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>
                                <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                                <script>
                                    function getData(){
                                        window.location.href="<?= site_url() ?>"+`/new-csr-allocation`;
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
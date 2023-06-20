<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

<div class="pagetitle">
    <h1>K3LH - K3LH Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
            <li class="breadcrumb-item">K3LH</li>
            <li class="breadcrumb-item active"><a href="<?= site_url("k3lh/") ?>">K3LH Report</a></li>
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
                        
            <form id="editaddnew" action="<?= site_url("/addnew/update") ?>" method="POST">
                            <?= csrf_field() ?>
                        <div class="col">
                        <?php foreach ($category as $ctg) { ?>
                                <div class="row mb-3">

                            <label for="Type" class="col-sm-2 col-form-label">Type Accident</label>
                            <div class="col-sm-4">
                            <select name="Type" id="type" class="form-select" aria-label="Default select example">
                                <option value="<?= $ctg['id_type']; ?>" selected hidden><?= $ctg['type_text']; ?></option>
                                
                                <?php foreach ($type as $typ) : ?>
                                    <option value="<?= $typ['id_type']; ?>" ><?= $typ['type']; ?></option>
                                <?php endforeach; ?>    
                            </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                                    <label for="contractors" class="col-sm-2 col-form-label">Category</label>
                            <div class="col-sm-4">
                                <input name="ty_category" id="Category" class="form-control" aria-label="Default select example" value="<?= $ctg['category']; ?>" required>
                                <input name="Id_category" id="Idcategory" class="form-control" aria-label="Default select example" value="<?= $ctg['Id_category']; ?>" hidden>
                                </input>                        
                            </div>
                        </div>    
                        <?php }?>

                            
                       
                        <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>
                                <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                                <script>
                                    function getData(){
                                        window.location.href="<?= site_url() ?>"+`/addnew`;
                                    }   
                                </script>                                                                                             
                        </div>
                </form>
                 
                </div>
                </div> 

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
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
                <li class="breadcrumb-item active"><a href="<?= site_url("#") ?>">Edit Report</a></li>
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
                        <h5 class="card-title">K3LH Report edit</h5>
                        <!-- notification -->
                <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
            <form id="k3lhedit" action="<?= site_url("/k3lh/update") ?>" method="POST">
                            <?= csrf_field() ?>
                        <div class="col">  
                        <div class="row mb-3">                         
                        <label for="date" class="col-sm-2 col-form-label">Date</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="prd_date_text" name="date" value="<?= $K3lhReport['date'] ?>" required>
                                
                            </div>                           
                        </div>
                        <div class="row mb-3">
                            <label for="Type" class="col-sm-2 col-form-label">Type Accident</label>
                            <div class="col-sm-4">
                            <select name="Type" id="type" class="form-select" aria-label="Default select example">
                                <?php foreach ($type as $typ) : ?>
                                    <option value="<?= $typ['id_type'] ?>" <?= $typ['id_type'] == $K3lhReport['Type'] ? 'selected' : null ?> ><?= $typ['type'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                        </div>    
                        <div class="row mb-3">
                                    <label for="contractors" class="col-sm-2 col-form-label">Category</label>
                            <div class="col-sm-4">
                            <select name="ty_category" id="Category" class="form-select" aria-label="Default select example">
                                <?php foreach ($category as $ctg) : ?>
                                    <option value="<?= $ctg['Id_category'] ?>" <?= $ctg['Id_category'] == $K3lhReport['ty_category'] ? 'selected' : null ?> ><?= $ctg['category'] ?></option>     
                                    <?php endforeach; ?>
                                </select>                        
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="contractors" class="col-sm-2 col-form-label">Description</label>
                            <div class="col-sm-4"> 
                                <input class="form-control" name="Description" rows="6" placeholder="Description" value="<?= $K3lhReport['Description'] ?>" required></input>                   
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                
                                <input name="id" type="text" class="form-control" value="<?= $K3lhReport['id'] ?>" hidden>
                                
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>                               
                                <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                                <script>
                                    function getData(){
                                        window.location.href="<?= site_url() ?>"+`/k3lh`;
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
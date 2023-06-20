<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

<div class="pagetitle">
        <h1>Master Data - K3LH - Stackholder</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Master Data</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("#") ?>">Tambah Stackholder</a></li>
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
                        <h5 class="card-title">Tambah Stackholder</h5>
                        <!-- notification -->
                <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
    <!-- button with modal -->                   
                        <div class="right-section">
                            <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                <i class="bi bi-plus me-1"></i> Tambah Data
                            </button>
                            <a href="<?= site_url("/tambah-manform") ?>">
                            <button type="button" class="btn btn-primary mb-2">
                                <i class="bi bi-plus me-1"></i> Tambah Form
                            </button>
                            </a>
                        </div>
                            
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("/tambah-manstack/add") ?>" method="POST" novalidate enctype="multipart/form-data" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Stackholder</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 mt-3">
                                                <!-- <label for="username" class="form-label">Production ID</label>
                                                <input type="text" class="form-control" id="prod_id" name="prod_id"> -->
                                            </div>
                                            <div class="col-md-12 mt-3">
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Stackholder</label>
                                                <div class="col-sm-12">
                                                    <input name="stockholder" id="stockholder" class="form-control" aria-label="Default select example" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                
                                                <div class="col-sm-12">
                                                                                                                                                    
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6 position-relative">
                                                    
                                                    <div class="col-sm-12">
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-md-6 position-relative">
                                                    
                                                    <div class="col-sm-12">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6 position-relative">
                                 
                                                    <div class="col-sm-12">
                                                        
                                                            
                                                    </div>
                                                </div>
                                                <div class="col-md-6 position-relative">
                                                    
                                                    <div class="col-sm-12">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                
                                                
                                            </div>
                                            <div class="col-md-12 mt-3"> 
                                            </div>
                                            <div class="col-md-12 mt-3">                                               
                                            </div>
                                            <div class="col-md-12 mt-3">   
                                            </div>
                                            <div class="col-md-12 mt-3">                                               
                                            </div>
                                            <div class="col-md-12 mt-3">    
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary" type="submit">Submit form</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="table-responsive">
                            <!-- Table users -->
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Stackholder</th>                                        
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($man_stack as $stack) : ?>
                                        <tr>
                                            <td><?= $stack['id_stockholder'] ?></td>
                                            <td><?= $stack['stockholder'] ?></td>
                                            <td class="col">                                          
                                                <a href="<?= site_url('/tambah-manstack/edit/') . urlencode($stack['id_stockholder']) ?>" class="btn btn-primary btn-sm col">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('tambah-manstack/delete/') ?><?= $stack['id_stockholder'] ?>" class="btn btn-danger btn-sm col">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>   
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
<script>
    $(document).ready(function() {
        $('#prd_date_text').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            minMonth: new Date().getMonth(),
            locale: {
                format: 'DD/MM/YYYY'
            },

        }, function(start, end, label) {
            $("#date").val(start.format('YYYY-MM-DD'));
        });
        $('#prd_date_text').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
        });

        $('#prd_date_text').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#date").val('');
        });
    });
</script>



<?= $this->endSection() ?>  
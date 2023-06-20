<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

<div class="pagetitle">
        <h1>CSR - CSR Budget</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">CSR</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("CSRBudget/") ?>">CSR Budget</a></li>
                <li class="breadcrumb-item active"><a href="#">Edit Budget</a></li>
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
                        <h5 class="card-title">CSR Budget edit</h5>
                        <!-- notification -->
                <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
            <form id="k3lhedit" action="<?= site_url("/CSRBudget/update") ?>" method="POST">
                            <?= csrf_field() ?>
                    <div class="row mt-3">
                        <div class="col-md-12 mt-3">
                            <label class="form-label">FORM</label>
                                <div class="col-md-12 position-relative">
                                    <select class="form-select" name="formtyp_bdg" id="formtyp_bdg" required>
                                        <option value="FORM PPM"<?= "FORM PPM" == $budgetcsr['formtyp_bdg'] ? 'selected' : null ?>>FORM PPM</option>
                                        <option value="NON PPM<"<?= "NON PPM" == $budgetcsr['formtyp_bdg'] ? 'selected' : null ?>>NON PPM</option>
                                    </select>
                                </div>
                        </div>
                        <div class="row mt-3">
                        <label class="form-label">Date</label>
                            <div class="col-md-6 position-relative">
                                <select class="form-control" name="bulan" id="bulan" onchange="filterMonth()" >
                                    <option value="1" <?= 1 == $budgetcsr['period_month'] ? 'selected' : null ?> >January</option>
                                    <option value="2" <?= 2 == $budgetcsr['period_month'] ? 'selected' : null ?>>February</option>
                                    <option value="3" <?= 3 == $budgetcsr['period_month'] ? 'selected' : null ?>>March</option>
                                    <option value="4" <?= 4 == $budgetcsr['period_month'] ? 'selected' : null ?>>April</option>
                                    <option value="5" <?= 5 == $budgetcsr['period_month'] ? 'selected' : null ?>>May</option>
                                    <option value="6" <?= 6 == $budgetcsr['period_month'] ? 'selected' : null ?>>June</option>
                                    <option value="7" <?= 7 == $budgetcsr['period_month'] ? 'selected' : null ?>>July</option>
                                    <option value="8" <?= 8 == $budgetcsr['period_month'] ? 'selected' : null ?>>August</option>
                                    <option value="9" <?= 9 == $budgetcsr['period_month'] ? 'selected' : null ?>>September</option>
                                    <option value="10" <?= 10 == $budgetcsr['period_month'] ? 'selected' : null ?>>October</option>
                                    <option value="11" <?= 11 == $budgetcsr['period_month'] ? 'selected' : null ?>>November</option>
                                    <option value="12" <?= 12 == $budgetcsr['period_month'] ? 'selected' : null ?>>Desember</option>
                                </select>
                            </div>
                            <div class="col-md-6 position-relative">
                                <select class="form-control" name="tahun" id="tahun" onchange="getDocReminder()">
                                    <?php foreach ($year as $y) { ?>
                                        <option value="<?= $y ?>"<?= $y == $budgetcsr['period_year'] ? 'selected' : null ?> ><?= $y ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Allocation</label>
                            <div class="col-sm-12">
                                <select name="allocation" id="allocation" class="form-select" aria-label="Default select example">
                                    <?php foreach ($new_allo as $a) : ?>
                                        <option value="<?= $a['id_allo'] ?>"<?= $a['allocation'] == $budgetcsr['allocation'] ? 'selected' : null ?>><?= $a['allocation']?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-10 mt-3">
                            <label class="form-label">Budget Amount</label>
                            <div class="col-sm-12">
                                <div class="input-group"> 
                                    <input id="rupiah" class="form-control" rows="6" placeholder="Rp." value="<?= $budgetcsr['budget_amount']?>" required></input>
                                    <input id="rupiah2" class="form-control" name="budget_amount" rows="6" placeholder="Rp." value="<?= $budgetcsr['budget_amount']?>" hidden></input>  
                                    <span class="input-group-text">IDR</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                
                                <input name="id" type="text" class="form-control" value="<?= $budgetcsr['id'] ?>" hidden>
                                
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>                               
                                <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                                <script>
                                    function getData(){
                                        window.location.href="<?= site_url() ?>"+`/CSRBudget`;
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

<script type="text/javascript">
		
        var rupiahnumber=document.getElementById('rupiah2');
		var rupiah = document.getElementById('rupiah');
		rupiah.addEventListener('keyup', function(e){
			// tambahkan 'Rp.' pada saat form di ketik
			// gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
            console.log(this.value);
            rupiahnumber.value = formatBiasa(this.value);
            rupiah.value = formatRupiah(this.value,'Rp. ');
		});
 
		/* Fungsi formatRupiah */
		function formatRupiah(angka, prefix){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

            
			// tambahkan titik jika yang di input sudah menjadi angka ribuan
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}
 
			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');

		} 

        function formatBiasa(angka){
            var number_biasa = angka.split(".").join("").split("Rp").join("");

            return number_biasa;
        }

	</script>

<?= $this->endSection() ?>
                                
                       
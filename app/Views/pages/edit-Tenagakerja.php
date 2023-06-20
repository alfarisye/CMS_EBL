<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

<div class="pagetitle">
        <h1>K3LH - Tenaga Kerja - Edit Tenaga Kerja</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">CSR</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("Manpower/") ?>">Tenaga Kerja</a></li>
                <li class="breadcrumb-item active"><a href="#">Edit Tenaga Kerja</a></li>
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
                        <h5 class="card-title">Edit Tenga Kerja</h5>
                        <!-- notification -->
                <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
            <form id="k3lhedit" action="<?= site_url("/Manpower/update") ?>" method="POST">
                            <?= csrf_field() ?>
                        <div class="row mt-3">
                            <div class="col-md-6 position-relative">
                                <select class="form-control" name="bulan" id="bulan" onchange="filterMonth()" >
                                    <option value="1" <?= 1 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?> >January</option>
                                    <option value="2" <?= 2 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>February</option>
                                    <option value="3" <?= 3 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>March</option>
                                    <option value="4" <?= 4 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>April</option>
                                    <option value="5" <?= 5 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>May</option>
                                    <option value="6" <?= 6 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>June</option>
                                    <option value="7" <?= 7 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>July</option>
                                    <option value="8" <?= 8 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>August</option>
                                    <option value="9" <?= 9 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>September</option>
                                    <option value="10" <?= 10 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>October</option>
                                    <option value="11" <?= 11 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>November</option>
                                    <option value="12" <?= 12 == $tenaga_kerja['month_jamkerja'] ? 'selected' : null ?>>Desember</option>
                                </select>
                            </div>
                            <div class="col-md-6 position-relative">
                                <select class="form-control" name="tahun" id="tahun" onchange="getDocReminder()">
                                    <?php foreach ($year as $y) { ?>
                                        <option value="<?= $y ?>"<?= $y == $tenaga_kerja['year_jamkerja'] ? 'selected' : null ?> ><?= $y ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Stackholder</label>
                            <div class="col-sm-12">
                            <select name="id_stockholder" id="id_stockholder" class="form-select" aria-label="Default select example">
                                <?php foreach ($stackholder as $stackholder) : ?>
                                    <option value="<?= $stackholder['id_stockholder'] ?>"<?= $stackholder['stockholder'] == $tenaga_kerja['stockholder'] ? 'selected' : null ?>><?= $stackholder['stockholder']?></option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                        </div>
                                <?php foreach ($man_form as $form) : ?>
                                    <div class="col-md-12 mt-3">
                                        <div class="sm-form">
                                            <label class="form-label" for="<?= $form['nama_form'] ?>"><?= $form['nama_form'] ?></label>
                                                <div class="col-sm-12">
                                                    <input type="number" id="<?= $form['id_form'] ?>" value="<?= $form['id_form'] ?>" name="idForm[]" class="form-control" aria-label="Default select example" hidden></input>
                                                        <?php foreach ($tambah_tenaga as $kerja) : ?>
                                                            <?php if($form['id_form']==$kerja['id_form']) { ?>
                                                                <input name="valueParameter[]" id="value" class="form-control" aria-label="Default select example" value="<?= $kerja['value'] ?>"></input>
                                                            <?php } ?>
                                                        <?php endforeach;?> 
                                                </div> 
                                        </div>                                                         
                                    </div>
                                <?php endforeach;?>
                            <div class="row mb-3">
                            <div class="col-sm-4">
                                
                                <input name="id_tenagakerja" type="text" class="form-control" value="<?= $tenaga_kerja['id_tenagakerja'] ?>" hidden></input>
                                
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>                               
                                <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                                <script>
                                    function getData(){
                                        window.location.href="<?= site_url() ?>"+`/Manpower`;
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
                                
                       
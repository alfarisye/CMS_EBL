<?php 

function rupiah($angka){
    $hasil_rupiah = "Rp." . number_format($angka,2,',','.');
    return $hasil_rupiah;
}



$bulan = [
    1 => "Januari",
    2 => "Febuari",
    3 => "Maret",
    4 => "April",
    5 => "Mei",
    6 => "Juni",
    7 => "Juli",
    8 => "Agustus",
    9 => "September",
    10 => "Oktober",
    11 => "November",
    12 => "Desember"
];
$allocation = [
    1 => "Pendidikan",
    2 => "Kesehatan",
    3 => "Kemandirian Ekonomi",
    4 => "Sosial Budaya",
    5 => "Pembangunan Fisik",
];


?>
<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>K3LH - Tenaga Kerja</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">K3LH</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("Manpower/") ?>">Tenaga Kerja</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tenaga Kerja</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->

                        <div class="d-flex justify-content-between mb-4">
                            <div class="d-flex left-section col-5">
                                <select class="form-control" name="bulan" id="bulan" onchange="filterMonth()">
                                    <option value="">Silahkan pilih bulan</option>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                <span class="mx-2 my-auto"></span>
                                <select class="form-control" name="tahun" id="tahun" onchange="getDocReminder()">
                                    <option value="">Silahkan pilih tahun</option>
                                    <?php foreach ($year as $y) { ?>
                                        <option value="<?= $y ?>"><?= $y ?></option>
                                    <?php } ?>
                                </select>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">Filter</button>
                                <script>
                                    function getData() {
                                        let bulan = document.getElementById('bulan').value
                                        let tahun = document.getElementById('tahun').value
                                        if (bulan != '' && tahun != '') {
                                            window.location.href = "<?= site_url() ?>" + `/Manpower?bulan=${bulan}&tahun=${tahun}`;
                                        }
                                    }
                                </script>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="refresh" name="refresh" onclick="getData2()">Refresh</button>
                                <script>
                                    function getData2() {
                                        let bulan = document.getElementById('bulan').value
                                        let tahun = document.getElementById('tahun').value
                                        window.location.href = "<?= site_url() ?>" + `/Manpower`;
                                    }
                                </script>
                            </div>
                            <div class="right-section">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Tambah Data
                                </button>
                                <a href="<?= site_url("/Jamkerja") ?>">
                                <button type="button" class="btn btn-primary mb-2">
                                    <i class="bi bi-plus me-1"></i> Jam Kerja
                                </button>
                                </a>
                            </div>
                        </div>



                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("Manpower/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Add Tenaga Kerja</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 mt-3">
                                                <!-- <label for="username" class="form-label">Production ID</label>
                                                <input type="text" class="form-control" id="prod_id" name="prod_id"> -->
                                            </div>
                                            
                                            <div class="col-md-12 mt-3">
                                                <!-- <label for="username" class="form-label">Production ID</label>
                                                <input type="text" class="form-control" id="prod_id" name="prod_id"> -->
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6 position-relative">
                                                    <select class="form-control" name="bulan" id="bulan" onchange="filterMonth()" required>
                                                        <option value="">Silahkan pilih bulan</option>
                                                        <option value="1">January</option>
                                                        <option value="2">February</option>
                                                        <option value="3">March</option>
                                                        <option value="4">April</option>
                                                        <option value="5">May</option>
                                                        <option value="6">June</option>
                                                        <option value="7">July</option>
                                                        <option value="8">August</option>
                                                        <option value="9">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">Desember</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 position-relative">

                                                    <select class="form-control" name="tahun" id="tahun" onchange="getDocReminder()" required>
                                                        <option value="">Silahkan pilih tahun</option>
                                                        <?php foreach ($year as $y) { ?>
                                                            <option value="<?= $y ?>"><?= $y ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Stackholder</label>
                                                <div class="col-sm-12">
                                                    <select name="id_stockholder" id="id_stockholder" class="form-select" aria-label="Default select example">
                                                        <option selected>Please Select Contractors</option>
                                                        <?php foreach ($stackholder as $stackholder) : ?>
                                                            <option value="<?= $stackholder['id_stockholder'] ?>"><?= $stackholder['stockholder'] ?></option>
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
                                                                <input name="valueParameter[]" id="value" class="form-control" aria-label="Default select example"></input>
                                                            </div> 
                                                    </div>                                                         
                                                    </div>
                                                <?php endforeach;?>
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
                                        
                                        <th scope="col">Month</th>
                                        <th scope="col">Year</th>
                                        <th scope="col">Stackholder</th>
                                        <?php foreach ($man_form as $form) : ?>
                                            <th>
                                                <?= $form['nama_form'] ?>
                                            </th>
                                        <?php endforeach;?>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tenaga_kerja as $kerja) : ?>
                                        <tr>
                                            <td><?= $kerja['month_jamkerja'] != null ? $bulan[$kerja['month_jamkerja']] : '' ?></td>
                                            <td><?= $kerja['year_jamkerja'] ?></td>
                                            <td><?= $kerja['stockholder']?></td>
                                            <?php foreach ($man_form as $form) : ?>
                                                <td>
                                                    <?php foreach ($tambah_tenaga as $work) : ?>
                                                        <?php if($form['id_form']==$work['id_form'] && $work['id_tenagakerja']==$kerja['id_tenagakerja']) { ?>
                                                            <?= $work['value'] ?>
                                                        <?php } ?>
                                                    <?php endforeach;?> 
                                                </td>
                                            <?php endforeach;?>
                                            <td class="col">
                                                <a href="<?= site_url('Manpower/edit/') . urlencode($kerja['id_tenagakerja']) ?>" class="btn btn-primary btn-sm col">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('Manpower/delete/') ?><?= $kerja['id_tenagakerja'] ?>" class="btn btn-danger btn-sm col">
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
    $(document).ready(function() {

        $('#type').change(function() {
            var id_type = $(this).val();

            var action = 'get_category';

            if (id_type != '') {
                $.ajax({
                    url: "<?php echo site_url('k3lh/action/'); ?>" + id_type,
                    method: "GET",
                    success: function(data) {
                        console.log(data);
                        const obj = JSON.parse(data);
                        $('#Category').empty();
                        $.each(obj, function(key, value) {
                            $('#Category').append('<option value="' + value.Id_category + '">' + value.category + '</option>');
                        });

                    }
                });
            } else {
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
<?php 

function rupiah($angka){
    $hasil_rupiah = "Rp." . number_format($angka,2,',','.');
    return $hasil_rupiah;
}
?>



<?php
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
        <h1>CSR - CSR Activity</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">CSR</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("CSR/") ?>">CSR Activity</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">CSR Activity</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->

                        <div class="d-flex justify-content-between mb-4">
                            <div class="d-flex left-section col-5">
                                <input class="form-control" type="date" id="tgl_awal" name="tgl_awal" placeholder="tgl_awal" value="<?= date('Y-m-d'); ?>">
                                <span class="mx-2 my-auto">to</span>
                                <input class="form-control" type="date" id="tgl_akhir" name="tgl_akhir" placeholder="tgl_akhir" value="<?= date('Y-m-d'); ?>">
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">Filter</button>
                                <script>
                                    function getData() {
                                        let tgl_awal = document.getElementById('tgl_awal').value
                                        let tgl_akhir = document.getElementById('tgl_akhir').value
                                        window.location.href = "<?= site_url() ?>" + `/CSRAct?tgl_awal=${tgl_awal}&tgl_akhir=${tgl_akhir}`;
                                    }
                                </script>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="refresh" name="refresh" onclick="getData2()">Refresh</button>
                                <script>
                                    function getData2() {
                                        let tgl_awal = document.getElementById('tgl_awal').value
                                        let tgl_akhir = document.getElementById('tgl_akhir').value
                                        window.location.href = "<?= site_url() ?>" + `/CSRAct`;
                                    }
                                </script>
                            </div>
                            <div class="right-section">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> New CSR Activity
                                </button>
                            </div>
                        </div>



                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("CSRAct/add") ?>" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">New Activity</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="row mt-3">
                                                <label class="form-label">FORM</label>
                                                <div class="col-md-12 position-relative">
                                                    <select class="form-select" name="formtyp_act" id="from" required>
                                                        <option value="1">FORM PPM</option>
                                                        <option value="2">NON PPM</option>
                                                        <option value="2">BOTH</option>
                                                    </select>
                                                </div>
                                        </div>
                                            <div class="col-md-12 mt-3">
                                                <!-- <label for="username" class="form-label">Production ID</label>
                                                <input type="text" class="form-control" id="prod_id" name="prod_id"> -->
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="username" class="form-label">Date</label>
                                                <input type="text" class="form-control" id="date" name="date" required>
                                                <input type="date" class="form-control" id="date" name="date" hidden>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Allocation</label>
                                                <div class="col-sm-12">
                                                    <select name="allocation" id="allocation" class="form-select" aria-label="Default select example" required>
                                                        <option value="">Select Allocation</option>
                                                        <?php foreach ($new_allo as $a) : ?>
                                                            <option value="<?= $a['id_allo'] ?>"><?= $a['allocation'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Location</label>
                                                <div class="col-sm-12">
                                                    <input name="location" id="location" class="form-control" aria-label="Default select example" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Activity</label>
                                                <div class="col-sm-12">
                                                    <textarea name="activity" id="activity" class="form-control" aria-label="Default select example" required></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Remark</label>
                                                <div class="col-sm-12">
                                                    <textarea name="Remark" id="Remark" class="form-control" aria-label="Default select example" ></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-10 mt-3">
                                                <label class="form-label">Total Cost</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <input id="rupiah" class="form-control"  rows="6" placeholder="Rp." required></input>
                                                        <input id="rupiah2" class="form-control" name="actual_cost"  rows="6" placeholder="Rp." hidden></input>
                                                        <span class="input-group-text">IDR</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-10 mt-3">
                                                <label class="form-label">Upload File</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group">
                                                        <input type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/vnd.rar" id="userfile" name="userfile" class="form-control p-1 text-xs rounded-sm shadow-sm" placeholder="userfile" required></input>
                                                        <span class="input-group-text">*MAX 5MB</span>
                                                    </div>
                                                </div>
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
                                        <th scope="col">Document No</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Allocation</th>
                                        <th scope="col">Location</th>
                                        <th scope="col">Activity</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Actual Cost(IDR)</th>
                                        <th scope="col">Remark</th>
                                        <th scope="col">Download</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($Activitycsr as $act) : ?>
                                        <tr>
                                            <td><?= $act['doc_no'] ?></td>
                                            <td><?= date('d/m/Y', strtotime($act['date']) ) ?></td>
                                            <td><?= $act['allocation'] ?></td>
                                            <td><?= $act['location'] ?></td>
                                            <td><?= $act['activity'] ?></td>
                                            <td><?= $act['formtyp_act'] ?></td>
                                            <td><?= rupiah($act['actual_cost']) ?></td>
                                            <td><?= $act['Remark']?></td>
                                            <td class="col">
                                                <a href="<?= site_url('/CSRAct/download/') . urlencode($act['id']) ?>" class="btn btn-success btn-sm">
                                                    <i class="bi-download"></i>
                                                </a>
                                            <td class="col">
                                                <a href="<?= site_url('/CSRAct/edit/') . urlencode($act['id']) ?>" class="btn btn-primary btn-sm col">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('/CSRAct/delete/') ?><?= $act['id'] ?>" class="btn btn-danger btn-sm col">
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
            $("#date").val(start.format('DD-MM-YYYY'));
        });
        $('#prd_date_text').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
            $("#date").val(picker.startDate.format('DD-MM-YYYY'));
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
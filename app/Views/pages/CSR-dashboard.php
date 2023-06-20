<?php 

function rupiah($angka){
    $hasil_rupiah = "Rp." . number_format($angka ?? 0,2,',','.');
    return $hasil_rupiah;
}
?>


<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>CSR - Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">CSR</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("CSRAct/dashboard") ?>">Dashboard</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="row">
        <span class="form-label">FROM</span>
            <div class="col-md-3">
                <div class="card">
                    <select class="form-select" name="from" id="from" onchange="filterform()">
                        <option value="">Silahkan Pilih Tipe data</option>    
                        <option value="FORM"<?php if ($_GET=="parameter"){echo'selected';} ?> >FORM PPM</option>
                        <option value="NON"<?php if ($_GET=="parameter"){echo'selected';}  ?> >NON PPM</option>
                        <option Value="BOTH"<?php if ($_GET=="parameter"){echo'selected';}  ?> >BOTH</option>
                    </select>
                </div>
            </div>

    <section class="section">
        <div class="row d-flex justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Actual Pendidikan</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-book" style="font-size: 50px; color:#000000;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= rupiah($actual_pendidikan['total']) ?></h3>
                                    <h5 style="font-size: 16px"><?= ($actual_pendidikan['budget'] != null) ? round($actual_pendidikan['total'] / $actual_pendidikan['budget'] * 100 ,2) : 0 ?>% cost plan</h5>
                                </div>                                
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Actual Kesehatan</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-heart-3-fill" style="font-size: 50px; color: #000000;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= rupiah($actual_kesehatan['total']) ?></h3>
                                    <h5 style="font-size: 16px"><?= ($actual_kesehatan['budget'] != null)? round($actual_kesehatan['total'] / $actual_kesehatan['budget'] * 100 ,2) :0 ?>% cost plan</h5>
                                </div>  
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Actual Kemandirian Ekonomi</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-line-chart-fill" style="font-size: 50px;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= rupiah($actual_kemandirian['total']) ?></h3>
                                    <h5 style="font-size: 16px"><?= ($actual_kemandirian['budget'] != null) ? round($actual_kemandirian['total'] / $actual_kemandirian['budget'] * 100 ,2):0 ?>% cost plan</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Actual Sosial Budaya</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= rupiah($actual_sosbud['total']) ?></h3>
                                    <h5 style="font-size: 16px"><?= ($actual_sosbud['budget'] != null)? round($actual_sosbud['total'] / $actual_sosbud['budget'] * 100 ,2) :0 ?>% cost plan</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Actual Infrastruktur</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-building-fill" style="font-size: 50px;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= rupiah($actual_pembangunan['total']) ?></h3>
                                    <h5 style="font-size: 16px"><?= ($actual_pembangunan['budget'] != null) ? round($actual_pembangunan['total'] / $actual_pembangunan['budget'] * 100 ,2) : 0 ?>% cost plan</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row d-flex justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Activity</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-donut-chart-fill" style="font-size: 3rem;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <h3><?= $actual_activity ?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Location</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-map-fill" style="font-size: 3rem;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <h3><?= $total_location ?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Actual Cost</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-cash" style="font-size: 3rem;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <h3 class=""><?= rupiah($total_cost['total']) ?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <select class="form-control" name="tahun" id="tahun" onchange="filterChart()">
                        <option value="">Silahkan pilih tahun</option>
                        <?php foreach ($year as $y) { ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <select class="form-control" name="bulan" id="bulan" onchange="filterChart()">
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
            </div>
        </div>

        <!-- charts -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Cost (Plan vs Actual)</h3>
                        <canvas id="costChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Actual Cost by Location</h3>
                        <canvas id="locationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script type="text/javascript">
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    $('#from').change(function() {
        const selectedFrom  = $(this).val();
       window.location.href = "<?= site_url('CSRAct/dashboard') ?>" + "?parameter=" + selectedFrom;

       if (selectedFrom == "BOTH"){
        const selectedFrom  = $(this).val();
       window.location.href = "<?= site_url('CSRAct/dashboard') ?>"

       }
       
    });

</script>

<script>
    let ctx, costChart, locationChart, ctx2;
    let savedData;
    $(document).ready(function() {
        ctx = document.getElementById('costChart').getContext('2d');
        costChart = new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: [{
                        label: 'Actual',
                        data: <?= $actual_data ?>,
                        backgroundColor: [
                            'rgba(226, 243, 0, 1)',
                        ],
                        borderWidth: 1
                    },
                    {
                        label: 'Plan',
                        data: <?= $plan_data ?>,
                        backgroundColor: [
                            'rgba(15, 236, 49, 1)',
                        ],
                        borderWidth: 1
                    },
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,

                    }
                },
                parsing: {
                    xAxisKey: 'allocation',
                    yAxisKey: 'total'
                }
            }
        });
        ctx2 = document.getElementById('locationChart').getContext('2d');
        locationChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                datasets: [{
                        label: 'Actual',
                        data: <?= $cost_location ?>,
                        backgroundColor: [
                            'rgba(15, 236, 49, 1)   ',
                        ],
                        borderWidth: 1
                    },
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                },
                parsing: {
                    xAxisKey: 'location',
                    yAxisKey: 'total'
                }
            }
        });
    });
    const filterform = function(){
        let from = $('#form').val();
        if (form == ''){
            return;
        }
    }
    const filterChart = function() {
        let bulan = $('#bulan').val();
        let tahun = $('#tahun').val();
        if (bulan == '' || tahun == '') {
            return;
        }
        costChart.data.datasets = [];
        locationChart.data.datasets = [];
        fetch(`<?= site_url("/CSRAct/get/") ?>` + `${tahun}/${bulan}`)
            .then(response => response.json())
            .then(data => {
                costChart.data.datasets = [
                    {
                        label: 'Actual',
                        data: data.actual_data,
                        backgroundColor: [
                            'rgba(226, 243, 0, 1)',
                        ],
                        borderWidth: 1
                    },
                    {
                        label: 'Plan',
                        data: data.plan_data,
                        backgroundColor: [
                            'rgba(15, 236, 49, 1)',
                        ],
                        borderWidth: 1
                    },
                ];
                locationChart.data.datasets = [
                    {
                        label: 'Actual',
                        data: data.cost_location,
                        backgroundColor: [
                            'rgba(15, 236, 49, 1)',
                        ],
                        borderWidth: 1
                    },
                ];
                costChart.update();
                locationChart.update();
            });
    };
</script>

<?= $this->endSection() ?>
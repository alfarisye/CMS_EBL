<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Document Reminder Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Administration</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("group-email/") ?>">Document Reminder Dashboard</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Doc</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-files" style="font-size: 3rem;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <h3><?= $total_count ?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Undelivered</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock-history" style="font-size: 3rem; color: #ff771d;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <h3><?= $undelivered_count ?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Delivered</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-file-check" style="font-size: 3rem;color: #2eca6a"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <h3><?= $delivered_count ?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <select class="form-control" name="tanggal" id="tanggal" onchange="getDocReminder()">
                        <option value="">Silahkan pilih tahun</option>
                        <?php foreach ($year as $y) { ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
            <div class="card">
                    <select class="form-control" name="bulan" id="bulan" onchange="filterMonth()">
                        <option value="">Silahkan pilih bulan</option>
                        <option value="January">January</option>
                        <option value="February">February</option>
                        <option value="March">March</option>
                        <option value="April">April</option>
                        <option value="May">May</option>
                        <option value="June">June</option>
                        <option value="July">July</option>
                        <option value="August">August</option>
                        <option value="September">September</option>
                        <option value="October">October</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Total Email Sent</h3>
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<script>
    let ctx, myChart;
    let savedData;
    $(document).ready(function() {
        ctx = document.getElementById('myChart').getContext('2d');
        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: [{
                        label: 'Delivered',
                        data: <?= $delivered ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 1)',
                        ],
                        borderWidth: 1
                    },
                    {
                        label: 'Undelivered',
                        data: <?= $undelivered ?>,
                        backgroundColor: [
                            'rgba(255, 159, 64, 1)',
                        ],
                        borderWidth: 1
                    },
                    {
                        label: 'Total',
                        data: <?= $total ?>,
                        backgroundColor: [
                            'rgba(54, 162, 235, 1)',
                        ],
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,

                    }
                },
                parsing: {
                    xAxisKey: 'month',
                    yAxisKey: 'val'
                }
            }
        });
    });
    const getDocReminder = function() {
        let tanggal = $('#tanggal').val();
        if (tanggal == '') {
            return alert('Silahkan pilih tanggal');
        }
        myChart.data.datasets = [];
        fetch(`<?= site_url("/doc-reminder/api/get-data/") ?>` + tanggal)
            .then(response => response.json())
            .then(data => {
                savedData = data;
                myChart.data.datasets = [{
                        label: 'Delivered',
                        data: data.delivered,
                        backgroundColor: [
                            'rgba(255, 99, 132, 1)',
                        ],
                        borderWidth: 1
                    },
                    {
                        label: 'Undelivered',
                        data: data.undelivered,
                        backgroundColor: [
                            'rgba(255, 159, 64, 1)',
                        ],
                        borderWidth: 1
                    },
                    {
                        label: 'Total',
                        data: data.total,
                        backgroundColor: [
                            'rgba(54, 162, 235, 1)',
                        ],
                        borderWidth: 1
                    }
                ];
                myChart.update();
            });
    };

    const filterMonth = function() {
        let tanggal = $('#bulan').val();

        if (tanggal == '') {
            return alert('Silahkan pilih bulan');
        }
        if (savedData == undefined) {
            return alert('Silahkan pilih tahun');
        }

        myChart.data.datasets.forEach(element => {
            element.data = savedData[element.label.toLowerCase()];
            element.data = element.data.filter(function(item) {
                if (item.month == tanggal) {
                    return item;
                }
            });
        });
        myChart.update();

    }

</script>

<?= $this->endSection() ?>
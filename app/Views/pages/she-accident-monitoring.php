<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>K3LH - K3LH Monitoring Accident</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">K3LH</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("k3lh/monitoring") ?>">Monitoring</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="card">
            <div class="card-body">
                <h5 class="card-title" >Dasboard </h5>
                <div class="row">
                    <div class="col-sm-2">
                        <a href="<?= site_url('k3lh/monitoring') ?>">
                            <button type="button" class="btn btn-primary mb-2" >SHE Accident Monitoring</button>
                        </a>
                    </div>
                    <div class="col-sm-2">
                        <a href="<?= site_url('kualitasair/monitoring') ?>">
                        <button type="button" class="btn btn-primary mb-2" >Kualitas Air Monitoring</button>
                        </a>
                    </div>
                    <div class="col-sm-2">
                        <a href="<?= site_url('Manpower/monitoring') ?>">
                        <button type="button" class="btn btn-primary mb-2" >Manpower Monitoring</button>
                        </a>
                    </div>
                    </div>
                </div>
            </div>
        </div>

    <section class="section">
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total LTI</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-fill text-warning" style="font-size: 3rem;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end " >
                                <h3 class="text-warning"><?= $losttime ?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Potential Total LTI</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-fill text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <h3 class="text-primary"><?= $potential ?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Non Potential Total LTI</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-fill text-success" style="font-size: 3rem;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <h3 class="text-success"><?= $nonpotential ?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <select class="form-select" name="tahun" id="tahun" onchange="filterCard()">
                    <option value="">Silahkan pilih tahun</option>
                    <?php foreach ($year as $y) { ?>
                        <option value="<?= $y ?>"><?= $y ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-2">
                <select class="form-select" name="bulan" id="bulan" onchange="filterCard()">
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
        <div class="row my-3" id="fetch-target">
            <?= $this->include('templates/k3lh-cards') ?>
        </div>
    </section>
</main>
<script>
    const fetchTarget = document.getElementById("fetch-target");
    
    const filterCard = function() {
        const tahun = document.getElementById('tahun').value;
        const bulan = document.getElementById('bulan').value;
        if (tahun === '' || bulan === '') {
            return;
        }
        fetchTarget.innerHTML = "<p class='text-center'>Please wait...</p>";
        fetch(`<?= site_url("/k3lh/get-card/") ?>` + tahun + '/' + bulan)
            .then(res => res.text())
            .then(res => {
                fetchTarget.innerHTML = res;
            })
    };

</script>

<?= $this->endSection() ?>
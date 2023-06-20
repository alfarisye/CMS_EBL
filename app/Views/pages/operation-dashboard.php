<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Operation Report</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Operation</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("group-email/") ?>">Dashboard</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <!-- <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <select class="form-control" name="option" id="option" onchange="getDocReminder()">
                        <option value="">Silahkan pilih</option>
                        <option value="mtd">Month to Date (MTD)</option>
                        <option value="ytd">Year to Date (YTD)</option>
                        <option value="other">Others</option>
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
        </div> -->
        <!-- Charts -->
        <div id="charts">
            <?= $this->include('templates/operation-charts') ?>
        </div>
    </section>

</main><!-- End #main -->

<script>
    // javascript
</script>

<?= $this->endSection() ?>
<?= $this->section('main') ?>
<!-- <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script> -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.27.2/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/date-fns/1.30.1/date_fns.min.js"></script>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/jstat@1.9.2/dist/jstat.min.js"></script> 
<script src="https://cdn.jsdelivr.net/gh/formulajs/formulajs@2.9.3/dist/formula.min.js"></script>
<style>
    @media print {
        .no-print,
        .no-print * {
            display: none !important;
        }
        .print * {
            display:initial !important;
        }
    }
</style>
<main id="main" class="main">
    <div>
        <?= view('pages/tools/multi') ?> 
    </div>
</main>
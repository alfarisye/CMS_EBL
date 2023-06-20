<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->


<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .border-hover{border:1px solid transparent}
    .border-hover:hover{border:1px solid #d0d0d0}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:100;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:105;min-width:50vw}
</style>
<main id="main" class="main">
    <div id="testView" class="d-none">

        <div class="pagetitle">
            <h1>Test</h1>
            <form action="" method="GET">
                <div class="sm-form ">
                    <label for="inputan1">inputan1</label>
                    <input type="text" id="inputan1" name="inputan1" class="form-control p-1  text-xs shadow-sm" placeholder="inputan1"  >
                </div>
                
                <div class="sm-form ">
                    <label for="inputan2">inputan2</label>
                    <input type="text" id="inputan2" name="inputan2" class="form-control p-1  text-xs shadow-sm" placeholder="inputan2"  >
                </div>
                
                <div class="sm-form ">
                    <label for="inputan3">inputan3</label>
                    <input type="text" id="inputan3" name="inputan3" class="form-control p-1  text-xs shadow-sm" placeholder="inputan3"  >
                </div>
                <hr>

                <?php foreach($t_parameter as $form):?>
                    <div class="sm-form ">
                        <label for="<?= $form['nama_parameter'] ?>"><?= $form['nama_parameter'] ?></label>
                        <input type="number" id="<?= $form['id_parameter'] ?>" name="<?= $form['id_parameter'] ?>" class="form-control p-1  text-xs shadow-sm" placeholder="<?= $form['nama_parameter'] ?>"  >
                    </div>
                <?php endforeach;?>
                <br>
                <button type="submit" class="btn btn-sm btn-dark  ">Kirim</button>
            </form>
        </div><!-- End Page Title -->

    </div>
    <div id="pageloading" style="height:80vh;" class="d-flex justify-content-center align-items-center text-center animate__animated animate__bounce animate__infinite text-2xl font-times">
            Loading ...
    </div>
</main><!-- End #main -->

<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    new Vue({
        el:"#testView",
        data(){
            return{
               
            }
        },
        methods: {
        },
        mounted() {
            document.getElementById('testView').classList.remove('d-none');
            document.getElementById('pageloading').remove()
        },
    })
</script>

<?= $this->endSection() ?>
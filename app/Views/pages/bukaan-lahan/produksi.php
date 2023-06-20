<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->
<link rel="stylesheet" href="https://unpkg.com/vue-select@3.0.0/dist/vue-select.css">
<!-- SCRIPT -->
<script src="https://unpkg.com/vue-select@3.0.0"></script>


<style>
    table td {
        position: relative;
    }

    table td textarea {
        position: absolute;
        top:0;
        left:0;
        margin: 0;
        height: 100%;
        width: 100%;
        border: none;
        padding: 10px;
        box-sizing: border-box;
        text-align: start;
    }
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:100;background:black;opacity: 0.5;}
    .modal2{position: fixed;top: 50%;left: 50%;transform: translateX(-50%) translateY(-50%);z-index:105;min-width:50vw;}
</style>


<main id="main" class="main">
    <div id="bukaanLahanProduksi" class="d-none">
        <div class="pagetitle">
            <h1>Bukaan Lahan - Tambah Data Bukaan Lahan</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Bukaan Lahan</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("bukaan-lahan/produksi") ?>">Tambah Data Bukaan Lahan</a></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <!-- MODAL -->
            <div v-if="modal" @click="modal=false" class="modal1"></div>
            <div v-if="modal" class="modal2">
                <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn">
                    <div class="sm-form">
                    <textarea type="text"
                    v-model="target['vmodel'][target.key]" 
                    id="alamat" name="alamat" rows="5" placeholder="alamat..." class="form-control md-textarea" >
                    </textarea>
                    </div>
                </div>
            </div>
                <!-- MODAL -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title" >Bukaan Lahan</h5>
                            <!-- notification -->
                            <!-- b-table -->
                            <div>
                                <div class="row py-2" >
                                    <div class="col-sm-3">
                                        <p class="font-semibold">Years :</p>
                                        <div class="sm-form ">
                                            <select class='form-control' v-model="tahun" @change="page=1;getForm()">
                                                <option v-for="(item, index) in listTahun" :key="index+'listTahun'">{{item}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <p class="font-semibold">Months :</p>
                                        <div class="sm-form ">
                                            <select class='form-control' v-model="bulan" @change="getForm">
                                                <option value="01">Januari</option>
                                                <option value="02">Februari</option>
                                                <option value="03">Maret</option>
                                                <option value="04">April</option>
                                                <option value="05">Mei</option>
                                                <option value="06">Juni</option>
                                                <option value="07">Juli</option>
                                                <option value="08">Agustus</option>
                                                <option value="09">September</option>
                                                <option value="10">Oktober</option>
                                                <option value="11">November</option>
                                                <option value="12">Desember</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <p class="font-semibold">Type Produksi</p>
                                        <select class='form-control' v-model="typeProduksi" @change="page=1;getForm()">
                                            <option v-for="(item, index) in bl_type" :key="index+'bl_type3'" :value="item.id">{{item.nama_type}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row py-2 justify-content-center" >
                                    <div class="col-sm-10" v-if="listForm.length>0">
                                        <form action="" @submit.prevent="insertDataProduksi">
                                            <div class="row shadow rounded-lg p-4 justify-content-center">
                                                <div class="col-12 font-semibold text-lg">
                                                    Input Form {{bl_type.filter(e=>e.id==typeProduksi)[0].deskripsi}}
                                                </div>
                                                <div v-for="(item, index) in listForm" :key="index+'listForm'" class="col-sm-4" v-if="item.status=='true'">
                                                    <div class="my-2">
                                                        <p class="font-semibold pt-2">
                                                            {{item.nama_form}}
                                                        </p>
                                                        <div class="sm-form ">
                                                            <input type="text" 
                                                            required
                                                            :id="`${item.bl_type_id}-${item.blok_id}`" 
                                                            :name="`${item.bl_type_id}-${item.blok_id}`" 
                                                            v-model="vdata[`${item.bl_type_id}-${item.blok_id}`]" 
                                                            :placeholder="item.nama_form" 
                                                            class="form-control p-2 rounded-md shadow-sm" 
                                                        >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12"></div>
                                                <div class="col-sm-6 p-3">
                                                    <div class="sm-form ">
                                                        <p class="p-2 font-semibold">GeoJson : </p>
                                                        <input :required="geojson.length==0" type="file" id="filenya" name="file" class="form-control p-1 rounded text-xs" placeholder="file" >
                                                        <!-- <button type="button" class="btn btn-sm btn-dark  my-1" @click="uploadGeojson">Upload</button> -->
                                                        <p class="my-1 text-sm" v-if="geojson.length>0">
                                                            Data Geojson bl_id dan periode :  <span class="font-semibold ">
                                                                {{geojson[0].bl_type_id}} / {{geojson[0].periode}}
                                                            </span> 
                                                            <button type="button" @click="deleteGeojson()" class="btn btn-sm btn-danger ml-2 mt-2">Delete Geojson</button>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6"></div>
                                                <div class="col-12 text-right">
                                                    <button type="submit"  class="btn btn-sm btn-style2">Update Data</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

</main><!-- End #main -->

<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    new Vue({
        el:"#bukaanLahanProduksi",
        data(){
            return{
                // CUSTOM
                datanya:[],
                bl_blok:[],
                bl_type:[],
                bl_form:[],
                listForm:[],
                listTahun:[],
                vdata:{},
                formTable:[],
                geojson:[],
                typeProduksi:'',
                bulan:'',
                tahun:'',
                periode:'',
                formInsert:false,
                modal:false,
                dari_tanggal:'',
                sampai_tanggal:'',
                option:{
                    headers:{
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                }
            }
        },
        computed:{
            td(){
                let that=this;
                let data=this.datanya;
                let keys=Object.keys(this.datanya[0]);
                data=data.filter(e=>{
                    let txt='';
                    keys.forEach(k=>{
                        txt+=e[k];
                    })
                    if(txt.toLowerCase().indexOf(that.search.toLowerCase())!=-1){
                        return e
                    }
                })
                data=data.filter(e=>{
                    if(e.bl_type_id.indexOf(that.typeProduksi)!=-1){
                        return e
                    }
                })
                this.totalPage = Math.ceil(data.length/this.perPage);
                data=data.slice((this.page-1)*this.perPage,this.page*this.perPage);
                return data;
            }
        },  
        methods: {
            async deleteGeojson(){
                let data={};
                if(!confirm('Are you sure to delete this file ? '))return;
                data['bl_type_id']=this.typeProduksi;
                data['periode']=this.periode;
                console.log(data)
                await axios.post("<?= site_url() ?>" + `/api/bukaan-lahan/geojson/delete`,data);
                window.location.reload();
            },
            async uploadGeojson(){
                let data={}
                let file = document.querySelector("#filenya"); // berikan id pada input file
                if(file.files.length>0){
                    if(!confirm('Are you sure to upload this file ? '))return;
                    if(!(file.files[0].name.indexOf('.geojson')!=-1)){
                        sdb.alert('Error format file must be *.geojson !');
                        return false;
                    }
                    sdb.loadingOn();
                    let fd= new FormData();
                    fd.append('file',file.files[0]);
                    fd.append('<?= csrf_token() ?>','<?= csrf_hash() ?>');
                    await axios.post("<?= site_url() ?>" + `/api/upload`,fd,this.option).then(async (res)=>{
                        data['bl_type_id']=this.typeProduksi;
                        data['periode']=this.periode;
                        data['geojson']=res.data.filepath;
                    await axios.post("<?= site_url() ?>" + `/api/bukaan-lahan/geojson`,data).then(res=>{
                        console.log(res)
                        });
                        sdb.loadingOff();
                        window.location.reload();
                    });
                }
            },
            async insertDataProduksi(){
                let that=this;
                let file = document.querySelector("#filenya"); // berikan id pada input file
                
                async function loop(datanya,i=0){
                    for(const e of datanya){
                        await new Promise(async (resolve,reject)=>{
                            // PROGRESS SEBELUM RESOLVE
                            let data={};
                            data['type_blok']=`${e['bl_type_id']}-${e['blok_id']}`;
                            data['bl_type_id']=e['bl_type_id'];
                            data['blok_id']=e['blok_id'];
                            data['data_produksi']=that.vdata[`${e['bl_type_id']}-${e['blok_id']}`];
                            data['periode']=that.periode;
                            await that.upsertDataProduksi(data);
                            setTimeout(() => {
                                i++;
                                resolve();
                            }, 0);
                        })
                    }
                }
                console.log("START")
                sdb.loadingOn();
                await loop(this.listForm);
                sdb.alert('Berhasil Update data Produksi!','bg-green-400');
                sdb.loadingOff();
                this.uploadGeojson();
                console.log('END')
            },
            async getForm(){
                let that=this;
                this.datanya=[]
                this.geojson=[]
                this.listForm=[]
                this.vdata={}
                this.$forceUpdate();
                if(this.bulan && this.tahun && this.typeProduksi){
                    this.periode=`${this.tahun}-${this.bulan}-${this.hari(new Date())}`;
                    console.log(this.periode)
                    let form = this.bl_form.filter(e=>e.bl_type_id==this.typeProduksi)
                    this.listForm=form
                    var date = new Date(this.periode);
                    this.dari_tanggal = this.format(new Date(date.getFullYear(), date.getMonth(), 1));
                    this.sampai_tanggal = this.format(new Date(date.getFullYear(), date.getMonth() + 1, 0));
                    this.getData();
                    this.$forceUpdate();
                }
            },
            formInsertCek(){
                this.formInsert=!this.formInsert;
                this.$forceUpdate();
            },
            async upsertDataProduksi(data){
                let that=this;
                return new Promise(async (resolve,reject)=>{
                    await axios.post("<?= site_url() ?>" + `/api/bukaan-lahan/produksi`,data).then(res=>{
                        resolve();
                    }).catch(err=>{
                        resolve();
                    });
                })
            },
            async getData(){
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/produksi?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}&bl_type_id=${this.typeProduksi}`);
                let data2 = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/geojson?periode=${this.periode}&bl_type_id=${this.typeProduksi}`);
                this.datanya=data.data;
                this.geojson=data2.data;
                this.datanya.forEach(e=>{
                    this.vdata[`${e.type_blok}`]=e.data_produksi;
                })
                this.$forceUpdate();
            },
            async getOther(){
                let data = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/blok`);
                let data2 = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/type`);
                let data3 = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/join/form`);
                this.bl_blok=data.data;
                this.bl_type=data2.data;
                this.bl_form=data3.data;
            },
            format(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY-MM-DD"
                );
            },
            hari(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "DD"
                );
            },
            bulans(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "MM"
                );
            },
            tahuns(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY"
                );
            },
        },
        components: {
            vSelect:VueSelect.VueSelect
        },
        mounted() {
            this.getOther();
            this.bulan=this.bulans(new Date())
            this.tahun=parseInt(this.tahuns(new Date()))
            var min = this.tahun - 9
            var years = []
            for (var i = this.tahun; i >= min; i--) {
                years.push(i)
            }
            this.listTahun=years
            document.getElementById('bukaanLahanProduksi').classList.remove('d-none');
        },
    })
</script>

<?= $this->endSection() ?>
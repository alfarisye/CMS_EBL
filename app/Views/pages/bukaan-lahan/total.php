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
    <div id="bukaanLahanTotalProduksi" class="d-none">

        <div class="pagetitle">
            <h1>Bukaan Lahan - Total Produksi</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Bukaan Lahan</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("bukaan-lahan/total/produksi") ?>">Total Produksi</a></li>
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
                            <div >
                                <div class="row py-2" >
                                    <div class="col-sm-3">
                                        <p class="font-semibold">Years :</p>
                                        <div class="sm-form ">
                                        <select class='form-control' v-model="tahun"  @change="page=1;getForm()">
                                                <option v-for="(item, index) in listTahun" :key="index+'listTahun'">{{item}}</option>
                                            </select>
                                            <!-- <input type="text" id="tahun" name="tahun" class="form-control p-1 rounded-sm shadow-sm" placeholder="tahun" v-model="tahun" @change="page=1;getForm()"> -->
                                            <!-- <input type="date" id="periode" name="periode" class="form-control p-1 rounded-sm shadow-sm" placeholder="periode" v-model="periode" @change="page=1;getForm()"> -->
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <p class="font-semibold">Months :</p>
                                        <div class="sm-form ">
                                            <select class='form-control' v-model="bulan" @change="getForm()">
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
                                        <div class="sm-form mt-10">
                                            <input type="text" 
                                            @change="page=1"
                                            id="search" name="search" class="form-control p-2 text-xs rounded-lg " placeholder="search" v-model="search" >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <br>
                                        <div class="text-center p-3" >
                                            <a v-if="datanya.length>0" :href="'<?= site_url("/production/report/download?nama_file=production report") ?>'+`&data=${JSON.stringify(td)}`">
                                                <button type="button"  class="btn btn-sm btn-dark  " ><i class="ri-download-line"></i></button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <!-- Table users -->
                                    <table class="table table-bordered ">
                                            <tr>
                                                <th scope="col" v-for="(item, index) in keys" :key="index+'keys'">
                                                    {{item}} &#8593;&#8595;
                                                </th>
                                            </tr>
                                            <tbody v-if="datanya.length>0">
                                                <tr v-for="(item, index) in td" :key="index">
                                                    <td v-for="(item2, index2) in keys" :key="index+'keys2'">
                                                        {{item[item2]}} <span v-if="item2!='Periode'" class="font-semibold text-lg">Ha</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                    </table>
                                    <div class="text-right">
                                        <!-- BULLET  -->
                                        <button type="button" class="btn btn-sm  rounded-circle py-1 px-2 mr-2" 
                                        :class="page==index+1?'btn-dark':'btn-dark-outline'"
                                        v-for="(item, index) in totalPage" :key="index+'totalPage'" @click="page=index+1">{{index+1}}</button>
                                    </div>
                                    <!-- End Table with stripped rows -->
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
        el:"#bukaanLahanTotalProduksi",
        data(){
            return{
                typedata:'timesheet',
                perPage:10000,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                modal:false,
                // CUSTOM
                listTahun:[],
                datanya:[],
                keys:[],
                vdata:{},
                tahun:'',
                bulan:'',
                dari_tanggal:'',
                sampai_tanggal:'',
                formTable:[],
                formInsert:false,
                option:{
                    headers:{
                        'X-Requested-With': 'XMLHttpRequest',
                        'contentType': "application/json",
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
                this.totalPage = Math.ceil(data.length/this.perPage);
                data=data.slice((this.page-1)*this.perPage,this.page*this.perPage);
                console.log('data',data)
                if(data[0]['Periode']=='Invalid Date'){
                    data=[];
                }
                return data;
            }
        },  
        methods: {
            async getForm(){
                let that=this;
                if(this.bulan && this.tahun){
                    this.periode=`${this.tahun}-${this.bulan}-${this.hari(new Date())}`;
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
            showField(vmodel,e){
                this.modal=true;
                this.target.vmodel=vmodel;
                this.target.key=e.target.name;
                this.$forceUpdate();
            },
            async getData(){
                let that=this;
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/produksi-total?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                console.log('data',data)
                let type = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/type`);
                type=type.data;
                this.datanya=data.data;
                let periode =data.data.map(e=>e.periode);
                periode= [... new Set(periode)];
                periode=[periode[0]]
                console.log(periode)
                console.log(type)
                let datas = periode.map(e=>{
                    let obj={};
                    obj.Periode=that.formatIndo(e);
                    type.forEach(t=>{
                        let filter=data.data.filter(e=>e.bl_type_id==t.id)
                        console.log('filter',t,filter)
                        // obj['id_bl_type']=t.id_bl_type
                        obj[t.nama_type]=filter.reduce((e,n)=>{return parseFloat(e)+parseFloat(n.data_produksi)},0)
                    })
                    return obj;
                })
                console.log(datas)
                this.datanya=datas;
                this.keys=Object.keys(datas[0]);
                this.formInsert=false;
                this.$forceUpdate();
             
                setTimeout(() => {
                    this.sortField();
                }, 1000);
            },
            sortField(){
                const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
                const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
                    v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
                    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
                // do the work...
                document.querySelectorAll('th').forEach(th => {
                    th.style.cursor="pointer";
                    th.addEventListener('click', (() => {
                    const table = th.closest('table');
                    Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
                        .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                        .forEach(tr => table.appendChild(tr) );
                }))});
            },
            format(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY-MM-DD"
                );
            },
            formatIndo(tgl){
                return dateFns.format(
                    new Date(tgl),
                    "DD MMMM YYYY"
                );
            },
            tahuns(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY"
                );
            },
            bulans(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "MM"
                );
            },
            hari(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "DD"
                );
            },
           
        },
        mounted() {
            this.bulan=this.bulans(new Date());
            this.tahun=parseInt(this.tahuns(new Date()));
            var min = this.tahun - 9
            var years = []
            for (var i = this.tahun; i >= min; i--) {
                years.push(i)
            }
            this.listTahun=years
            this.getForm();
            document.getElementById('bukaanLahanTotalProduksi').classList.remove('d-none');
        },
    })
</script>

<?= $this->endSection() ?>
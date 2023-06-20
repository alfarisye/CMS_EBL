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
    <div id="glogspage" class="d-none">

        <div class="pagetitle">
            <h1>General Logs</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url("general/logs") ?>">General Logs</a></li>
                </ol>
            </nav>
        </div>
        <!-- MODAL -->
        <div v-if="modal" @click="modal=false" class="modal1"></div>
        <div v-if="modal" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn">
                <p class="float-right mr-3">Actor : 
                    <span class="font-semibold text-lg">{{target.pilih.created_by??target.pilih.updated_by??target.pilih.deleted_by}}</span>
                </p>
                <p class="ml-2">Table : 
                    <span class="font-semibold">{{target.pilih.table}}</span>
                </p>
                <p class="ml-2">Action : 
                    <span class="font-semibold">{{target.pilih.action}}</span>
                </p>
                <div v-if="target.pilih.data_before" style="max-width:50vw;">
                    <p class="font-semibold">Data Before</p>
                    <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                            <tr>
                                <th v-for="(item, index) in Object.keys(JSON.parse(target.pilih.data_before))" :key="index+'keytabl'">
                                    {{item}}
                                </th>
                            </tr>
                            <tr >
                                <td v-for="(item, index) in JSON.parse(target.pilih.data_before)" :key="index+'keytabl'">
                                    {{item}}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <hr>
                </div>
                <div v-if="target.pilih.data_after" style="max-width:50vw;">
                 <p class="font-semibold">Data After</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th v-for="(item, index) in Object.keys(JSON.parse(target.pilih.data_after))" :key="index+'keytabl'">
                                    {{item}}
                                </th>
                            </tr>
                            <tr >
                                <td v-for="(item, index) in JSON.parse(target.pilih.data_after)" :key="index+'keytabl'">
                                    {{item}}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
            <!-- MODAL -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" >Data Logs</h5>
                    <div class="row py-2" >
                    <div class="col-sm-3">
                        <p class="font-semibold">Table</p>
                        <select class='form-control' v-model="table" @change="getData">
                            <option v-for="(item, index) in listTable" :key="index+'listtable'">{{item}}</option>
                            <option value="100000">Semua</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <p class="font-semibold">Action</p>
                        <select class='form-control' v-model="action" @change="getData">
                            <option value="">Semua</option>
                            <option>insert</option>
                            <option>update</option>
                            <option>delete</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <p class="font-semibold">View</p>
                        <select class='form-control' v-model="perPage" @change="page=1">
                            <option>5</option>
                            <option>10</option>
                            <option>50</option>
                            <option>100</option>
                            <option value="100000">Semua</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-right">
                            <a v-if="datanya.length>0" :href="'<?= site_url("/production/report/download?nama_file=production report") ?>'+`&data=${JSON.stringify(td)}`">
                                <button type="button"  class="btn btn-sm btn-dark  " ><i class="ri-download-line"></i></button>
                            </a>
                        </div>
                        <div class="sm-form mt-1">
                            <input type="text" 
                            @change="page=1"
                            id="search" name="search" class="form-control p-2 text-xs rounded-lg " placeholder="search" v-model="search" >
                        </div>
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-bordered ">
                        <tr>
                            <th scope="col">
                                No &#8593;&#8595;
                            </th>
                            <th scope="col">
                                Table &#8593;&#8595;
                            </th>
                            <th scope="col">
                                Action &#8593;&#8595;
                            </th>
                            <th scope="col">
                                Data &#8593;&#8595;
                            </th>
                            <th scope="col">
                                Created at &#8593;&#8595;
                            </th>
                            <th scope="col">
                                Updated At &#8593;&#8595;
                            </th>
                            <th scope="col">
                                Deleted at &#8593;&#8595;
                            </th>
                            <th scope="col">
                                Created by &#8593;&#8595;
                            </th>
                            <th scope="col">
                                Updated by &#8593;&#8595;
                            </th>
                            <th scope="col">
                                Deleted by &#8593;&#8595;
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index" >
                                <td>{{index+1}}</td>
                                <td>
                                    {{item.table}}
                                </td>
                                <td>
                                    {{item.action}}
                                </td>
                                <td>
                                     <button type="button"  class="btn btn-sm btn-dark btn-block " @click="modal=true;target.pilih=item;$forceUpdate()">
                                        Show
                                    </button>
                                </td>
                                <td class="text-xs">
                                    {{item.created_at}}
                                </td>
                                <td class="text-xs">
                                    {{item.updated_at}}
                                </td>
                                <td class="text-xs">
                                    {{item.deleted_at}}
                                </td>
                                <td class="">
                                    {{item.created_by}}
                                </td>
                                <td class="">
                                    {{item.updated_by}}
                                </td>
                                <td class="">
                                    {{item.deleted_by}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-right">
                        <button type="button" class="btn btn-sm  rounded-circle py-1 px-2 mr-2" 
                        :class="page==1?'btn-dark':'btn-dark-outline'" @click="page=1"><</button>
                        <button type="button" class="btn btn-sm  rounded-circle py-1 px-2 mr-2" 
                        :class="page==index+1?'btn-dark':'btn-dark-outline'"
                        v-for="(item, index) in totalPage" :key="index+'totalPage'" 
                        v-if="item<page+3 && item>page-3"
                        @click="page=index+1">{{index+1}}</button>
                        <button type="button" class="btn btn-sm  rounded-circle py-1 px-2 mr-2" 
                        :class="page==totalPage?'btn-dark':'btn-dark-outline'" @click="page=totalPage">></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="pageloading" style="height:80vh;" class="d-flex justify-content-center align-items-center text-center animate__animated animate__bounce animate__infinite text-2xl font-times">
            Loading ...
    </div>
</main><!-- End #main -->

<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    new Vue({
        el:"#glogspage",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                disableInput:[],
                sortTable:[null,'table','action',null,'created_at','updated_at','deleted_at','created_by','updated_by','deleted_by'], // disusun berdasarkan urutan td td td
                listTable:['bl_blok','bl_type','bl_blok_form','bl_produksi','quality_report','bl_geojson'],
                action:'',
                table:'',
                modal:false,
                showInsert:false,
                // CUSTOM
                datanya:[],
                vdata:{},
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
                data=data.filter(e=>e.action.indexOf(this.action)!=-1)
                this.totalPage = Math.ceil(data.length/this.perPage);
                data=data.slice((this.page-1)*this.perPage,this.page*this.perPage);
                return data;
            }
        },  
        methods: {
            showField(vmodel,e){
                this.modal=true;
                this.target.vmodel=vmodel;
                this.target.key=e.target.name;
                this.$forceUpdate();
            },
            async getData(){
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/logs?table=${this.table}`);
                this.datanya=data.data;
                console.log(data)
                this.showInsert=false;
                this.$forceUpdate();
                setTimeout(() => {
                    this.sortField();
                }, 1000);
            },
            sortField(){
                let that=this
                document.querySelectorAll('table tr th').forEach((e,i)=>{
                    e.style.cursor='pointer';
                    e.addEventListener('click',()=>{
                        that.sortTable[1000]=!that.sortTable[1000];
                        if(!that.sortTable[1000]){
                            that.datanya=that.datanya.sort((a,b) => (a[that.sortTable[i]] > b[that.sortTable[i]]) ? 1 : ((b[that.sortTable[i]] > a[that.sortTable[i]]) ? -1 : 0))
                        }else{
                            that.datanya=that.datanya.sort((a,b) => (a[that.sortTable[i]] < b[that.sortTable[i]]) ? 1 : ((b[that.sortTable[i]] < a[that.sortTable[i]]) ? -1 : 0))
                        }
                        that.$forceUpdate();
                    })
                })
            },
            format(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY-MM-DD"
                );
            },
           
        },
        mounted() {
            this.table=this.listTable[0]
            this.getData();
            document.getElementById('glogspage').classList.remove('d-none');
            document.getElementById('pageloading').remove()
        },
    })
</script>

<?= $this->endSection() ?>
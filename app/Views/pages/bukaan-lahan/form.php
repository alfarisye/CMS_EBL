<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->
<link rel="stylesheet" href="https://unpkg.com/vue-select@3.0.0/dist/vue-select.css">
<!-- SCRIPT -->
<script src="https://unpkg.com/vue-select@3.0.0"></script>


<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .border-hover{border:1px solid transparent}
    .border-hover:hover{border:1px solid #d0d0d0}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:100;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:105;min-width:50vw}
</style>
<main id="main" class="main">
    <div id="bukaanLahanForm" class="d-none">
        <div class="pagetitle">
            <h1>Bukaan Lahan - Tambah Data Blok</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Bukaan Lahan</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("bukaan-lahan/blok") ?>">Tambah Data Blok</a></li>
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
                            <h5 class="card-title" >Blok Status</h5>
                            <div class="row">
                                <div class="col-sm-2">
                                    <a href="<?= site_url('bukaan-lahan/blok?page=blok') ?>">
                                        <button type="button" class="btn btn-sm btn-block" :class="pages=='blok'?'btn-dark':'btn-outline-dark'">Tambah Blok</button>
                                    </a>
                                </div>
                                <div class="col-sm-2">
                                    <a href="<?= site_url('bukaan-lahan/type?page=kategori') ?>">
                                        <button type="button" class="btn btn-sm btn-block" :class="pages=='kategori'?'btn-dark':'btn-outline-dark'">Tambah Kategori</button>
                                    </a>
                                </div>
                                <div class="col-sm-2">
                                    <a href="<?= site_url('bukaan-lahan/form?page=status') ?>">
                                        <button type="button" class="btn btn-sm btn-block" :class="pages=='status'?'btn-dark':'btn-outline-dark'">Blok Status</button>
                                    </a>
                                </div>
                                <div class="offset-sm-4 col-sm-2">
                                    <a href="<?= site_url('general/logs?table=bl_blok_form') ?>">
                                        <button type="button" class="btn btn-sm btn-block btn-style2" >Logs Input</button>
                                    </a>
                                </div>
                            </div>
                            <!-- notification -->
                            <!-- b-table -->
                            <!-- <div class="p-2  shadow-sm rounded">
                                <form action="" @submit.prevent="">
                                    <div class="sm-form ">
                                        <label for="nama_form">Your nama_form</label>
                                        <input type="text" id="nama_form" name="nama_form" class="form-control p-2 rounded-lg shadow" placeholder="nama_form" v-model="vdata['nama_form']" >
                                    </div>
                                    <select class='form-control' v-model="vdata.status">
                                        <option value="true">Aktif</option>
                                        <option value="false">Tidak Aktif</option>
                                    </select>
                                </form>
                            </div> -->
                            <div>
                                <div class="row py-2" >
                                    <div class="col-sm-3">
                                        <p class="font-semibold">Type Produksi</p>
                                        <select class='form-control' v-model="typeProduksi" @change="page=1">
                                            <option v-for="(item, index) in bl_type" :key="index+'bl_type3'" :value="item.id">{{item.nama_type}}</option>
                                            <option value="">Semua</option>
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
                                        <div class="sm-form mt-10">
                                            <input type="text" 
                                            @change="page=1"
                                            id="search" name="search" class="form-control p-2 text-xs rounded-lg " placeholder="search" v-model="search" >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <br>
                                        <div class="text-center p-3" >
                                            <!-- <a v-if="datanya.length>0" :href="'<?= site_url("/production/report/download?nama_file=production report") ?>'+`&data=${JSON.stringify(td)}`">
                                                <button type="button"  class="btn btn-sm btn-dark  " ><i class="ri-download-line"></i></button>
                                            </a> -->
                                            <button type="button"  class="btn btn-sm btn-style2  ml-3 my-1" @click="formInsertCek()">Insert Data +</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive" style="min-height:200px;">
                                    <!-- Table users -->
                                    <table class="table table-bordered ">
                                            <tr>
                                                <th scope="col">
                                                    No &#8593;&#8595;
                                                </th>
                                                <th scope="col">
                                                    ID Form &#8593;&#8595;
                                                </th>
                                                <th scope="col">
                                                    Type &#8593;&#8595;
                                                </th>
                                                <th scope="col">
                                                    Blok &#8593;&#8595;
                                                </th>
                                                <th scope="col">
                                                    Nama Form &#8593;&#8595;
                                                </th>
                                                <!-- <th scope="col">
                                                    Kategory Inputan &#8593;&#8595;
                                                </th> -->
                                                <th scope="col">
                                                    Status &#8593;&#8595;
                                                </th>
                                                <th scope="col">
                                                    Aksi 
                                                </th>
                                            </tr>
                                            <tr v-if="formInsert">
                                                <td><button type="button" class="btn btn-sm btn-dark btn-block" @click="formInsert=false">-</button></td>
                                                <td>
                                                    <textarea disabled type="text"  id="id_form" name="id_form" rows="1" placeholder="id form..." class="" v-model="vdata.id" ></textarea>
                                                </td>
                                                <td>
                                                    <v-select :options="bl_type" label="nama_type" v-model="vdata.bl_type_id" :reduce="e => e.id"></v-select>
                                                </td>
                                                <td>
                                                    <v-select :options="bl_blok" label="nama_blok" v-model="vdata.blok_id" :reduce="e => e.id"></v-select>
                                                </td>
                                                <td>
                                                    <textarea required type="text"  id="nama_form" name="nama_form" rows="1" placeholder="nama_form..." class="" v-model="vdata.nama_form"  ></textarea>
                                                </td>
                                                <!-- <td>
                                                    <select class='form-control' v-model="vdata['kategory_inputan']">
                                                        <option>Daily</option>
                                                        <option>Monthly</option>
                                                    </select>
                                                </td> -->
                                                <td>
                                                    <select required class='form-control ' v-model="vdata.status">
                                                        <option value="true">Aktif</option>
                                                        <option value="false">Tidak Aktif</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success" @click="insertData">+ Tambah</button>
                                                </td>
                                            </tr>
                                            <tbody v-if="datanya.length>0">
                                                <tr v-for="(item, index) in td" :key="index">
                                                    <td>{{index+1}}</td>
                                                    <td>
                                                        <textarea type="text" disabled id="deskripsi" name="id" rows="1" placeholder="id..." class="" v-model="td[index].id" ></textarea>
                                                    </td>
                                                    <td>
                                                        <select  class='form-control border-hover' :disabled="!showAksi[index]" v-model="td[index].bl_type_id">
                                                            <option v-for="(item, index) in bl_type" :key="index+'bl_type2'" :value="item.id">{{item.nama_type}}</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select required class='form-control border-hover' :disabled="!showAksi[index]" v-model="td[index].blok_id">
                                                            <option v-for="(item, index) in bl_blok" :key="index+'bl_blok2'" :value="item.id">{{item.nama_blok}}</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <textarea required type="text" id="nama_form" :disabled="!showAksi[index]" name="nama_form" rows="1" placeholder="nama_form..." class="" v-model="td[index].nama_form" @dblclick="showField(td[index],$event)"></textarea>
                                                    </td>
                                                    <!-- <td>
                                                        <select class='form-control' v-model="td[index].kategory_inputan">
                                                            <option>Daily</option>
                                                            <option>Monthly</option>
                                                        </select>
                                                    </td> -->
                                                    <td>
                                                        <select required class='form-control border-hover' :disabled="!showAksi[index]" v-model="td[index].status">
                                                            <option value="true">Aktif</option>
                                                            <option value="false">Tidak Aktif</option>
                                                        </select>
                                                    </td>
                                                <td>
                                                    <div v-if="showAksi[index]">
                                                        <button type="button" @click="updateData(td[index])" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                                            <span class="tipstextB">Edit</span>
                                                        </button>
                                                        <button type="button" @click="deleteData(td[index])" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                                            <span class="tipstextB">Delete</span>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-dark text-xs ml-1" @click="showAksi[index]=!showAksi[index];$forceUpdate()">&#9779;</button>
                                                    </div>
                                                    <div v-else>
                                                        <button type="button" class="btn btn-sm btn-dark text-xs" @click="showAksi[index]=!showAksi[index];formInsert=false;$forceUpdate()">Edit &#9779;</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="text-right">
                                        <!-- BULLET  -->
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
                                    <!-- End Table with stripped rows -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div id="pageloading" style="height:80vh;" class="d-flex justify-content-center align-items-center text-center animate__animated animate__bounce animate__infinite text-2xl font-times">
            Loading ...
    </div>
</main><!-- End #main -->

<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    var pages="<?= $_GET['page'] ?>"
    new Vue({
        el:"#bukaanLahanForm",
        data(){
            return{
                pages,
                showAksi:[],
                sortTable:[null,'id','bl_type_id','blok_id','deskripsi','status'], // disusun berdasarkan urutan td td td
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                modal:false,
                // CUSTOM
                datanya:[],
                bl_blok:[],
                bl_type:[],
                vdata:{},
                formTable:[],
                typeProduksi:'',
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
            validateRequired(){ // validation manual select semua input required
                let validation=true;
                document.querySelectorAll('[required]').forEach(e=>{e.reportValidity()?'':validation=false});
                return validation;
            },
            async insertData(){
                if(!this.validateRequired())return;
                if(!this.vdata.bl_type_id || !this.vdata.blok_id) return alert('Field "Type" dan "Blok" tidak boleh kosong!') ;
                sdb.loadingOn();
                this.vdata['<?= csrf_token() ?>']='<?= csrf_hash() ?>';
                console.log(this.datanya)
                console.log('vdata',this.vdata)
                // if(this.datanya.filter(e=>e.bl_type_id==this.vdata.bl_type_id && e.blok_id==this.vdata.blok_id && e.kategory_inputan==this.vdata.kategory_inputan).length>0){
                if(this.datanya.filter(e=>e.bl_type_id==this.vdata.bl_type_id && e.blok_id==this.vdata.blok_id ).length>0){
                    sdb.loadingOff();
                    sdb.alert('Form ini sudah ada!');
                }else{
                    axios.post("<?= site_url() ?>" + `/api/bukaan-lahan/form`,this.vdata).then(res=>{
                        sdb.loadingOff();
                        sdb.alert('Insert data berhasil!','bg-green-400');
                        this.vdata={}
                        // window.location.reload();
                        this.getData();
                    }).catch(err=>{
                        sdb.loadingOff();
                        sdb.alert('Insert data gagal!');
                    });  
                }
            },
            async updateData(data){
                if(!this.validateRequired())return;
                sdb.loadingOn();
                let update=false;
                axios.put("<?= site_url() ?>" + `/api/bukaan-lahan/form/${data.id}`,data).then(res=>{
                    sdb.loadingOff();
                    sdb.alert('Update data berhasil!','bg-green-400');
                    // this.getData();
                    console.log(res)
                }).catch(err=>{
                    sdb.loadingOff();
                    sdb.alert('Data Type dan Blok ini sudah ada ! Update data gagal!');
                });   
            },
            async deleteData(data){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                sdb.loadingOn();
                this.vdata['<?= csrf_token() ?>']='<?= csrf_hash() ?>';
                axios.delete("<?= site_url() ?>" + `/api/bukaan-lahan/form/${data.id}`).then(res=>{
                    sdb.loadingOff();
                    sdb.alert('Delete data berhasil!','bg-green-400');
                    this.getData();
                    console.log(res)
                }).catch(err=>{
                    sdb.loadingOff();
                    sdb.alert('Delete data gagal!');
                });  
            },
            async getData(){
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/form`);
                this.datanya=data.data;
                this.formInsert=false;
                this.$forceUpdate();
             
                setTimeout(() => {
                    this.sortField();
                }, 1000);
            },
            async getOther(){
                let data = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/blok`);
                let data2 = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/type`);
                this.bl_blok=data.data;
                this.bl_type=data2.data;
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
        components: {
            vSelect:VueSelect.VueSelect
        },
        mounted() {
            this.getOther();
            this.getData();
            document.getElementById('bukaanLahanForm').classList.remove('d-none');
            document.getElementById('pageloading').remove()
        },
    })
</script>

<?= $this->endSection() ?>
<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->


<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .border-hover{border:1px solid transparent}
    .border-hover:hover{border:1px solid #d0d0d0}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:1000;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:1005;min-width:50vw}
</style>
<main id="main" class="main">
    <div id="product-material" class="d-none">
        <div class="pagetitle">
            <h1>Product Material</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Sales</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("sales/product-material") ?>">Product Material</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn">
               <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <table>
                    <tr>
                        <td class="p-2">Sequence</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required :disabled="!showInsert" type="text" id="sequence" name="sequence" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="Sequence ..." v-model="vdata['sequence']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Activtiy</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required type="text" id="activity" name="activity" class="form-control p-1  rounded-sm shadow-sm" placeholder="Activity ..." v-model="vdata['activity']" >
                        </td>
                    </tr>
                </table>
                <hr class="my-4">
                <div class="text-right">
                    <button type="button" @click="modals=false"  class="btn btn-sm btn-warning ml-2 ">Cancel</button>
                    <button type="submit"  class="btn btn-sm btn-success ml-2 ">{{showInsert?'Save Data':'Update Data'}}</button>
                </div>
               </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" >Product Material</h5>
                <div class="row py-2" >
                    <div class="col-sm-3">
                        <select class='form-control' v-model="perPage" @change="page=1">
                            <option>5</option>
                            <option>10</option>
                            <option>50</option>
                            <option>100</option>
                            <option value="100000">Semua</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                            <input type="text" 
                            @change="page=1"
                            id="search" name="search" class="form-control p-2 text-xs rounded-lg " placeholder="search" v-model="search" >
                    </div>
                    <div class="col-sm-3">
                        <div class="text-center " >
                            <!-- <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={}">+ New Master</button> -->
                        </div>
                    </div>
                    <div class="col-sm-3">
                         <form v-if="datanya.length>0" action="<?= site_url() ?>/production/report/download?nama_file=product material" method="post">
                                <textarea :value="JSON.stringify(td)" v-show="false" type="text" id="datanya" name="datanya" rows="2" placeholder="datanya..." class="form-control md-textarea"  ></textarea>
                                <button type="submit" class="btn btn-sm btn-dark text-xs ">Excel <i class="ri-download-line"></i></button>
                            </form>
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-bordered ">
                        <tr>
                            <th style="background:lightgreen;" scope="col">
                                Material Type &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" scope="col">
                                Material Number &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" scope="col">
                                Material Description &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" scope="col ">
                                UoM &#8593;&#8595;
                            </th>
                            <!-- <th style="background:lightgreen;" scope="col">
                                Action 
                            </th> -->
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index+'form'" >
                                <td>
                                    {{item.MTART}}
                                </td>
                                <td>
                                    {{item.MATNR}}
                                </td>
                                <td>
                                    {{item.MAKTX}}
                                </td>
                                <td>
                                    {{item.MEINS}}
                                </td>
                                <!-- <td>
                                    <div v-if="disableInput[index]">
                                        <button type="button" @click="showUpdate(item)" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                            <span class="tipstextB">Edit</span>
                                        </button>
                                        <button type="button" @click="deleteData(item)" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                            <span class="tipstextB">Delete</span>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark text-xs ml-1" @click="disableInput[index]=!disableInput[index];$forceUpdate()">&#9779;</button>
                                    </div>
                                    <div v-else>
                                        <button type="button" class="btn btn-sm btn-dark text-xs" @click="disableInput[index]=!disableInput[index];showInsert=false;$forceUpdate()">Edit &#9779;</button>
                                    </div>
                                </td> -->
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
    var pages="<?= @$_GET['page'] ?>"
    new Vue({
        el:"#product-material",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                disableInput:[],
                sortTable:[null,'sequence','activity','status'], // disusun berdasarkan urutan td td td
                modals:false,
                showInsert:false,
                // CUSTOM
                datanya:[],
                vdata:{},
                pages,
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
                return data;
            }
        },  
        methods: {
            validateRequired(){ // validation manual select semua input required
                let validation=true;
                document.querySelectorAll('[required]').forEach(e=>{e.reportValidity()?'':validation=false});
                return validation;
            },
            async insertData(){
                if(!this.validateRequired())return;
                sdb.loadingOn();
                this.vdata.status='';
                let token=await axios.post("<?= site_url() ?>" + `/api/test`);
                this.vdata[Object.keys(token.data[0])[0]]=Object.values(token.data[0])[0];
                axios.post("<?= site_url() ?>" + `/api/product-material`,this.vdata).then(res=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert('Insert data berhasil!','bg-green-400');
                    this.vdata={}
                    this.getData();
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert(err.response.data.message??'Insert data gagal');
                });  
            },
            async updateData(){
                if(!this.validateRequired())return;
                sdb.loadingOn();
                let id=this.vdata.id;
                delete this.vdata.id;
                delete this.vdata.sequence;
                axios.put("<?= site_url() ?>" + `/api/product-material/${id}`,this.vdata).then(res=>{
                    sdb.loadingOff();
                    this.modals=false;
                    this.getData()
                    sdb.alert('Update data berhasil!','bg-green-400');
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    this.getData()
                    sdb.alert(err.response.data.message??'Update data gagal');
                });   
            },
            async deleteData(data){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                sdb.loadingOn();
                axios.delete("<?= site_url() ?>" + `/api/product-material/${data.id}`).then(res=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert('Delete data berhasil!','bg-green-400');
                    this.getData();
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert('Delete data gagal!');
                });  
            },
            showUpdate(item){
                this.vdata=item;
                this.showInsert=false;
                this.modals=true;
                this.$forceUpdate();
            },
            async getData(){
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/get/product-material`);
                this.datanya=data.data;
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
            this.getData();
            document.getElementById('product-material').classList.remove('d-none');
            document.getElementById('pageloading').remove()
            this.$forceUpdate();
        },
    })
</script>
<?= $this->endSection() ?>
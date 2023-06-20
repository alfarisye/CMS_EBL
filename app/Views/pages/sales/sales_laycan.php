<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/vue-select@3.0.0/dist/vue-select.css">
<!-- SCRIPT -->
<script src="https://unpkg.com/vue-select@3.0.0"></script>


<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .border-hover{border:1px solid transparent}
    .border-hover:hover{border:1px solid #d0d0d0}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:10000;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:10005;min-width:50vw}
</style>
<main id="main" class="main">
    <div id="sales-laycan" class="d-none">
        <div class="pagetitle">
            <h1>Monitoring Shipment</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Sales</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("sales/sales-laycan") ?>">Monitoring Shipment</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn" style="height:80vh;overflow:scroll;">
               <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <table class="table table-sm ">
                    <tr>
                        <td colspan="3">
                            <div class="row">
                                <div class="col-3">
                                    <div style="height:100px;" class="d-flex justify-content-center align-items-center ">
                                        <div style="width:100%">
                                            <p class="text-center font-bold text-lg">Monitoring Shipment</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-9">
                                    <table>
                                        <tr>
                                            <td>
                                                Shipment 
                                            </td>
                                            <td class="py-2">
                                                :
                                            </td>
                                            <td>
                                                <v-select  :options="shipment" label="shipment_id" v-model="vdata.shipment_id" :reduce="e => e.shipment_id"></v-select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Contract No
                                            </td>
                                            <td class="py-2">
                                                :
                                            </td>
                                            <td>
                                                <input id type="text" id="contract_no" name="contract_no"  class="form-control p-1  rounded-sm shadow-sm" placeholder="contract_no ..." v-model="vdata['contract_no']" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Upload File
                                            </td>
                                            <td class="py-2">
                                                :
                                            </td>
                                            <td>
                                                <div class="my-2" v-if="vdata.file_laycan">
                                                    <button type="button" class="btn btn-sm btn-dark  text-xs" @click="dowloadpdf(vdata)">
                                                        Download
                                                    </button>
                                                </div>
                                                <input  type="file" id="filenya" name="filenya" class="form-control p-1  rounded-sm shadow-sm" placeholder="filenya ..."  >
                                                <button type="button" class="btn btn-m btn-warning text-xs " @click="updateData">Upload</button>
                                            </td>
                                        </tr>
                                    </table>
                                   
                                </div>
                                <div class="col-12">
                                     <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <tr>
                                                <th>Activity</th>
                                                <th>Draft</th>
                                                <th>Issue</th>
                                                <th>Received</th>
                                                <th>Status</th>
                                            </tr>
                                            <tr v-for="(item, index) in master_activity" :key="index+'master_activity'">
                                                <td>{{item.activity}}</td>
                                                <td>
                                                    <div v-if="laycan.length>0 && laycan.map(e=>e.item_activity).includes(item.activity)">
                                                        <div v-for="(item2, index2) in laycan" v-if="item2.item_activity==item.activity"  :key="index2+'laycan'">
                                                            <button v-if="item2.item_activity==item.activity && (item2.status=='0' || item2.status=='1' || item2.status=='2')" type="button" name="" id="" class="btn btn-sm btn-success rounded-circle">
                                                                <i class="ri-check-line"></i>
                                                            </button>
                                                            <button @click="updateActivtiy(item,0,item2)" v-else type="button" name="" id="" class="btn btn-sm btn-outline-success rounded-circle">
                                                                <i class="ri-check-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div v-else>
                                                        <button @click="updateActivtiy(item,0)" type="button" name="" id="" class="btn btn-sm btn-outline-success rounded-circle">
                                                            <i class="ri-check-line"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div v-if="laycan.length>0 && laycan.map(e=>e.item_activity).includes(item.activity)">
                                                        <div v-for="(item2, index2) in laycan" v-if="item2.item_activity==item.activity" :key="index2+'laycan'">
                                                            <button v-if="item2.item_activity==item.activity && (item2.status=='1' || item2.status=='2')" type="button" name="" id="" class="btn btn-sm btn-success rounded-circle">
                                                                <i class="ri-check-line"></i>
                                                            </button>
                                                            <button @click="updateActivtiy(item,1,item2)" v-else type="button" name="" id="" class="btn btn-sm btn-outline-success rounded-circle">
                                                                <i class="ri-check-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div v-else>
                                                        <button @click="updateActivtiy(item,1)" type="button" name="" id="" class="btn btn-sm btn-outline-success rounded-circle">
                                                            <i class="ri-check-line"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div v-if="laycan.length>0 && laycan.map(e=>e.item_activity).includes(item.activity)">
                                                        <div v-for="(item2, index2) in laycan" v-if="item2.item_activity==item.activity" :key="index2+'laycan'">
                                                            <button v-if="item2.item_activity==item.activity && item2.status=='2'" type="button" name="" id="" class="btn btn-sm btn-success rounded-circle">
                                                                <i class="ri-check-line"></i>
                                                            </button>
                                                            <button @click="updateActivtiy(item,2,item2)" v-else type="button" name="" id="" class="btn btn-sm btn-outline-success rounded-circle">
                                                                <i class="ri-check-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div v-else>
                                                        <button  @click="updateActivtiy(item,2)" type="button" name="" id="" class="btn btn-sm btn-outline-success rounded-circle">
                                                            <i class="ri-check-line"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                     <div v-if="laycan.length>0 && laycan.map(e=>e.item_activity).includes(item.activity)">
                                                        <div v-for="(item2, index2) in laycan" v-if="item2.item_activity==item.activity" :key="index2+'laycan'">
                                                            <p v-if="item2.item_activity==item.activity && item2.status=='0'">
                                                                Waiting Issue
                                                            </p>
                                                            <p v-else-if="item2.item_activity==item.activity && item2.status=='1'">
                                                                 Waiting Reveived
                                                            </p>
                                                            <p v-else-if="item2.item_activity==item.activity && item2.status=='2'">
                                                                Completed
                                                            </p>
                                                            <p v-else>
                                                            </p>
                                                        </div>
                                                        <div v-else>
                                                                <!-- Waiting Draft -->
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <hr class="my-4">
                <div class="text-right">
                    <!-- <button type="button" @click="modals=false"  class="btn btn-sm btn-warning ml-2 ">Cancel</button>
                    <button type="submit"  class="btn btn-sm btn-success ml-2 ">{{showInsert?'Save Data':'Update Data'}}</button> -->
                </div>
               </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" >Monitoring Shipment</h5>
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
                            <select class='form-control' v-model="statusnya">
                                <option value="">All</option>
                                <option value="2">Fullfilled</option>
                                <option value="1">In Progress</option>
                            </select>
                            <!-- <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={}">+ New Data</button> -->
                        </div>
                    </div>
                    <!-- <div class="col-sm-3">
                         <form v-if="datanya.length>0" action="<?= site_url() ?>/production/report/download?nama_file=sales shipment" method="post">
                                <textarea :value="JSON.stringify(td)" v-show="false" type="text" id="datanya" name="datanya" rows="2" placeholder="datanya..." class="form-control md-textarea"  ></textarea>
                                <button type="submit" class="btn btn-sm btn-dark text-xs ">Excel <i class="ri-download-line"></i></button>
                            </form>
                    </div> -->
                </div>
                <div class="table-responsive" >
                    <table class="table table-bordered ">
                        <tr>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                ID &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Shipment &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Contract &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Total Days &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Status &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Action 
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index+'form'" >
                                <td class="text-xs">
                                    {{item.id}}
                                </td>
                                <td class="text-xs">
                                    {{item.shipment_id}}
                                </td>
                                <td class="text-xs">
                                    {{item.contract_no}}
                                </td>
                                <td class="text-xs">
                                    {{item.total_days2}}
                                </td>
                                <td class="text-xs font-semibold" :class="item.status=='0'?'text-orange-500':item.status=='1'?'text-blue-700':'text-green-500'">
                                    {{item.status=='0'?'OPEN':item.status=='1'?'IN PROGRESS':'Fullfilled'}}
                                </td>
                                <td >
                                        <button type="button"  @click="showUpdate(item)" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                            <span class="tipstextL">Maintain</span>
                                        </button>
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
    var pages="<?= @$_GET['page'] ?>"
    new Vue({
        el:"#sales-laycan",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                statusnya:'',
                target:{},
                disableInput:[],
                sortTable:['id','shipment_no','contract_no','issue_date','status'], // disusun berdasarkan urutan td td td
                modals:false,
                showInsert:false,
                // CUSTOM
                datanya:[],
                vdata:{},
                vdata2:{},
                idnya:'',
                laycan:[],
                shipment:[],
                master_activity:[],
                dari_tanggal:'',
                sampai_tanggal:'',
                pages,
                option:{
                    headers:{
                        'X-Requested-With': 'XMLHttpRequest',
                        'contentType': "application/json",
                    }
                }
            }
        },
        components: {
            vSelect:VueSelect.VueSelect
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
                data=data.filter(e=>e.status.indexOf(this.statusnya)!=-1)
                return data;
            }
        },  
        methods: {
            async updateActivtiy(item,status,dataactivity=null){
                if(dataactivity){
                    if(status-parseInt(dataactivity.status)==2){
                        sdb.alert('Activity ini belum bisa di akses!');
                        return;
                    }
                }else{
                    if(status==1 || status==2){
                        sdb.alert('Activity ini belum bisa di akses!');
                        return;
                    }
                }
                if(!confirm('Are you sure to update this activity ?')){
                    return;
                }
                let data={
                    shipment_no:this.vdata.shipment_id,
                    contract_no:this.vdata.contract_no,
                    item_activity:item.activity,
                    status:status,
                }
                if(status=='0'){
                    data.draft_date=this.format(new Date())
                }else if(status=='1'){
                    data.issue_date=this.format(new Date())
                }else if(status=='2'){
                    data.received_date=this.format(new Date())
                }
                sdb.loadingOn();
                axios.post("<?= site_url() ?>" + `/api/method/sales-laycan`,data).then(res=>{
                    sdb.loadingOff();
                    // this.modals=false;
                    this.showUpdate(this.vdata)
                }).catch(err=>{
                    sdb.loadingOff();
                    this.showUpdate(this.vdata)
                    // this.modals=false;
                })
            },
            validateRequired(){ // validation manual select semua input required
                let validation=true;
                document.querySelectorAll('[required]').forEach(e=>{e.reportValidity()?'':validation=false});
                return validation;
            },
            async insertData(){
                let that=this;
                if(!this.validateRequired())return;
                sdb.loadingOn();
                let token=await axios.post("<?= site_url() ?>" + `/api/test`);
                this.vdata.created_by="<?php echo session()->get('username') ?>";
                let cekid=this.datanya.map(e=>e.id);
                this.vdata[Object.keys(token.data[0])[0]]=Object.values(token.data[0])[0];
                let uploadfile= await this.uploadFile();
                this.vdata.file=uploadfile;
                axios.post("<?= site_url() ?>" + `/api/sales-laycan`,this.vdata).then(async (res)=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert('Insert data berhasil!','bg-green-400');
                    this.getData();
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert(err.response.data.message??'Insert data gagal');
                });  
            },
            async updateData(loading=false){
                let that=this;
                if(!this.validateRequired())return;
                if(!loading){
                    sdb.loadingOn();
                }
                let id=this.vdata.id;
                delete this.vdata.id;
                this.vdata.updated_by="<?php echo session()->get('username') ?>";
                let uploadfile= await this.uploadFile();
                this.vdata.file_laycan=uploadfile;
                axios.put("<?= site_url() ?>" + `/api/sales-shipment/${id}`,this.vdata).then(res=>{
                    sdb.loadingOff();
                    this.modals=false;
                    this.getData()
                    sdb.alert('Update data berhasil!','bg-green-400');
                })
            },
            async updateData2(loading=false){
                let that=this;
                if(!this.validateRequired())return;
                if(!loading){
                    sdb.loadingOn();
                }
                let id=this.vdata.id;
                delete this.vdata.id;
                this.vdata.updated_by="<?php echo session()->get('username') ?>";
                axios.put("<?= site_url() ?>" + `/api/sales-laycan/${id}`,this.vdata).then(res=>{
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
            async deleteDataStatus(item){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                sdb.loadingOn();
                let id=item.id;
                delete item.id;
                item.status='2';
                axios.delete("<?= site_url() ?>" + `/api/sales-laycan/${id}`,item).then(res=>{
                    sdb.loadingOff();
                    this.modals=false;
                    this.getData()
                    sdb.alert('Delete data berhasil!','bg-green-400');
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    this.getData()
                    sdb.alert(err.response.data.message??'Delete data gagal');
                });   
            },
            async deleteData(data){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                sdb.loadingOn();
                axios.delete("<?= site_url() ?>" + `/api/sales-laycan/${data.id}`).then(res=>{
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
                this.vdata.shipment_no=item.shipment_id
                this.showInsert=false;
                this.modals=true;
                this.getDataLaycan();
                this.$forceUpdate();
            },
            async getDataLaycan(){
                let res = await axios.get("<?= site_url() ?>" + `/api/get/sales-laycan?shipment_no=${this.vdata.shipment_id}&contract_no=${this.vdata.contract_no}`);  
                this.laycan=res.data;
                console.log(res.data)
                this.$forceUpdate();
            },
            async getData(){
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/get/sales-shipment?laycan=true`);
                data.data=data.data.map(e=>{
                    return{
                        ...e,
                        total_days2:Math.abs(data.data.filter(k=>e.id==k.id).reduce((e,n)=>{return e+parseInt(n.total_days)},0))
                    }
                })
                this.datanya=data.data;
                this.datanya = [...new Map(this.datanya.map((item) => [item["id"], item])).values()];
                
                this.shipment=data.data;
                let res = await axios.get("<?= site_url() ?>" + `/api/get/sales-laycan`);
                let laycan=res.data;
                this.shipment=this.shipment.map(e=>{
                    return {
                        ...e,
                        total_days:laycan.filter(k=>k.shipment_no==e.shipment_id && k.contract_no==e.contract_no)
                    }
                })
                
                console.log('shipment',this.shipment)

                this.showInsert=false;
                this.customer=[]
                let master = await axios.get("<?= site_url() ?>" + `/api/get/master-activity`);
                this.master_activity=master.data;
                this.$forceUpdate();
                setTimeout(() => {
                    this.sortField();
                }, 1000);
            },
            async uploadFile(){
                let data={}
                let file = document.querySelector("#filenya"); // berikan id pada input file
                if(file.files.length>0){
                    if(!(file.files[0].name.indexOf('.pdf')!=-1)){
                        sdb.alert('Error format file must be *.pdf !');
                        return false;
                    }
                    if(!confirm('Are you sure to upload this file ? '))return;
                    sdb.loadingOn();
                    let fd= new FormData();
                    fd.append('file',file.files[0]);
                    fd.append('<?= csrf_token() ?>','<?= csrf_hash() ?>');
                    return await axios.post("<?= site_url() ?>" + `/api/upload`,fd,this.option).then(async (res)=>{
                        sdb.loadingOff();
                        if(res.data){
                            return res.data.filepath
                        }else{
                            return;
                        }
                    });
                }
            },
            dowloadpdf(item){
                console.log(item)
                let data={
                    path:item.file_laycan
                }
                let name = item.file_laycan.split('/')[2]
                axios.post("<?= site_url() ?>" + `/api/download/pdf`,data,{
                    responseType:'blob'
                }).then(response => {
                    const href = URL.createObjectURL(response.data);
                    const link = document.createElement('a');
                    link.href = href;
                    link.setAttribute('download', `Sales_laycan-0${item.id}.pdf`); //or any other extension
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
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
            formatDistance(tgl1,tgl2) {
                return dateFns.differenceInDays(
                    new Date(tgl1),
                    new Date(tgl2)
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
            document.getElementById('sales-laycan').classList.remove('d-none');
            document.getElementById('pageloading').remove()
            this.bulan = this.bulans(new Date());
            this.tahun = parseInt(this.tahuns(new Date()));
            if (this.bulan && this.tahun) {
                this.periode = `${this.tahun}-${this.bulan}-${this.hari(new Date())}`;
                var date = new Date(this.periode);
                this.dari_tanggal = this.format(new Date(date.getFullYear(), date.getMonth(), 1));
                this.sampai_tanggal = this.format(new Date(date.getFullYear(), date.getMonth() + 1, 0));
                this.$forceUpdate();
            }
            setTimeout(() => {
                this.getData();
            }, 500);
            this.$forceUpdate();
        },
    })
</script>
<?= $this->endSection() ?>
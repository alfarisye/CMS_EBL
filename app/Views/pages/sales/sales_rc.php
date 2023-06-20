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
    <div id="sales-rc" class="d-none">
        <div class="pagetitle">
            <h1>Sales RC</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Sales</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("sales/sales-rc") ?>">Sales RC</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn" style="height:80vh;overflow:scroll;">
                <p class="text-sm font-semibold">Return Cargo</p>
               <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <table class="table table-sm ">
                    <tr>
                        <td class="p-2">Shipment No</td>
                        <td class="px-2">:</td>
                        <td>
                            <v-select @input="getOther" :options="shipment" label="shipment_id" v-model="vdata.shipment_id" :reduce="e => e.shipment_id"></v-select>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Vessel</td>
                        <td class="px-2">:</td>
                        <td>
                            <input type="text" id="vessel" name="vessel" class="form-control p-1  rounded-sm shadow-sm" placeholder="vessel ..." v-model="vdata['vessel']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Date</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required type="date" id="date" name="date" class="form-control p-1  rounded-sm shadow-sm" placeholder="date ..." v-model="vdata['date']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Customer</td>
                        <td class="px-2">:</td>
                        <td>
                            <!-- <v-select :options="customer" label="item_data" v-model="vdata.customer_name" :reduce="e => e.NAME1"></v-select> -->
                            <input required disabled type="text" id="customer_name" name="customer_name" class="form-control p-1  rounded-sm shadow-sm" placeholder="customer_name ..." v-model="vdata['customer_name']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Contract No</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required disabled type="text" id="contract_no" name="contract_no" class="form-control p-1  rounded-sm shadow-sm" placeholder="contract_no ..." v-model="vdata['contract_no']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Shipped Qty</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required disabled type="text" id="shipped_qty" name="shipped_qty" class="form-control p-1  rounded-sm shadow-sm" placeholder="shipped_qty ..." v-model="vdata['shipped_qty']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Receipt Qty</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required disabled type="text" id="unloading_qty" name="unloading_qty" class="form-control p-1  rounded-sm shadow-sm" placeholder="unloading_qty ..." v-model="vdata['unloading_qty']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Variance</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required disabled type="text" id="variance" name="variance" class="form-control p-1  rounded-sm shadow-sm" placeholder="variance ..." v-model="vdata['variance']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Return Cargo</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required @change="getVariance" type="text" id="rc_qty" name="rc_qty" class="form-control p-1  rounded-sm shadow-sm" placeholder="rc_qty ..." v-model="vdata['rc_qty']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Upload</td>
                        <td class="px-2">:</td>
                        <td>
                            <input  type="file" id="filenya" name="filenya" class="form-control p-1  rounded-sm shadow-sm" placeholder="filenya ..."  >
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="p-2">Remarks</td>
                        <td class="px-2">:</td>
                        <td>
                            <div class="sm-form">
                            <textarea type="text" id="remarks" name="remarks" rows="2" placeholder="remarks..." class="form-control md-textarea" v-model="vdata.remarks" ></textarea>
                            </div>
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
                <h5 class="card-title" >Sales RC</h5>
                <div class="row">
                    <div class="col-6 text-xs">
                        <table>
                            <tr>
                                <td>
                                    <input type="date" id="dari_tanggal" @change="getData()" name="dari_tanggal" class="form-control p-1 rounded-sm shadow-sm" placeholder="dari_tanggal" v-model="dari_tanggal" >
                                </td>
                                <td class="px-3">S/D</td>
                                <td>
                                    <input type="date" id="sampai_tanggal" @change="getData()" name="sampai_tanggal" class="form-control p-1 rounded-sm shadow-sm" placeholder="sampai_tanggal" v-model="sampai_tanggal" >
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
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
                            <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={}">+ New Return Cargo</button>
                        </div>
                    </div>
                    <div class="col-sm-3">
                         <!-- <form v-if="datanya.length>0" action="<?= site_url() ?>/production/report/download?nama_file=production report" method="post">
                                <textarea :value="JSON.stringify(td)" v-show="false" type="text" id="datanya" name="datanya" rows="2" placeholder="datanya..." class="form-control md-textarea"  ></textarea>
                                <button type="submit" class="btn btn-sm btn-dark text-xs ">Excel <i class="ri-download-line"></i></button>
                            </form> -->
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-bordered ">
                        <tr>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Document No &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Date &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Customer &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Contract No &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Shipment No &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Shipped Qty &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Unloading Qty &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                variance &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Return Cargo &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Download &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Action 
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index+'form'" >
                                <td class="text-xs">
                                    RC-0{{item.id}}
                                </td>
                                <td class="text-xs">
                                    {{formatTgl(item.date,'DD-MM-YYYY')}}
                                </td>
                                <td class="text-xs">
                                    {{item.customer_name}}
                                </td>
                                <td class="text-xs">
                                    {{item.contract_no}}
                                </td>
                                <td class="text-xs">
                                    {{item.shipment_id}}
                                </td>
                                <td class="text-xs">
                                    {{item.shipped_qty}}
                                </td>
                                <td class="text-xs">
                                    {{item.unloading_qty}}
                                </td>
                                <td class="text-xs">
                                    {{item.variance}}
                                </td>
                                <td class="text-xs">
                                    {{item.rc_qty}}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning rounded-circle " @click="dowloadpdf(item)"><i class="ri-download-line"></i></button>
                                </td>
                                <td >
                                    <div v-if="disableInput[index] " >
                                        <button type="button"  @click="showUpdate(item)" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                            <span class="tipstextL">Edit</span>
                                        </button>
                                        <button type="button" @click="deleteData(item)" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                            <span class="tipstextL">Delete</span>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark text-xs ml-1" @click="disableInput[index]=!disableInput[index];$forceUpdate()">&#9779;</button>
                                    </div>
                                    <div v-else>
                                        <button  type="button" class="btn btn-sm btn-dark text-xs" @click="disableInput[index]=!disableInput[index];showInsert=false;$forceUpdate()">Edit &#9779;</button>
                                    </div>
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
        el:"#sales-rc",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                disableInput:[],
                sortTable:['id','date','customer_name','contract_no','shipment_no','shipped_qty','unloading_qty','variance'], // disusun berdasarkan urutan td td td
                modals:false,
                showInsert:false,
                // CUSTOM
                datanya:[],
                vdata:{},
                customer:[],
                shipment:[],
                product:[],
                sales_order:[],
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
                return data;
            }
        },  
        methods: {
            dowloadpdf(item){
                let data={
                    path:item.File
                }
                let name = item.File.split('/')[2]
                axios.post("<?= site_url() ?>" + `/api/download/pdf`,data,{
                    responseType:'blob'
                }).then(response => {
                    const href = URL.createObjectURL(response.data);
                    const link = document.createElement('a');
                    link.href = href;
                    link.setAttribute('download', `RC-0${item.id}.pdf`); //or any other extension
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                });
            },
            async sendEmail(to,cc,subject,message,attach=null){
                let txt=message;
                let fd=new FormData();
                fd.append('to',to);
                fd.append('cc',cc);
                fd.append('subject',subject);
                fd.append('message',txt);
                if(attach){
                    fd.append('attach',attach);
                }
                sdb.loadingOn();
                return await axios.post("<?= site_url() ?>"+`/api/send/mail`,fd).then(res=>{
                    if(res.data){
                        if(JSON.parse(res.data.split('}')[0]+'}').status=='true'){
                            sdb.loadingOff();
                            sdb.alert('Email Berhasil dikirim!','bg-green-400');
                            return true;
                        }else{
                            sdb.loadingOff();
                            sdb.alert('Email Gagal dikirim!','bg-red-400');
                            return false;
                        }
                    }
                });  
            },
            getBuyerName(){
                this.vdata.customer_name=this.customer.filter(e=>e.KUNNR==this.vdata.customer_code)[0].NAME1;
                this.$forceUpdate();
            },
            getProductName(){
                this.vdata.product_name=this.product.filter(e=>e.MATNR==this.vdata.product)[0].MAKTX;
                this.$forceUpdate();
            },
            getOther(){
                this.vdata.contract_no=this.shipment.filter(e=>e.shipment_id==this.vdata.shipment_id)[0].contract_no;
                this.vdata.vessel=this.shipment.filter(e=>e.shipment_id==this.vdata.shipment_id)[0].vessel;
                this.vdata.customer_name=this.sales_order.filter(e=>e.contract_no==this.shipment.filter(e=>e.shipment_id==this.vdata.shipment_id)[0].contract_no)[0].customer_name;
                this.vdata.shipped_qty=this.shipment.filter(e=>e.shipment_id==this.vdata.shipment_id)[0].gi_qty;
                this.vdata.unloading_qty=this.shipment.filter(e=>e.shipment_id==this.vdata.shipment_id)[0].bl_qty;
                this.$forceUpdate();
            },
            getVariance(){
                this.vdata.variance=parseFloat(this.vdata.shipped_qty??0)-parseFloat(this.vdata.rc_qty??0);
                this.$forceUpdate();
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
                this.vdata.status='';
                let token=await axios.post("<?= site_url() ?>" + `/api/test`);
                this.vdata.status='0'
                this.vdata.created_by="<?php echo session()->get('username') ?>";
                let cekid=this.datanya.map(e=>e.id);
                let uploadfile= await this.uploadFile();
                this.vdata.File=uploadfile;
                this.vdata[Object.keys(token.data[0])[0]]=Object.values(token.data[0])[0];
                axios.post("<?= site_url() ?>" + `/api/sales-rc`,this.vdata).then(async (res)=>{
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
            async updateData(){
                let that=this;
                if(!this.validateRequired())return;
                sdb.loadingOn();
                let id=this.vdata.id;
                delete this.vdata.id;
                this.vdata.updated_by="<?php echo session()->get('username') ?>";
                let uploadfile= await this.uploadFile();
                this.vdata.File=uploadfile;
                axios.put("<?= site_url() ?>" + `/api/sales-rc/${id}`,this.vdata).then(res=>{
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
                console.log("<?= site_url() ?>" + `/api/sales-rc/${data.id}`)
                axios.delete("<?= site_url() ?>" + `/api/sales-rc/${data.id}`).then(res=>{
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
                        // data['bl_type_id']=this.typeProduksi;
                        // data['periode']=this.periode;
                        // data['geojson']=res.data.filepath;
                        // window.location.reload();
                    // await axios.post("<?= site_url() ?>" + `/api/bukaan-lahan/geojson`,data).then(res=>{
                    //     console.log(res)
                    //     });
                    });
                }
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
                data = await axios.get("<?= site_url() ?>" + `/api/get/sales-rc?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.datanya=data.data;
                this.showInsert=false;
                this.customer=[]
                let customer = await axios.get("<?= site_url() ?>" + `/api/get/customer`);
                this.customer=customer.data;
                let sales_order = await axios.get("<?= site_url() ?>" + `/api/get/sales-order`);
                this.sales_order=sales_order.data;
                let shipment = await axios.get("<?= site_url() ?>" + `/api/get/sales-shipment`);
                this.shipment=shipment.data;
                let product = await axios.get("<?= site_url() ?>" + `/api/get/product-material`);
                this.product=product.data;
                this.customer.map(function (x){ // taruh setelah produks di initial
                      return x.item_data = x.KUNNR + ' ' + x.NAME1 ;
                });
                this.product.map(function (x){ // taruh setelah produks di initial
                      return x.item_data = x.MATNR + ' ' + x.MAKTX ;
                });
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
            formatTgl(tgl,pattern="YYYY-MM-DD") {
                return dateFns.format(
                    new Date(tgl),
                    pattern
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
            document.getElementById('sales-rc').classList.remove('d-none');
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
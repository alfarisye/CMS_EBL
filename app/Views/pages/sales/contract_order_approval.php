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
    <div id="sales-order" class="d-none">
        <div class="pagetitle">
        <h1>Sales Order 
                <span @click="sendEmail('Taufik.Maliki@hasnurgroup.com','MAbdullah.Sani@hasnurgroup.com','subject','test text')">Approval</span> </h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Sales</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("sales/contract-order-approval") ?>">Sales Order Approval</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2" style="position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:10005;min-width:25vw">
            <div class="text-center rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn" style="height:30vh;">
            <br>
                <p class="text-sm font-semibold">Are you sure to Approve Sales Order ? </p>
                 <button type="button" @click="approve" class="btn btn-sm btn-success ml-3">Yes</button>
                 <button type="button" @click="modals=false" class="btn btn-sm btn-danger ml-3">Cancel</button>
            </div>
        </div>
        <div v-if="modals2" @click="modals2=false" class="modal1"></div>
        <div v-if="modals2" class="modal2" style="position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:10005;min-width:25vw">
            <div class="text-center rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn" style="height:30vh;">
            <br>
                <p class="text-sm font-semibold">Are you sure to Approve Sales Order ? </p>
                 <button type="button" @click="approve" class="btn btn-sm btn-success ml-3">Yes</button>
                 <button type="button" @click="modals2=false" class="btn btn-sm btn-danger ml-3">Cancel</button>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" >Sales Order Approval</h5>
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
                        <!-- <select class='form-control' v-model="status">
                            <option value="0">Draft</option>
                            <option value="1">Full Approved</option>
                            <option value="2">Delete</option>
                            <option value="3">Complete</option>
                            <option value="4">Partial Approved 1</option>
                            <option value="5">Partial Approved 2</option>
                            <option value="6">Partial Approved 3</option>
                            <option value="7">Partial Approved 4</option>
                            <option value="8">Partial Approved 5</option>
                        </select> -->
                        <!-- <div class="text-center " >
                            <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={}">+ New Sales Order</button>
                        </div> -->
                    </div>
                    <div class="col-sm-3">
                         <form v-if="datanya.length>0" action="<?= site_url() ?>/production/report/download?nama_file=Sales Order" method="post">
                                <textarea :value="JSON.stringify(td)" v-show="false" type="text" id="datanya" name="datanya" rows="2" placeholder="datanya..." class="form-control md-textarea"  ></textarea>
                                <button type="submit" class="btn btn-sm btn-dark text-xs ">Excel <i class="ri-download-line"></i></button>
                            </form>
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-sm table-bordered ">
                        <tr>
                            <th style="background:lightgreen;" class="text-xs" scope="col">

                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Sales No &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Customer &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Status &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Quantity &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                UoM &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Price &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Curr &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Last Update &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Action 
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index+'form'" >
                                <td style="width:150px;">
                                    <div >
                                        <button type="button" :class="item.status=='1' || item.status=='4' || item.status=='5' || item.status=='6' || item.status=='7' || item.status=='8'?'bg-success text-white':'bg-dark text-white'" class="btn btn-sm rounded-circle p-1 text-xs my-1"><i style="font-size:9px;" class="my-1 py-1 ri-check-line text-xs"></i></button>
                                        <button type="button" :class="item.status=='1' || item.status=='5' || item.status=='6' || item.status=='7' || item.status=='8'?'bg-success text-white':'bg-dark text-white'" class="btn btn-sm rounded-circle p-1 text-xs my-1"><i style="font-size:9px;" class="my-1 py-1 ri-check-line text-xs"></i></button>
                                        <button type="button" :class="item.status=='1' || item.status=='6' || item.status=='7' || item.status=='8'?'bg-success text-white':'bg-dark text-white'" class="btn btn-sm rounded-circle p-1 text-xs my-1"><i style="font-size:9px;" class="my-1 py-1 ri-check-line text-xs"></i></button>
                                        <button type="button" :class="item.status=='1' || item.status=='7' || item.status=='8'?'bg-success text-white':'bg-dark text-white'" class="btn btn-sm rounded-circle p-1 text-xs my-1"><i style="font-size:9px;" class="my-1 py-1 ri-check-line text-xs"></i></button>
                                        <button type="button" :class="item.status=='1' || item.status=='8'?'bg-success text-white':'bg-dark text-white'" class="btn btn-sm rounded-circle p-1 text-xs my-1"><i style="font-size:9px;" class="my-1 py-1 ri-check-line text-xs"></i></button>
                                        <button type="button" :class="item.status=='1' ?'bg-success text-white':'bg-dark text-white'" class="btn btn-sm rounded-circle p-1 text-xs my-1"><i style="font-size:9px;" class="my-1 py-1 ri-check-line text-xs"></i></button>
                                    </div>
                                </td>
                                <td class="text-xs">
                                    {{item.id}}
                                </td>
                                <td class="text-xs">
                                    {{item.customer_name}}
                                </td>
                                <td class="text-xs font-semibold" :class="item.status=='0'?'text-orange-500':item.status=='1'?'text-green-500':item.status=='2'?'text-red-500':item.status=='3'?'text-green-500':item.status=='4'?'text-blue-500':item.status=='5'?'text-blue-600':item.status=='6'?'text-blue-500':item.status=='7'?'text-blue-500':item.status=='8'?'text-blue-500':'text-black'">
                                    {{item.status=='0'?'Draft':item.status=='1'?'Full Approved':item.status=='2'?'Delete':item.status=='3'?'Complete':item.status=='4'?'Partial Approved 1':item.status=='5'?'Partial Approved 2':item.status=='6'?'Partial Approved 3':item.status=='7'?'Partial Approved 4':item.status=='8'?'Partial Approved 5':'Undefined'}}
                                </td>
                                
                                <td class="text-xs">
                                    {{item.quantity}}
                                </td>
                                
                                <td class="text-xs">
                                    {{item.uom}}
                                </td>
                                <td class="text-xs">
                                    {{item.contract_price}}
                                </td>
                                <td class="text-xs">
                                    {{item.currency}}
                                </td>
                                <td class="text-xs">
                                    {{formatTgl(item.updated_at,'DD-MM-YYYY HH:mm:ss')}}
                                </td>
                                <td style="width:110px;">
                                    <div v-if="users.level">
                                        <div v-if="(users.level=='1' && item.status=='4') || (users.level=='2' && item.status=='5')  || (users.level=='3' && item.status=='6') || (users.level=='4' && item.status=='7') || (users.level=='5' && item.status=='8')">
                                            <div class="tips" v-if="item.status=='0' || item.status=='4' || item.status=='5' || item.status=='6' || item.status=='7' || item.status=='8'">
                                                <button @click="cekApprove(item)" type="button" class="btn btn-sm btn-primary rounded-circle text-xs "><i style="font-size:9px;" class="my-1 py-1 ri-check-line text-xs"></i></button>
                                                <span class="tipstextL">Approve 1</span>
                                            </div>
                                            <div class="tips" v-if="item.status=='0' || item.status=='4' || item.status=='5' || item.status=='6' || item.status=='7' || item.status=='8'">
                                                <button @click="cekRevoke(item)" type="button" class="btn btn-sm btn-danger rounded-circle text-xs "><i style="font-size:9px;" class="my-1 py-1 ri-close-circle-line text-xs"></i></button>
                                                <span class="tipstextL">Revoke</span>
                                            </div>
                                        </div>
                                        <div v-else-if="users.level=='0' && (item.status!='1' && item.status!='3')">
                                            <div class="tips">
                                                <button @click="cekApprove(item)" type="button" class="btn btn-sm btn-primary rounded-circle text-xs "><i style="font-size:9px;" class="my-1 py-1 ri-check-line text-xs"></i></button>
                                                <span class="tipstextL">Approve</span>
                                            </div>
                                            <div class="tips">
                                                <button @click="cekRevoke(item)" type="button" class="btn btn-sm btn-danger rounded-circle text-xs "><i style="font-size:9px;" class="my-1 py-1 ri-close-circle-line text-xs"></i></button>
                                                <span class="tipstextL">Revoke</span>
                                            </div>
                                        </div>
                                        <div class="tips" v-if="item.status=='4' || item.status=='5' || item.status=='6' || item.status=='7' || item.status=='8'">
                                            <button @click="sendEmail2(item)" type="button" class="btn btn-sm btn-info btn-block mt-2 text-xs ">Notif <span class="font-semibold ">!</span></button>
                                            <span class="tipstextL">Kirim Notifikasi</span>
                                        </div>
                                        <!-- <div v-if="disableInput[index] && item.status=='0'" >
                                            <button type="button"  @click="showUpdate(item)" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                                <span class="tipstextB">Edit</span>
                                            </button>
                                            <button type="button" @click="deleteDataStatus(item)" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                                <span class="tipstextB">Delete</span>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-dark text-xs ml-1" @click="disableInput[index]=!disableInput[index];$forceUpdate()">&#9779;</button>
                                        </div>
                                        <div v-else>
                                            <button v-if="item.status=='0'" type="button" class="btn btn-sm btn-dark text-xs" @click="disableInput[index]=!disableInput[index];showInsert=false;$forceUpdate()">Edit &#9779;</button>
                                        </div> -->
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
        el:"#sales-order",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                disableInput:[],
                sortTable:['id','customer_name','date','status','quantity','uom','created_at','updated_at'], // disusun berdasarkan urutan td td td
                modals:false,
                modals2:false,
                showInsert:false,
                // CUSTOM
                datanya:[],
                vdata:{},
                customer:[],
                product:[],
                dari_tanggal:'',
                sampai_tanggal:'',
                status:'0',
                pilih:{},
                users:{},
                listUser:[],
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
                // data=data.filter(e=>e.status.indexOf(that.status)!=-1);
                return data;
            }
        },  
        methods: {
            cekApprove(item){
                this.modals=true;
                this.pilih=item;
                this.$forceUpdate();
            },
            cekRevoke(item){
                this.modals2=true;
                this.pilih=item;
                this.$forceUpdate();
            },
            async approve(){
                let user=this.users;
                let txt=`<h3>Dengan Hormat</h3>
                <p>Berikut kami lampirkan draft kontrak untuk Customer <b>${this.pilih.customer_name}</b></p>
                <p>Mohon bantuannya untuk dapat mereview serta persetujuan apabila kontrak tersebut sudah sesuai dengan membuka link : </p>
                <p>
                    <a href="<?= site_url() ?>/sales/contract-order-approval"> <?= site_url() ?>/sales/contract-order-approval </a>
                </p>
                <br>
                <br>
                <p>Terima Kasih</p>
                <br>
                <br>
                <p>Coal Monitoring System</p>
                <p>PT. Energi Batubara Lestari</p>
                <hr>`;
                if((user.level=='1'||user.level=='0') && this.pilih.status=='4'){
                    this.modals=false;
                    this.$forceUpdate();
                    this.sendNotification(this.listUser.filter(e=>e.level=='2')[0].user_name,'',`Approval Contract ${this.pilih.id}`,txt);
                    // await this.sendEmail(this.listUser.filter(e=>e.level=='2')[0].email, this.listUser.filter(e=>e.level=='2')[0].email,`Approval Contract ${this.pilih.id}`,txt+`
                    
                    // Ke Level 2
                    // `,this.pilih.pdf);
                    this.approveData('5');
                    sdb.alert('Permintaan Approval di teruskan ke level 2','bg-yellow-600');
                }else if((user.level=='2'||user.level=='0') && this.pilih.status=='5'){
                    this.modals=false;
                    this.$forceUpdate();
                    this.sendNotification(this.listUser.filter(e=>e.level=='3')[0].user_name,'',`Approval Contract ${this.pilih.id}`,txt);
                    // await this.sendEmail(this.listUser.filter(e=>e.level=='3')[0].email, this.listUser.filter(e=>e.level=='3')[0].email,`Approval Contract ${this.pilih.id}`,txt+`
                    
                    // Ke Level 3
                    // `,this.pilih.pdf);
                    // this.approveData('6');
                    this.approveData('1');
                    sdb.alert('Permintaan Approval di teruskan ke level 3','bg-yellow-600');
                }else if((user.level=='3'||user.level=='0') && this.pilih.status=='6'){
                    this.modals=false;
                    this.$forceUpdate();
                    this.sendNotification(this.listUser.filter(e=>e.level=='4')[0].user_name,'',`Approval Contract ${this.pilih.id}`,txt);
                    // await this.sendEmail(this.listUser.filter(e=>e.level=='4')[0].email, this.listUser.filter(e=>e.level=='4')[0].email,`Approval Contract ${this.pilih.id}`,txt+`
                    
                    // Ke Level 4
                    // `,this.pilih.pdf);
                    this.approveData('7');
                    sdb.alert('Permintaan Approval di teruskan ke level 4','bg-yellow-600');
                }else if((user.level=='4'||user.level=='0') && this.pilih.status=='7'){
                    this.modals=false;
                    this.$forceUpdate();
                    this.sendNotification(this.listUser.filter(e=>e.level=='5')[0].user_name,'',`Approval Contract ${this.pilih.id}`,txt);
                    // await this.sendEmail(this.listUser.filter(e=>e.level=='5')[0].email, this.listUser.filter(e=>e.level=='5')[0].email,`Approval Contract ${this.pilih.id}`,txt+`
                    
                    // Ke Level 5
                    // `,this.pilih.pdf);
                    this.approveData('8');
                    sdb.alert('Permintaan Approval di teruskan ke level 5','bg-yellow-600');
                }else if((user.level=='5'||user.level=='0') && this.pilih.status=='8'){
                    this.modals=false;
                    this.$forceUpdate();
                    // this.sendNotification(this.listUser.filter(e=>e.level=='1')[0].user_name,'',`Approval Contract ${this.pilih.id}`,txt);
                    // await this.sendEmail(this.listUser.filter(e=>e.level=='1')[0].email, this.listUser.filter(e=>e.level=='1')[0].email,`Approval Contract ${this.pilih.id}`,txt+`
                    
                    // Ke Level 1
                    // `,this.pilih.pdf);
                    this.approveData('1');
                    sdb.alert('Permintaan Approval di teruskan ke level 1','bg-yellow-600');
                }else{
                    sdb.alert('Level Approval berbeda!');
                }
            },
            async approveData(status){
                this.vdata=this.pilih;
                this.vdata.status=status;
                if(status=='5'){
                    this.vdata.approved_by1="<?php echo session()->get('username') ?>";
                    this.vdata.approved_date1=this.formatTgl(new Date(),'YYYY-MM-DD HH:mm:ss');
                }else if(status=='1'){
                    this.vdata.approved_by2="<?php echo session()->get('username') ?>";
                    this.vdata.approved_date2=this.formatTgl(new Date(),'YYYY-MM-DD HH:mm:ss');
                }
                this.updateData();
            },
            disapprove(){
                sdb.alert('disapprove');
                this.modals2=false;
                this.$forceUpdate();
            },
            getBuyerName(){
                this.vdata.customer_name=this.customer.filter(e=>e.KUNNR==this.vdata.customer_code)[0].NAME1;
                this.$forceUpdate();
            },
            getProductName(){
                this.vdata.product_name=this.product.filter(e=>e.MATNR==this.vdata.product)[0].MAKTX;
                this.$forceUpdate();
            },
            validateRequired(){ // validation manual select semua input required
                let validation=true;
                document.querySelectorAll('[required]').forEach(e=>{e.reportValidity()?'':validation=false});
                return validation;
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
                    // if(res.data){
                    //     if(JSON.parse(res.data.split('}')[0]+'}').status=='true'){
                            sdb.loadingOff();
                            // sdb.alert('Email Berhasil dikirim!','bg-green-400');
                            return true;
                    //     }else{
                    //         sdb.loadingOff();
                    //         sdb.alert('Email Gagal dikirim!','bg-red-400');
                    //         return false;
                    //     }
                    // }
                });  
            },
            async sendEmail2(item){
                this.pilih=item;
                let txt=`<h3>Dengan Hormat</h3>
                <p>Berikut kami lampirkan draft kontrak untuk Customer <b>${this.pilih.customer_name}</b></p>
                <p>Mohon bantuannya untuk dapat mereview serta persetujuan apabila kontrak tersebut sudah sesuai dengan membuka link : </p>
                <p>
                    <a href="<?= site_url() ?>/sales/contract-order-approval"> <?= site_url() ?>/sales/contract-order-approval </a>
                </p>
                <br>
                <br>
                <p>Terima Kasih</p>
                <br>
                <br>
                <p>Coal Monitoring System</p>
                <p>PT. Energi Batubara Lestari</p>
                <hr>`;
                if(this.pilih.status=='4'){
                    if(!confirm(`Kirim email notifikasi ke ${this.listUser.filter(e=>e.level=='1')[0].email} ?`))return;
                    await this.sendEmail(this.listUser.filter(e=>e.level=='1')[0].email, this.listUser.filter(e=>e.level=='2')[0].email,`Approval Contract ${this.pilih.id}`,txt+``,this.pilih.pdf);
                    sdb.alert(`Email Dikirim ke ${this.listUser.filter(e=>e.level=='1')[0].email}...`,'bg-yellow-600');
                }else if(this.pilih.status=='5'){
                    if(!confirm(`Kirim email notifikasi ke ${this.listUser.filter(e=>e.level=='2')[0].email} ?`))return;
                    await this.sendEmail(this.listUser.filter(e=>e.level=='2')[0].email, this.listUser.filter(e=>e.level=='3')[0].email,`Approval Contract ${this.pilih.id}`,txt+``,this.pilih.pdf);
                    sdb.alert(`Email Dikirim ke ${this.listUser.filter(e=>e.level=='2')[0].email}...`,'bg-yellow-600');
                }else if(this.pilih.status=='6'){
                    if(!confirm(`Kirim email notifikasi ke ${this.listUser.filter(e=>e.level=='3')[0].email} ?`))return;
                    await this.sendEmail(this.listUser.filter(e=>e.level=='3')[0].email, this.listUser.filter(e=>e.level=='4')[0].email,`Approval Contract ${this.pilih.id}`,txt+``,this.pilih.pdf);
                    sdb.alert(`Email Dikirim ke ${this.listUser.filter(e=>e.level=='3')[0].email}...`,'bg-yellow-600');
                }else if(this.pilih.status=='7'){
                    if(!confirm(`Kirim email notifikasi ke ${this.listUser.filter(e=>e.level=='4')[0].email} ?`))return;
                    await this.sendEmail(this.listUser.filter(e=>e.level=='4')[0].email, this.listUser.filter(e=>e.level=='5')[0].email,`Approval Contract ${this.pilih.id}`,txt+``,this.pilih.pdf);
                    sdb.alert(`Email Dikirim ke ${this.listUser.filter(e=>e.level=='4')[0].email}...`,'bg-yellow-600');
                }else if(this.pilih.status=='8'){
                    if(!confirm(`Kirim email notifikasi ke ${this.listUser.filter(e=>e.level=='5')[0].email} ?`))return;
                    await this.sendEmail(this.listUser.filter(e=>e.level=='1')[0].email, this.listUser.filter(e=>e.level=='1')[0].email,`Approval Contract ${this.pilih.id}`,txt+``,this.pilih.pdf);
                    sdb.alert(`Email Dikirim ke ${this.listUser.filter(e=>e.level=='5')[0].email}...`,'bg-yellow-600');
                }else{
                    sdb.alert('Level Approval berbeda!');
                }
            },
            sendNotification(to,cc,subject,message,attach=''){
                let data={
                    "user_id_from":"<?php echo session()->get('username') ?>",
                    "user_id_to":to,
                    "user_id_cc":cc,
                    "subject":subject,
                    "message":message,
                    "attach":attach,
                    "type":"notification",
                    "status":"0",
                    "detail":"detail",
                    "created_by":"1"
                }
                console.log(data)
                axios.post("<?= site_url() ?>"+"/api/send/notification",data).then(res=>{
                    sdb.alert('Notifikasi Terkirim !','bg-green-400');
                });
            },
            async insertData(){
                if(!this.validateRequired())return;
                sdb.loadingOn();
                this.vdata.status='';
                let token=await axios.post("<?= site_url() ?>" + `/api/test`);
                this.vdata.status='0'
                this.vdata.created_by="<?php echo session()->get('username') ?>";
                let cekid=this.datanya.map(e=>e.id);
                console.log()
                this.vdata[Object.keys(token.data[0])[0]]=Object.values(token.data[0])[0];
                axios.post("<?= site_url() ?>" + `/api/sales-order`,this.vdata).then(res=>{
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
                this.vdata.updated_by="<?php echo session()->get('username') ?>";
                axios.put("<?= site_url() ?>" + `/api/sales-order/${id}`,this.vdata).then(res=>{
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
                axios.put("<?= site_url() ?>" + `/api/sales-order/${id}`,item).then(res=>{
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
                console.log("<?= site_url() ?>" + `/api/sales-order/${data.id}`)
                axios.delete("<?= site_url() ?>" + `/api/sales-order/${data.id}`).then(res=>{
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
                data = await axios.get("<?= site_url() ?>" + `/api/get/sales-order?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.datanya=data.data;
                this.showInsert=false;
                this.customer=[]
                let customer = await axios.get("<?= site_url() ?>" + `/api/get/customer`);
                this.customer=customer.data;
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
        async mounted() {
            document.getElementById('sales-order').classList.remove('d-none');
            document.getElementById('pageloading').remove()
            let cek = await axios.get("<?= site_url() ?>"+'/api/get/t_sal_approval_step')
            if(cek.data){
                let listUser=cek.data;
                // let listUser=[
                //     {
                //         id:1,
                //         level:1,
                //         user_name:'malit',
                //         email:'mrrudyska@gmail.com'
                //     },
                //     {
                //         id:2,
                //         level:2,
                //         user_name:'malit',
                //         email:'mrrudyska@gmail.com'
                //     },
                //     {
                //         id:3,
                //         level:3,
                //         user_name:'malit',
                //         email:'mrrudyska@gmail.com'
                //     },
                //     {
                //         id:4,
                //         level:4,
                //         user_name:'malit',
                //         email:'mrrudyska@gmail.com'
                //     },
                //     {
                //         id:5,
                //         level:5,
                //         user_name:'malit',
                //         email:'mrrudyska@gmail.com'
                //     },
                // ]
                this.listUser=listUser;
                let cek2=listUser.filter(e=>e.user_name=="<?= $_SESSION['username'] ?>")
                if(cek2.length>0){
                    this.users=cek2[0];
                }
                this.$forceUpdate();
            }
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
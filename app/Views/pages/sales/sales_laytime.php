<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/vue-select@3.0.0/dist/vue-select.css">
<!-- SCRIPT -->
<script src="https://unpkg.com/vue-select@3.0.0"></script>


<script src="https://cdn.jsdelivr.net/npm/jstat@1.9.2/dist/jstat.min.js"></script> 
<script src="https://cdn.jsdelivr.net/gh/formulajs/formulajs@2.9.3/dist/formula.min.js"></script>
<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .border-hover{border:1px solid transparent}
    .border-hover:hover{border:1px solid #d0d0d0}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:1500;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:1505;min-width:96vw}
</style>
<main id="main" class="main">
    <div id="sales-laytime" class="d-none">
        <div class="pagetitle">
            <h1>Sales Laytime</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Sales</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("sales/sales-laytime") ?>">Sales Laytime</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn" style="height:90vh;overflow:scroll;">
               
                <p class="text-lg font-semibold">Laytime Monitoring</p>
                <p class=" font-semibold text-green-400 ml-3 text-lg">{{vdata.id?"00"+vdata.id:'Transaction Number'}}</p>
                <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <div class="row">
                    <div class="col-sm-6">
                        <table class="table table-sm ">
                            <tr>
                                <td class="p-2">Type</td>
                                <td class="px-2">:</td>
                                <td>
                                    <select class='form-control' @change="getHours" v-model="vdata.type">
                                        <option>Vessel</option>
                                        <option>Barge</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Shipment No</td>
                                <td class="px-2">:</td>
                                <td>
                                    <v-select :options="shipment" @input="getOther" label="shipment_id" v-model="vdata.shipment_no" :reduce="e => e.shipment_id"></v-select>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Contract No</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required disabled type="contract_no" id="contract_no" name="contract_no" class="form-control p-1  rounded-sm shadow-sm" placeholder="contract_no ..." v-model="vdata['contract_no']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Agreed Laycan (No. SPAL)</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required type="text" id="agreed_laycan" name="agreed_laycan" class="form-control p-1  rounded-sm shadow-sm" placeholder="No. SPAL ..." v-model="vdata['agreed_laycan']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Vessel Name</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required disabled type="text" id="vessel_name" name="vessel_name" class="form-control p-1  rounded-sm shadow-sm" placeholder="vessel_name ..." v-model="vdata['vessel_name']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Vessel Arrival</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <input type="date" id="vessel_arrived_date" name="vessel_arrived_date" class="form-control p-1  rounded-sm shadow-sm" placeholder="vessel_arrived_date ..." v-model="vdata['vessel_arrived_date']" >
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="time" id="vessel_arrived_time" name="vessel_arrived_time" class="form-control p-1  rounded-sm shadow-sm" placeholder="vessel_arrived_time ..." v-model="vdata['vessel_arrived_time']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">NOR Tendered</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <input  type="date" id="nor_tendered_date" name="nor_tendered_date" class="form-control p-1  rounded-sm shadow-sm" placeholder="nor_tendered_date ..." v-model="vdata['nor_tendered_date']" >
                                        </div>
                                        <div class="col-sm-4">
                                            <input  type="time" id="nor_tendered_time" name="nor_tendered_time" class="form-control p-1  rounded-sm shadow-sm" placeholder="nor_tendered_time ..." v-model="vdata['nor_tendered_time']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">NOR Retendered</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <input  type="date" id="nor_retendered_date" name="nor_retendered_date" class="form-control p-1  rounded-sm shadow-sm" placeholder="nor_retendered_date ..." v-model="vdata['nor_retendered_date']" >
                                        </div>
                                        <div class="col-sm-4">
                                            <input  type="time" id="nor_retendered_time" name="nor_retendered_time" class="form-control p-1  rounded-sm shadow-sm" placeholder="nor_retendered_time ..." v-model="vdata['nor_retendered_time']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Remarks</td>
                                <td class="px-2">:</td>
                                <td style="height:100px;">
                                    <div class="sm-form">
                                    <textarea type="text"  id="remarks" name="remarks" rows="2" placeholder="remarks..." class="form-control md-textarea" v-model="vdata.remarks" ></textarea>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Upload</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input  type="file" id="filenya" name="filenya" class="form-control p-1  rounded-sm shadow-sm" placeholder="filenya ..."  >
                                    <button type="button" class="btn btn-sm btn-primary my-2 " @click="dowloadpdf(vdata)" v-if="vdata.file!=''">Download</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table>
                        <tr>
                                <td class="p-2">Loading Commence</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <input  type="date" id="loading_commence_date" name="loading_commence_date" class="form-control p-1  rounded-sm shadow-sm" placeholder="loading_commence_date ..." v-model="vdata['loading_commence_date']" >
                                        </div>
                                        <div class="col-sm-4">
                                            <input  type="time" id="loading_commence_time" name="loading_commence_time" class="form-control p-1  rounded-sm shadow-sm" placeholder="loading_commence_time ..." v-model="vdata['loading_commence_time']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Loading Completed</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <input  type="date" id="loading_completed_date" name="loading_completed_date" class="form-control p-1  rounded-sm shadow-sm" placeholder="loading_completed_date ..." v-model="vdata['loading_completed_date']" >
                                        </div>
                                        <div class="col-sm-4">
                                            <input  type="time" id="loading_completed_time" name="loading_completed_time" class="form-control p-1  rounded-sm shadow-sm" placeholder="loading_completed_time ..." v-model="vdata['loading_completed_time']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Cargo Quantity</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <input   disabled type="text" id="cargo_qty" name="cargo_qty" class="form-control p-1  rounded-sm shadow-sm" placeholder="cargo_qty ..." v-model="vdata['cargo_qty']" >
                                        </div>
                                        <div class="col-sm-4">
                                            <input   disabled type="text" id="cargo_uom" name="cargo_uom" class="form-control p-1  rounded-sm shadow-sm" placeholder="cargo_uom ..." v-model="vdata['cargo_uom']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Loading Rate / Day</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <input required :disabled="vdata.type=='Barge'" type="text" @change="getHours" id="loading_rate_qty" name="loading_rate_qty" class="form-control p-1  rounded-sm shadow-sm" placeholder="loading_rate_qty ..." v-model="vdata['loading_rate_qty']" >
                                        </div>
                                        <div class="col-sm-4">
                                            <input required disabled type="text" id="loading_rate_oum" name="loading_rate_oum" class="form-control p-1  rounded-sm shadow-sm" placeholder="loading_rate_oum ..." v-model="vdata['loading_rate_oum']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">POD (Discharging Port)</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required :disabled="vdata.type=='Vessel'" type="text" id="discharging_port" name="discharging_port" class="form-control p-1  rounded-sm shadow-sm" placeholder="discharging_port ..." v-model="vdata['discharging_port']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Demurrage / Dispatch Rate</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <input required type="text" @change="getHours" id="demmurage" name="demmurage" class="form-control p-1  rounded-sm shadow-sm" placeholder="demmurage ..." v-model="vdata['demmurage']" >
                                        </div>
                                        <div class="col-sm-4">
                                            <input required type="text" disabled id="dispatch" name="dispatch" class="form-control p-1  rounded-sm shadow-sm" placeholder="dispatch ..." v-model="vdata['dispatch']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Currency</td>
                                <td class="px-2">:</td>
                                <td>
                                    <select class='form-control' v-model="vdata.curr">
                                        <option>IDR</option>
                                        <option>USD</option>
                                    </select>
                                </td>
                            </tr>
                           
                            <tr>
                                <td class="p-2">Laytime Allowed</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Hour
                                            <input required disabled type="text" id="laytime_allow_hour" name="laytime_allow_hour" class="form-control p-1  rounded-sm shadow-sm" placeholder="laytime_allow_hour ..." v-model="vdata['laytime_allow_hour']" >
                                        </div>
                                        <div class="col-sm-6">
                                            Days
                                            <input required disabled type="text" id="laytime_allow_days" name="laytime_allow_days" class="form-control p-1  rounded-sm shadow-sm" placeholder="laytime_allow_days ..." v-model="vdata['laytime_allow_days']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Days In Demmurage</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Days
                                            <input  type="text" disabled id="days_demmurage" name="days_demmurage" class="form-control p-1  rounded-sm shadow-sm" placeholder="days_demmurage ..." v-model="vdata['days_demmurage']" >
                                        </div>
                                        <div class="col-sm-6">
                                            Value
                                            <input  type="text" disabled id="value_demmurage" name="value_demmurage" class="form-control p-1  rounded-sm shadow-sm" placeholder="value_demmurage ..." v-model="vdata['value_demmurage']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Days In Dispatch</td>
                                <td class="px-2">:</td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Days
                                            <input  type="text" disabled id="days_dispatch" name="days_dispatch" class="form-control p-1  rounded-sm shadow-sm" placeholder="days_dispatch ..." v-model="vdata['days_dispatch']" >
                                        </div>
                                        <div class="col-sm-6">
                                            Value
                                            <input  type="text" disabled id="value_dispatch" name="value_dispatch" class="form-control p-1  rounded-sm shadow-sm" placeholder="value_dispatch ..." v-model="vdata['value_dispatch']" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Status</td>
                                <td class="px-2">:</td>
                                <td>
                                    <select class='form-control' v-model="vdata.status">
                                        <option>Draft</option>
                                        <option>Final</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-12">
                        <div class="text-right">
                            <button type="button" @click="modals=false"  class="btn btn-sm btn-warning ml-2 ">Cancel</button>
                            <button type="submit"  class="btn btn-sm btn-success ml-2 ">{{showInsert?'Save Data':'Save Data'}}</button>
                        </div>
                    </div>
                    <div class="col-sm-12 " v-if="vdata.id">
                        <hr class="my-2">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered ">
                                <tr>
                                    <th class="text-xs">Item</th>
                                    <th class="text-xs">Wheater</th>
                                    <th class="text-xs">Date</th>
                                    <th class="text-xs">Day</th>
                                    <th class="text-xs">From</th>
                                    <th class="text-xs">To</th>
                                    <th class="text-xs">Code</th>
                                    <th class="text-xs">Description</th>
                                    <th class="text-xs">Crane Rate Usage (%)</th>
                                    <th class="text-xs">Laytime To Count (Hrs)</th>
                                    <th class="text-xs">Time Not To Count (Hrs)</th>
                                    <th class="text-xs">Total Laytime Used (Hrs)</th>
                                    <th class="text-xs">Total Laytime Used (Days)</th>
                                    <th class="text-xs">Balance Laytime Allowed (Days)</th>
                                    <th class="text-xs">Action</th>
                                </tr>
                                <tbody v-if="datanya2.length>0">
                                    <tr v-for="(item, index) in td2" :key="index+'td2'">
                                        <td class="text-xs">
                                            <input type="text" style="width:40px;" id="item" name="item" class="form-control p-1 text-xs " placeholder="item" v-model="vdata2[index]['item']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="text" id="wheater" name="wheater" class="form-control p-1 text-xs " placeholder="wheater" v-model="vdata2[index]['wheater']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="date" @change="getOther2(index)" id="date" name="date" class="form-control p-1 text-xs " placeholder="date" v-model="vdata2[index]['date']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="text" id="day" name="day" class="form-control p-1 text-xs " placeholder="day" v-model="vdata2[index]['day']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="time" id="from" @change="getLaytimeHrs(index)" name="from" class="form-control p-1 text-xs " placeholder="from" v-model="vdata2[index]['from']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="time" id="to" @change="getLaytimeHrs(index)" name="to" class="form-control p-1 text-xs " placeholder="to" v-model="vdata2[index]['to']" >
                                        </td>
                                        <td class="text-xs">
                                            <select style="width: 60px;" class='form-control' @change="getDescription(index);getLaytimeHrs(index)" v-model="vdata2[index].code">
                                                <option v-for="(item, index) in masterLaytime" :key="index+'masterLaytime'">{{item.code}}</option>
                                            </select>
                                        </td>
                                        <td class="text-xs">
                                            <input type="text" disabled id="description" name="description" class="form-control p-1 text-xs " placeholder="description" v-model="vdata2[index]['description']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="number" @change="getLaytimeHrs(index)" id="rate" name="rate" class="form-control p-1 text-xs " placeholder="rate" v-model="vdata2[index]['rate']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="text" disabled id="laytime_to_count" name="laytime_to_count" class="form-control p-1 text-xs " placeholder="laytime_to_count" v-model="vdata2[index]['laytime_to_count']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="text" disabled id="laytime_not_count" name="laytime_not_count" class="form-control p-1 text-xs " placeholder="laytime_not_count" v-model="vdata2[index]['laytime_not_count']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="text" disabled id="laytime_used_hour" name="laytime_used_hour" class="form-control p-1 text-xs " placeholder="laytime_used_hour" v-model="vdata2[index]['laytime_used_hour']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="text" disabled id="laytime_used_days" name="laytime_used_days" class="form-control p-1 text-xs " placeholder="laytime_used_days" v-model="vdata2[index]['laytime_used_days']" >
                                        </td>
                                        <td class="text-xs">
                                            <input type="text" id="balance_days" name="balance_days" class="form-control p-1 text-xs " placeholder="balance_days" v-model="vdata2[index]['balance_days']" >
                                        </td>
                                        <td class="text-xs">
                                             <div class="tips">
                                                <button @click="updateLine(index)" type="button" class="btn text-xs p-1 btn-sm btn-primary  ">&#9999;</button>
                                                <span class="tipstextL">Update</span>
                                            </div>
                                            <div class="tips">
                                                <button @click="deleteLine(index)" type="button" class="mt-1 btn text-xs px-2 btn-sm btn-danger  ">&#10005;</button>
                                                <span class="tipstextL">Delete</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" @click="newLineItem" >Add New Line</button>
                    </div>
                    <div v-else class="p-4">
                        <br>
                        <p class="p-2 text-center">Data Laytime harus ada untuk menambahkan Laytime Item!</p>
                    </div>
                </div>
               
               </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" >Sales Laytime</h5>
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
                            <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={status:'Draft'}">+ New Laytime</button>
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
                                Id &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Agreed Laycan &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Vessel Name &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Loading Port &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Arrive Date &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Arrive Time &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Cargo Qty &#8593;&#8595;
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
                                    {{index+1}}
                                </td>
                                <td class="text-xs">
                                    {{item.agreed_laycan}}
                                </td>
                                <td class="text-xs">
                                    {{item.vessel_name}}
                                </td>
                                <td class="text-xs">
                                    {{item.loading_commence_date}}
                                </td>
                                <td class="text-xs">
                                    {{formatTgl(item.vessel_arrived_date,'DD-MM-YYYY')}}
                                </td>
                                <td class="text-xs">
                                    {{item.vessel_arrived_time}}
                                </td>
                                <td class="text-xs">
                                    {{item.cargo_qty}}
                                </td>
                                <td class="text-xs" :class="item.status=='Draft'?'text-orange-500':'text-green-500'">
                                    {{item.status}}
                                </td>
                                <!-- <td>
                                    <button type="button" class="btn btn-sm btn-warning rounded-circle " @click="dowloadpdf(item)"><i class="ri-download-line"></i></button>
                                </td> -->
                                <td >
                                    <div v-if="disableInput[index] && item.status=='Draft'" >
                                        <button type="button"  @click="showUpdate(item)" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                            <span class="tipstextL">Edit</span>
                                        </button>
                                        <button type="button" @click="deleteData(item)" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                            <span class="tipstextB">Delete</span>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark text-xs ml-1" @click="disableInput[index]=!disableInput[index];$forceUpdate()">&#9779;</button>
                                    </div>
                                    <div v-else>
                                        <button v-if="item.status=='Draft'" type="button" class="btn btn-sm btn-dark text-xs" @click="disableInput[index]=!disableInput[index];showInsert=false;$forceUpdate()">Edit &#9779;</button>
                                    </div>
                                    <a :href="`<?= site_url() ?>/sales/sales-laytime/pdf?id=${item.id}`" target="__blank" class="my-2">
                                        <button type="button" class="btn btn-sm btn-style2 text-xs p-1 my-2  " >Print PDF</button>
                                    </a>
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
        el:"#sales-laytime",
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
                datanya2:[],
                vdata:{
                    status:'Draft'
                },
                vdata2:[],
                masterLaytime:[],
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
            },
            td2(){
                let that=this;
                let data=this.datanya2;
                return data;
            }
        },  
        methods: {
            getBuyerName(){
                this.vdata.customer_name=this.customer.filter(e=>e.KUNNR==this.vdata.customer_code)[0].NAME1;
                this.$forceUpdate();
            },
            getProductName(){
                this.vdata.product_name=this.product.filter(e=>e.MATNR==this.vdata.product)[0].MAKTX;
                this.$forceUpdate();
            },
            getDescription(index){
                console.log(this.vdata2[index])
                if(this.vdata2[index].code!=''){
                    this.vdata2[index]['description']=this.masterLaytime.filter(e=>e.code==this.vdata2[index].code)[0].description
                    this.vdata2[index]['rate']=this.masterLaytime.filter(e=>e.code==this.vdata2[index].code)[0].rate
                }
               
                this.$forceUpdate();
            },
            getLaytimeHrs(index){
                if(parseInt(this.vdata2[index]['rate'])>0){
                    let to =this.vdata2[index]['to'].length==5?this.vdata2[index]['to']+':00':this.vdata2[index]['to']
                    let from =this.vdata2[index]['from'].length==5?this.vdata2[index]['from']+':00':this.vdata2[index]['from']
                    to=this.timeStringToDecimal(to)
                    from=this.timeStringToDecimal(from)
                    this.vdata2[index]['laytime_to_count']=this.decimalToStringTime((to-from)* parseInt(this.vdata2[index].rate)/100) ;
                }else{
                    this.vdata2[index]['laytime_to_count']='0:00';
                }

                if(parseInt(this.vdata2[index]['rate'])<100){
                    let to =this.vdata2[index]['to'].length==5?this.vdata2[index]['to']+':00':this.vdata2[index]['to']
                    let from =this.vdata2[index]['from'].length==5?this.vdata2[index]['from']+':00':this.vdata2[index]['from']
                    to=this.timeStringToDecimal(to)
                    from=this.timeStringToDecimal(from)
                    this.vdata2[index]['laytime_not_count']=this.decimalToStringTime((to-from) * (1-(parseInt(this.vdata2[index].rate)/100)));
                }else{
                    this.vdata2[index]['laytime_not_count']='0:00';
                }
                this.vdata2[index]['laytime_used_hour']=this.decimalToStringTime(this.timeStringToDecimal(this.vdata2.length>1?this.vdata2[index-1]['laytime_used_hour']:'0:00') + this.timeStringToDecimal(this.vdata2[index]['laytime_to_count']))
                this.vdata2[index]['laytime_used_days']=parseInt(this.vdata2[index]['laytime_used_hour'].split(':')[0])/24 + formulajs.TIMEVALUE(`2022-02-02 00:${this.vdata2[index]['laytime_used_hour'].split(':')[1]}`)
                if(index>0){
                    this.vdata2[index]['balance_days']=this.vdata2[0]['balance_days']-this.vdata2[index]['laytime_used_days'];
                    if(this.vdata2[index]['balance_days']<0){
                        this.vdata['days_dispatch']=0
                        this.vdata['value_dispatch']=0
                        this.vdata['days_demmurage']=this.vdata2[index]['balance_days'];
                        this.vdata['value_demmurage']=this.vdata2[index]['balance_days']*this.vdata['demmurage'];
                    }else{
                        this.vdata['days_demmurage']=0;
                        this.vdata['value_demmurage']=0
                        this.vdata['days_dispatch']=this.vdata2[index]['balance_days'];
                        this.vdata['value_dispatch']=this.vdata2[index]['balance_days']*this.vdata['dispatch'];
                    }
                }else{
                    this.vdata2[index]['balance_days']=this.vdata['laytime_allow_days'];
                }
                // this.updateLine(index);
                this.$forceUpdate();
            },
            getHours(){
                if(this.vdata.type=='Vessel'){
                    setTimeout(() => {
                        this.vdata.laytime_allow_days=parseInt(this.vdata.cargo_qty)/parseInt(this.vdata.loading_rate_qty??1);
                        this.vdata.laytime_allow_hour=(this.vdata.laytime_allow_days*24).toFixed(2)
                    }, 1000);
                }else{
                    this.vdata.laytime_allow_days=7
                    this.vdata.laytime_allow_hour=(this.vdata.laytime_allow_days*24).toFixed(2)
                }
                this.vdata.dispatch=parseInt(this.vdata.demmurage)/2??'-';
                this.$forceUpdate();
            },
            getOther(){
                this.vdata.contract_no=this.shipment.filter(e=>e.shipment_id==this.vdata.shipment_no)[0].contract_no;
                // this.vdata.customer_name=this.sales_order.filter(e=>e.contract_no==this.shipment.filter(e=>e.shipment_id==this.vdata.shipment_no)[0].contract_no)[0].customer_name;
                this.vdata.vessel_name=this.shipment.filter(e=>e.shipment_id==this.vdata.shipment_no)[0].vessel;
                this.vdata.cargo_qty=this.shipment.filter(e=>e.shipment_id==this.vdata.shipment_no)[0].bl_qty;
                this.vdata.cargo_uom=this.shipment.filter(e=>e.shipment_id==this.vdata.shipment_no)[0].uom;
                this.vdata.loading_rate_oum=this.vdata.cargo_uom;
                this.$forceUpdate();
            },
            getOther2(index){
                function getDayName(dateStr, locale)
                {
                    var date = new Date(dateStr);
                    return date.toLocaleDateString(locale, { weekday: 'long' });        
                }

                var dateStr = '05/23/2014';
                this.vdata2[index].day=getDayName(this.vdata2[index].date, "id-ID");
                this.$forceUpdate();
            },
            async newLineItem(){
                this.td2.forEach((e,i)=>{
                    this.updateLine(i);;
                })
                sdb.loadingOn();
                let data = {
                    laytime_id:this.vdata.id
                }
                await axios.post("<?= site_url() ?>"+`/api/sales-laytime-item`,data).then(res=>{
                    this.vdata2=[]
                    sdb.loadingOff();
                    this.getLaytimeItem();
                })
                this.$forceUpdate();
            },
            async getLaytimeItem(){
                if(this.vdata.id){
                    let res = await axios.get("<?= site_url() ?>"+`/api/get/sales-laytime-item?id=${this.vdata.id}`);
                    this.datanya2=res.data;
                    res.data.forEach((e,i)=>{
                        e['item']='00'+(i+1)
                        this.vdata2.push(e)
                    })
                    let res2 = await axios.get("<?= site_url() ?>"+`/api/get/master-laytime`);
                    this.masterLaytime=res2.data;
                    this.$forceUpdate();
                }
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
                this.vdata.shipment_id=this.vdata.shipment_no
                this.vdata[Object.keys(token.data[0])[0]]=Object.values(token.data[0])[0];
                let uploadfile= await this.uploadFile();
                this.vdata.file=uploadfile;
                axios.post("<?= site_url() ?>" + `/api/sales-laytime`,this.vdata).then(async (res)=>{
                    console.log('res',res.data)
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert('Insert data berhasil!','bg-green-400');
                    this.getData();
                    setTimeout(() => {
                        this.modals=true;
                        this.vdata.id=res.data[0].id;
                    }, 500);
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
                this.vdata.shipment_id=this.vdata.shipment_no
                let uploadfile= await this.uploadFile();
                this.vdata.file=uploadfile;
                axios.put("<?= site_url() ?>" + `/api/sales-laytime/${id}`,this.vdata).then(res=>{
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
            async updateLine(item){
                sdb.loadingOn();
                let that=this;
                let data = this.vdata2[item];
                let id=this.vdata2[item].id;
                this.vdata2.updated_by="<?php echo session()->get('username') ?>";
                axios.put("<?= site_url() ?>" + `/api/sales-laytime-item/${id}`,data).then(res=>{
                    this.getLaytimeItem()
                      sdb.loadingOff();
                    // sdb.alert('Update data berhasil!','bg-green-400');
                }).catch(err=>{
                    this.getLaytimeItem()
                    sdb.loadingOff();
                    // sdb.alert('Update data berhasil!','bg-green-400');
                });   
            },
            deleteLine(item){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                let data = this.vdata2[item];
                let id=this.vdata2[item].id;
                axios.delete("<?= site_url() ?>" + `/api/sales-laytime-item/${id}`).then(res=>{
                    this.getLaytimeItem()
                    sdb.alert('Delete berhasil!','bg-green-400');
                }).catch(err=>{
                    this.getLaytimeItem()
                    sdb.alert('Delete berhasil!','bg-green-400');
                });   
            },
            async deleteData(data){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                sdb.loadingOn();
                console.log("<?= site_url() ?>" + `/api/sales-laytime/${data.id}`)
                axios.delete("<?= site_url() ?>" + `/api/sales-laytime/${data.id}`).then(res=>{
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
                this.getLaytimeItem()
                this.$forceUpdate();
            },
            async getData(){
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/get/sales-laytime?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                data.data=data.data.map(e=>{
                    return{
                        ...e,
                        shipment_id:e.shipment_no
                    }
                })
                this.datanya=data.data;
                this.showInsert=false;
                let shipment = await axios.get("<?= site_url() ?>" + `/api/get/sales-shipment?type=sal_laytime`);
                this.shipment=shipment.data;
                this.$forceUpdate();
                setTimeout(() => {
                    this.sortField();
                }, 1000);
            },
            dowloadpdf(item){
                let data={
                    path:item.file
                }
                let name = item.file.split('/')[2]
                axios.post("<?= site_url() ?>" + `/api/download/pdf`,data,{
                    responseType:'blob'
                }).then(response => {
                    const href = URL.createObjectURL(response.data);
                    const link = document.createElement('a');
                    link.href = href;
                    link.setAttribute('download', `Laytime-Doc-0${item.id}.pdf`); //or any other extension
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
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
                    });
                }
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
            jam(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "HH:mm:ss"
                );
            },
            timeStringToDecimal(time){
                var hoursMinutes = time.split(/[.:]/);
                var hours = parseInt(hoursMinutes[0], 10);
                var minutes = hoursMinutes[1] ? parseInt(hoursMinutes[1], 10) : 0;
                return hours + minutes / 60;
            },
            decimalToStringTime(string){
                var decimalTimeString = string;
                var n = new Date(0,0);
                n.setSeconds(+decimalTimeString * 60 * 60);
                return n.toTimeString().slice(0, 8);
            }
            
        },
        mounted() {
            let time="01:39:28"
            function timeStringToDecimal(time){

                var hoursMinutes = time.split(/[.:]/);
                var hours = parseInt(hoursMinutes[0], 10);
                var minutes = hoursMinutes[1] ? parseInt(hoursMinutes[1], 10) : 0;
                return hours + minutes / 60;
            }
            let hasil =timeStringToDecimal(time);
            var decimalTimeString = hasil;
            var n = new Date(0,0);
            n.setSeconds(+decimalTimeString * 60 * 60);
            console.log(n.toTimeString().slice(0, 8));
            document.getElementById('sales-laytime').classList.remove('d-none');
            document.getElementById('pageloading').remove()
            this.bulan = this.formatTgl(new Date(),'MM');
            this.tahun = parseInt(this.formatTgl(new Date(),'YYYY'));
            if (this.bulan && this.tahun) {
                this.periode = `${this.tahun}-${this.bulan}-${this.formatTgl(new Date(),'DD')}`;
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

<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:1000;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:1005;min-width:50vw}
</style>
<main id="main" class="main">
    <div id="FiSalesInv" class="d-none">
        <div class="pagetitle">
            <h1>FI SALES INV</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("undefined/fi-sales-inv") ?>">FI SALES INV</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn">
               <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <table>
                    <tr>
                    <td class="p-2">ATTCH</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="ATTCH" name="ATTCH" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="ATTCH ..." v-model="vdata['ATTCH']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">AUFNR</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="AUFNR" name="AUFNR" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="AUFNR ..." v-model="vdata['AUFNR']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">BELNR</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="BELNR" name="BELNR" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="BELNR ..." v-model="vdata['BELNR']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">BLDAT</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="BLDAT" name="BLDAT" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="BLDAT ..." v-model="vdata['BLDAT']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">BUDAT</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="BUDAT" name="BUDAT" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="BUDAT ..." v-model="vdata['BUDAT']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">CONTRACT_NO</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="CONTRACT_NO" name="CONTRACT_NO" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="CONTRACT_NO ..." v-model="vdata['CONTRACT_NO']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">CPUDT</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="CPUDT" name="CPUDT" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="CPUDT ..." v-model="vdata['CPUDT']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">DOC_NO</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="DOC_NO" name="DOC_NO" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="DOC_NO ..." v-model="vdata['DOC_NO']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_AMNT</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_AMNT" name="FNL_AMNT" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_AMNT ..." v-model="vdata['FNL_AMNT']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_PRICE</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_PRICE" name="FNL_PRICE" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_PRICE ..." v-model="vdata['FNL_PRICE']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY" name="FNL_QTY" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY ..." v-model="vdata['FNL_QTY']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY1</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY1" name="FNL_QTY1" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY1 ..." v-model="vdata['FNL_QTY1']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY10</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY10" name="FNL_QTY10" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY10 ..." v-model="vdata['FNL_QTY10']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY2</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY2" name="FNL_QTY2" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY2 ..." v-model="vdata['FNL_QTY2']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY3</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY3" name="FNL_QTY3" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY3 ..." v-model="vdata['FNL_QTY3']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY4</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY4" name="FNL_QTY4" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY4 ..." v-model="vdata['FNL_QTY4']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY5</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY5" name="FNL_QTY5" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY5 ..." v-model="vdata['FNL_QTY5']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY6</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY6" name="FNL_QTY6" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY6 ..." v-model="vdata['FNL_QTY6']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY7</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY7" name="FNL_QTY7" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY7 ..." v-model="vdata['FNL_QTY7']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY8</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY8" name="FNL_QTY8" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY8 ..." v-model="vdata['FNL_QTY8']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">FNL_QTY9</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="FNL_QTY9" name="FNL_QTY9" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="FNL_QTY9 ..." v-model="vdata['FNL_QTY9']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">KUNNR</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="KUNNR" name="KUNNR" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="KUNNR ..." v-model="vdata['KUNNR']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">MESSAGE_SAP</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="MESSAGE_SAP" name="MESSAGE_SAP" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="MESSAGE_SAP ..." v-model="vdata['MESSAGE_SAP']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">PPH_22</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="PPH_22" name="PPH_22" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="PPH_22 ..." v-model="vdata['PPH_22']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">PPN</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="PPN" name="PPN" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="PPN ..." v-model="vdata['PPN']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">PPN_AMNT</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="PPN_AMNT" name="PPN_AMNT" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="PPN_AMNT ..." v-model="vdata['PPN_AMNT']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">PRCTR</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="PRCTR" name="PRCTR" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="PRCTR ..." v-model="vdata['PRCTR']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">PROJK</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="PROJK" name="PROJK" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="PROJK ..." v-model="vdata['PROJK']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SAL_DISC</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SAL_DISC" name="SAL_DISC" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SAL_DISC ..." v-model="vdata['SAL_DISC']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SGTXT</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SGTXT" name="SGTXT" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SGTXT ..." v-model="vdata['SGTXT']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID" name="SHIPMENT_ID" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID ..." v-model="vdata['SHIPMENT_ID']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID1</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID1" name="SHIPMENT_ID1" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID1 ..." v-model="vdata['SHIPMENT_ID1']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID10</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID10" name="SHIPMENT_ID10" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID10 ..." v-model="vdata['SHIPMENT_ID10']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID2</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID2" name="SHIPMENT_ID2" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID2 ..." v-model="vdata['SHIPMENT_ID2']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID3</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID3" name="SHIPMENT_ID3" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID3 ..." v-model="vdata['SHIPMENT_ID3']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID4</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID4" name="SHIPMENT_ID4" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID4 ..." v-model="vdata['SHIPMENT_ID4']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID5</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID5" name="SHIPMENT_ID5" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID5 ..." v-model="vdata['SHIPMENT_ID5']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID6</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID6" name="SHIPMENT_ID6" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID6 ..." v-model="vdata['SHIPMENT_ID6']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID7</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID7" name="SHIPMENT_ID7" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID7 ..." v-model="vdata['SHIPMENT_ID7']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID8</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID8" name="SHIPMENT_ID8" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID8 ..." v-model="vdata['SHIPMENT_ID8']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_ID9</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_ID9" name="SHIPMENT_ID9" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_ID9 ..." v-model="vdata['SHIPMENT_ID9']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">SHIPMENT_TYPE</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="SHIPMENT_TYPE" name="SHIPMENT_TYPE" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="SHIPMENT_TYPE ..." v-model="vdata['SHIPMENT_TYPE']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">STATUS_SAP</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="STATUS_SAP" name="STATUS_SAP" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="STATUS_SAP ..." v-model="vdata['STATUS_SAP']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">STBLG</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="STBLG" name="STBLG" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="STBLG ..." v-model="vdata['STBLG']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">STJAH</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="STJAH" name="STJAH" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="STJAH ..." v-model="vdata['STJAH']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">TCURR</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="TCURR" name="TCURR" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="TCURR ..." v-model="vdata['TCURR']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">UKURS</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="UKURS" name="UKURS" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="UKURS ..." v-model="vdata['UKURS']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">USNAM</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="USNAM" name="USNAM" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="USNAM ..." v-model="vdata['USNAM']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">XBLNR</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="XBLNR" name="XBLNR" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="XBLNR ..." v-model="vdata['XBLNR']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">ZFBDT</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="ZFBDT" name="ZFBDT" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="ZFBDT ..." v-model="vdata['ZFBDT']" >
                    </td>
                </tr>
        <tr>
                    <td class="p-2">ZTERM</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="ZTERM" name="ZTERM" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="ZTERM ..." v-model="vdata['ZTERM']" >
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
                <h5 class="card-title" >FI SALES INV</h5>
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
                            <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={}">+ New Data</button>
                        </div>
                    </div>
                    <div class="col-sm-3">
                         <form v-if="datanya.length>0" action="<?= site_url() ?>/production/report/download?nama_file=FI_SALES_INV" method="post">
                            <textarea :value="JSON.stringify(td)" v-show="false" type="text" id="datanya" name="datanya" rows="2" placeholder="datanya..." class="form-control md-textarea"  ></textarea>
                            <button type="submit" class="btn btn-sm btn-dark text-xs ">Excel <i class="ri-download-line"></i></button>
                        </form>
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                           <th class="text-xs" style="background:lightgreen;" scope="col">
                ATTCH &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                AUFNR &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                BELNR &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                BLDAT &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                BUDAT &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                CONTRACT_NO &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                CPUDT &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                DOC_NO &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_AMNT &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_PRICE &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY1 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY10 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY2 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY3 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY4 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY5 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY6 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY7 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY8 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                FNL_QTY9 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                KUNNR &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                MESSAGE_SAP &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                PPH_22 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                PPN &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                PPN_AMNT &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                PRCTR &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                PROJK &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SAL_DISC &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SGTXT &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID1 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID10 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID2 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID3 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID4 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID5 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID6 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID7 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID8 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_ID9 &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                SHIPMENT_TYPE &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                STATUS_SAP &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                STBLG &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                STJAH &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                TCURR &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                UKURS &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                USNAM &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                XBLNR &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                ZFBDT &#8593;&#8595;
            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                ZTERM &#8593;&#8595;
            </th>
                            <th style="background:lightgreen;" scope="col">
                                Action 
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index+'form'" >
                               <td class="text-xs">{{item.ATTCH}}</td>
        <td class="text-xs">{{item.AUFNR}}</td>
        <td class="text-xs">{{item.BELNR}}</td>
        <td class="text-xs">{{item.BLDAT}}</td>
        <td class="text-xs">{{item.BUDAT}}</td>
        <td class="text-xs">{{item.CONTRACT_NO}}</td>
        <td class="text-xs">{{item.CPUDT}}</td>
        <td class="text-xs">{{item.DOC_NO}}</td>
        <td class="text-xs">{{item.FNL_AMNT}}</td>
        <td class="text-xs">{{item.FNL_PRICE}}</td>
        <td class="text-xs">{{item.FNL_QTY}}</td>
        <td class="text-xs">{{item.FNL_QTY1}}</td>
        <td class="text-xs">{{item.FNL_QTY10}}</td>
        <td class="text-xs">{{item.FNL_QTY2}}</td>
        <td class="text-xs">{{item.FNL_QTY3}}</td>
        <td class="text-xs">{{item.FNL_QTY4}}</td>
        <td class="text-xs">{{item.FNL_QTY5}}</td>
        <td class="text-xs">{{item.FNL_QTY6}}</td>
        <td class="text-xs">{{item.FNL_QTY7}}</td>
        <td class="text-xs">{{item.FNL_QTY8}}</td>
        <td class="text-xs">{{item.FNL_QTY9}}</td>
        <td class="text-xs">{{item.KUNNR}}</td>
        <td class="text-xs">{{item.MESSAGE_SAP}}</td>
        <td class="text-xs">{{item.PPH_22}}</td>
        <td class="text-xs">{{item.PPN}}</td>
        <td class="text-xs">{{item.PPN_AMNT}}</td>
        <td class="text-xs">{{item.PRCTR}}</td>
        <td class="text-xs">{{item.PROJK}}</td>
        <td class="text-xs">{{item.SAL_DISC}}</td>
        <td class="text-xs">{{item.SGTXT}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID1}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID10}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID2}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID3}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID4}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID5}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID6}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID7}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID8}}</td>
        <td class="text-xs">{{item.SHIPMENT_ID9}}</td>
        <td class="text-xs">{{item.SHIPMENT_TYPE}}</td>
        <td class="text-xs">{{item.STATUS_SAP}}</td>
        <td class="text-xs">{{item.STBLG}}</td>
        <td class="text-xs">{{item.STJAH}}</td>
        <td class="text-xs">{{item.TCURR}}</td>
        <td class="text-xs">{{item.UKURS}}</td>
        <td class="text-xs">{{item.USNAM}}</td>
        <td class="text-xs">{{item.XBLNR}}</td>
        <td class="text-xs">{{item.ZFBDT}}</td>
        <td class="text-xs">{{item.ZTERM}}</td>
        
                                <td>
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
</main><!-- End #main -->
<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    var pages="<?= @$_GET['page'] ?>"
    new Vue({
        el:"#FiSalesInv",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                disableInput:[],
                sortTable:["ZTERM"], // disusun berdasarkan urutan td td td
                modals:false,
                showInsert:false,
                // CUSTOM
                datanya:[],
                vdata:{},
                pages,
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
                
                let token=await axios.post("<?= site_url() ?>" + `/api/test`);
                this.vdata[Object.keys(token.data[0])[0]]=Object.values(token.data[0])[0];
                axios.post("<?= site_url() ?>" + `/api/fi-sales-inv`,this.vdata).then(res=>{
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
                
                axios.put("<?= site_url() ?>" + `/api/fi-sales-inv/${id}`,this.vdata).then(res=>{
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
                axios.delete("<?= site_url() ?>" + `/api/fi-sales-inv/${data.id}`).then(res=>{
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
                this.datanya=[]
                let data = await axios.get("<?= site_url() ?>" + `/api/get/fi-sales-inv`);
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
            formatTgl(tgl,pattern="YYYY-MM-DD") {
                return dateFns.format(
                    new Date(tgl),
                    pattern
                );
            },
        },
        mounted() {
            this.getData();
            document.getElementById('FiSalesInv').classList.remove('d-none');
            this.$forceUpdate();
        },
    })
</script>
<?= $this->endSection() ?>

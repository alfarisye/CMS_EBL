<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->



<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .border-hover{border:1px solid transparent}
    .border-hover:hover{border:1px solid #d0d0d0}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:100000;background:#000;opacity:0.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:100005;min-width:85vw;min-height:50vh;max-height:95vh;overflow:scroll;}
</style>

<main id="main" class="main">
    <div id="qualityReportPage" class="d-none">
        <div class="mb-15">
            <div class="row">
                <div class="col-12">
                    <!-- <img src="<?= base_url("assets/img/ebl.png") ?>"  style="width:120px;height:auto;" class="float-left"> -->
                    <h1 class="pt-8">Quality Report</h1>
                </div>
            </div>
            <div class="pagetitle">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                        <li class="breadcrumb-item active"><a href="<?= site_url("quality_report") ?>">Quality Report</a></li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- MODAL -->
        <div v-if="modal" @click="modal=false" class="modal1"></div>
        <div v-if="modal" class="modal2 ">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn ">
               <form action="" @submit.prevent="submit">
                   <div >
                       <div class="row">
                           <div class="col-12">
                               <p class="font-semibold text-lg">Quality Edit</p>
                           </div>
                           <div class="col-sm-3">
                               <div class="sm-form ">
                                    <label for="Project_location">Project Location</label>
                                    <input type="text" id="Project_location" name="Project_location" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Project_location" v-model="vdata['Project_location']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="Sample_type">Sample Type</label>
                                    <input type="text" id="Sample_type" name="Sample_type" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Sample_type" v-model="vdata['Sample_type']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="Lab_sample_id">Lab Sample ID</label>
                                    <input type="text" id="Lab_sample_id" name="Lab_sample_id" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Lab_sample_id" v-model="vdata['Lab_sample_id']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="Customer_sample_id">Customer Sample ID</label>
                                    <input type="text" id="Customer_sample_id" name="Customer_sample_id" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Customer_sample_id" v-model="vdata['Customer_sample_id']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="tanggal_mulai">Tanggal Mulai</label>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="tanggal_mulai" v-model="vdata['tanggal_mulai']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="tanggal_akhir">Tanggal Akhir</label>
                                    <input type="date" id="tanggal_akhir" name="tanggal_akhir" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="tanggal_akhir" v-model="vdata['tanggal_akhir']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="status">Status</label>
                                    <input type="text" id="status" name="status" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="status" v-model="vdata['status']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="From_meter">From Meter</label>
                                    <input type="text" id="From_meter" name="From_meter" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="From_meter" v-model="vdata['From_meter']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="To_meter">To Meter</label>
                                    <input type="text" id="To_meter" name="To_meter" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="To_meter" v-model="vdata['To_meter']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Thick_meter">Thick Meter</label>
                                    <input type="text" id="Thick_meter" name="Thick_meter" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Thick_meter" v-model="vdata['Thick_meter']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Seam">Seam</label>
                                    <input type="text" id="Seam" name="Seam" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Seam" v-model="vdata['Seam']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Weight_of_Recieved">Weight of Recieved</label>
                                    <input type="text" id="Weight_of_Recieved" name="Weight_of_Recieved" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Weight_of_Recieved" v-model="vdata['Weight_of_Recieved']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Total_moisture">Total Moisture</label>
                                    <input type="text" id="Total_moisture" name="Total_moisture" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Total_moisture" v-model="vdata['Total_moisture']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Moisture_in_sample">Moisture in sample</label>
                                    <input type="text" id="Moisture_in_sample" name="Moisture_in_sample" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Moisture_in_sample" v-model="vdata['Moisture_in_sample']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Ash_content">Ash Content</label>
                                    <input type="text" id="Ash_content" name="Ash_content" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Ash_content" v-model="vdata['Ash_content']" >
                                </div>
                           </div>
                           <div class="col-sm-3">
                                <div class="sm-form ">
                                    <label for="Volatil_matter">Volatil Matter</label>
                                    <input type="text" id="Volatil_matter" name="Volatil_matter" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Volatil_matter" v-model="vdata['Volatil_matter']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Fixed_carbon">Fixed Carbon</label>
                                    <input type="text" id="Fixed_carbon" name="Fixed_carbon" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Fixed_carbon" v-model="vdata['Fixed_carbon']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Total_sulphu">Total Sulphur</label>
                                    <input type="text" id="Total_sulphu" name="Total_sulphu" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Total_sulphu" v-model="vdata['Total_sulphu']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Gross_calorifi_adb">Gross Calorifi ADB</label>
                                    <input type="text" id="Gross_calorifi_adb" name="Gross_calorifi_adb" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Gross_calorifi_adb" v-model="vdata['Gross_calorifi_adb']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Gross_calorifi_ar">Gross Calorifi AR</label>
                                    <input type="text" id="Gross_calorifi_ar" name="Gross_calorifi_ar" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Gross_calorifi_ar" v-model="vdata['Gross_calorifi_ar']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Gross_calorifi_daf">Gross Calorifi DAF</label>
                                    <input type="text" id="Gross_calorifi_daf" name="Gross_calorifi_daf" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Gross_calorifi_daf" v-model="vdata['Gross_calorifi_daf']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Gross_calorifi_dab">Gross Calorifi DAB</label>
                                    <input type="text" id="Gross_calorifi_dab" name="Gross_calorifi_dab" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Gross_calorifi_dab" v-model="vdata['Gross_calorifi_dab']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="RD">RD</label>
                                    <input type="text" id="RD" name="RD" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="RD" v-model="vdata['RD']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="HGI">HGI</label>
                                    <input type="text" id="HGI" name="HGI" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="HGI" v-model="vdata['HGI']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="EQM">EQM</label>
                                    <input type="text" id="EQM" name="EQM" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="EQM" v-model="vdata['EQM']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Sulphur">Sulphur</label>
                                    <input type="text" id="Sulphur" name="Sulphur" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Sulphur" v-model="vdata['Sulphur']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Carbon">Carbon</label>
                                    <input type="text" id="Carbon" name="Carbon" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Carbon" v-model="vdata['Carbon']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Hydrogen">Hydrogen</label>
                                    <input type="text" id="Hydrogen" name="Hydrogen" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Hydrogen" v-model="vdata['Hydrogen']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Nitrogen">Nitrogen</label>
                                    <input type="text" id="Nitrogen" name="Nitrogen" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Nitrogen" v-model="vdata['Nitrogen']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Oxygen">Oxygen</label>
                                    <input type="text" id="Oxygen" name="Oxygen" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Oxygen" v-model="vdata['Oxygen']" >
                                </div>
                           </div>
                           <div class="col-sm-3">
                               <div class="sm-form ">
                                    <label for="SiO2">SiO2</label>
                                    <input type="text" id="SiO2" name="SiO2" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="SiO2" v-model="vdata['SiO2']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="Al2O3">Al2O3</label>
                                    <input type="text" id="Al2O3" name="Al2O3" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Al2O3" v-model="vdata['Al2O3']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="TiO2">TiO2</label>
                                    <input type="text" id="TiO2" name="TiO2" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="TiO2" v-model="vdata['TiO2']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="Fe2O3">Fe2O3</label>
                                    <input type="text" id="Fe2O3" name="Fe2O3" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Fe2O3" v-model="vdata['Fe2O3']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="CaO">CaO</label>
                                    <input type="text" id="CaO" name="CaO" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="CaO" v-model="vdata['CaO']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="MgO">MgO</label>
                                    <input type="text" id="MgO" name="MgO" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="MgO" v-model="vdata['MgO']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="K2O">K2O</label>
                                    <input type="text" id="K2O" name="K2O" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="K2O" v-model="vdata['K2O']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="Na2O">Na2O</label>
                                    <input type="text" id="Na2O" name="Na2O" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Na2O" v-model="vdata['Na2O']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="SO3">SO3</label>
                                    <input type="text" id="SO3" name="SO3" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="SO3" v-model="vdata['SO3']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="P2O5">P2O5</label>
                                    <input type="text" id="P2O5" name="P2O5" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="P2O5" v-model="vdata['P2O5']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="Mn3O4">Mn3O4</label>
                                    <input type="text" id="Mn3O4" name="Mn3O4" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Mn3O4" v-model="vdata['Mn3O4']" >
                                </div>
                               <div class="sm-form ">
                                    <label for="Deformation_reducing">Deformation Reducing</label>
                                    <input type="text" id="Deformation_reducing" name="Deformation_reducing" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Deformation_reducing" v-model="vdata['Deformation_reducing']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Spherical_reducing">Spherical Reducing</label>
                                    <input type="text" id="Spherical_reducing" name="Spherical_reducing" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Spherical_reducing" v-model="vdata['Spherical_reducing']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Hemishare_reducing">Hemishare Reducing</label>
                                    <input type="text" id="Hemishare_reducing" name="Hemishare_reducing" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Hemishare_reducing" v-model="vdata['Hemishare_reducing']" >
                                </div>
                           </div>
                           <div class="col-sm-3">
                                <div class="sm-form ">
                                    <label for="Flow_reducing">Flow Reducing</label>
                                    <input type="text" id="Flow_reducing" name="Flow_reducing" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Flow_reducing" v-model="vdata['Flow_reducing']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Deformation_oxidicing">Deformation Oxidicing</label>
                                    <input type="text" id="Deformation_oxidicing" name="Deformation_oxidicing" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Deformation_oxidicing" v-model="vdata['Deformation_oxidicing']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Spherical_oxidicing">Spherical Oxidicing</label>
                                    <input type="text" id="Spherical_oxidicing" name="Spherical_oxidicing" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Spherical_oxidicing" v-model="vdata['Spherical_oxidicing']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Hemishare_oxidicing">Hemishare Oxidicing</label>
                                    <input type="text" id="Hemishare_oxidicing" name="Hemishare_oxidicing" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Hemishare_oxidicing" v-model="vdata['Hemishare_oxidicing']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Flow_oxidicing">Flow Oxidicing</label>
                                    <input type="text" id="Flow_oxidicing" name="Flow_oxidicing" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Flow_oxidicing" v-model="vdata['Flow_oxidicing']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Sudiom">Sudiom</label>
                                    <input type="text" id="Sudiom" name="Sudiom" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Sudiom" v-model="vdata['Sudiom']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Potasium">Potasium</label>
                                    <input type="text" id="Potasium" name="Potasium" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Potasium" v-model="vdata['Potasium']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="As">As</label>
                                    <input type="text" id="As" name="As" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="As" v-model="vdata['As']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Hg">Hg</label>
                                    <input type="text" id="Hg" name="Hg" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="As" v-model="vdata['Hg']" >
                                </div>
                                <div class="sm-form ">
                                    <label for="Se">Se</label>
                                    <input type="text" id="Se" name="Se" class="form-control p-1 rounded-sm shadow-sm text-xs" placeholder="Se" v-model="vdata['Se']" >
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-sm btn-success ml-2 mt-2 text-lg "
                                @click="aksi='update'"
                                >Simpan</button>
                                <button type="submit" class="btn btn-sm btn-danger ml-2  mt-2 text-lg" 
                                @click="aksi='delete'"
                                >Delete</button>
                           </div>
                       </div>
                   </div>
               </form>
            </div>
        </div>
            <!-- MODAL -->
        <div class="card">
            <div class="card-body">
                <div class="row py-2" >
                    <div class="col-sm-12">
                        <p class="font-semibold">Date Range : </p>
                        <div class="row">
                            <div class="col-sm-3">
                                <input type="date" id="tgl_awal" name="tgl_awal" class="form-control p-1 text-xs rounded-sm shadow-sm" placeholder="tgl_awal" v-model="tgl_awal" >
                            </div>
                            <div class="col-sm-1">
                                <span class="text-xs font-semibold">
                                    s/d 
                                </span>
                            </div>
                            <div class="col-sm-3">
                                <input type="date" id="tgl_akhir" name="tgl_akhir" class="form-control p-1 text-xs rounded-sm shadow-sm" placeholder="tgl_akhir" v-model="tgl_akhir" >
                            </div>
                            <div class="col-2">
                                <button type="button"  class="btn btn-sm btn-style2 " @click="getData">Cari</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <p class="font-semibold">View</p>
                        <select class='form-control' v-model="perPage" @change="page=1">
                            <option>5</option>
                            <option>10</option>
                            <option>50</option>
                            <option>100</option>
                            <option value="100000">Semua</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <div class="sm-form mt-10">
                            <input type="text" 
                            @change="page=1"
                            id="search" name="search" class="form-control p-2 text-xs rounded-lg " placeholder="search" v-model="search" >
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <br>
                        <div class="text-center p-3" >
                            <form class="d-inline" v-if="datanya.length>0" :action="'<?= site_url("/production/report/download?nama_file=production report") ?>'" method="post">
                                <div class="sm-form " v-show="false">
                                    <input type="text" id="datanya" name="datanya" class="form-control p-2 rounded-lg shadow" placeholder="datanya" v-model="tdDatanya" >
                                </div>
                                <button type="submit"  class="btn btn-sm btn-dark  " ><i class="ri-download-line"></i></button>
                            </form>
                            <!-- <a v-if="datanya.length>0" :href="'<?= site_url("/production/report/download?nama_file=production report") ?>'+`&data=${JSON.stringify(td)}`">
                                <button type="button"  class="btn btn-sm btn-dark  " ><i class="ri-download-line"></i></button>
                            </a> -->
                            <a href="<?= site_url("quality-report/upload") ?>">
                                <button type="button"  class="btn btn-sm btn-style2  ml-3 my-1" >+Upload Quality</button>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-bordered ">
                        <tr>
                            <th scope="col" rowspan="2">
                                # 
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Id 
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Project Location 
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Sample type 
                            </th>
                            <th scope="col " rowspan="2" class="text-xs">
                                Lab Sample ID 
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Customer Sample ID 
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Tanggal Mulai 
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Tanggal Akhir
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                status
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                From Meter
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                To Meter
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Thick Meter
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Seam
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Weight Of Received
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Total Moisture
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Moisture in Sample
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Ash Content
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Volatil Matter
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Fixed Carbon
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Total Sulphu
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Gross Calorifi ADB
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Gross Alorifi AR
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Gross Calorifi DAF
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                Gross Calorifi DAB
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                RD
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                HGI
                            </th>
                            <th scope="col" rowspan="2" class="text-xs">
                                EQM
                            </th>
                            <!--  -->
                            <td scope="col" colspan="5" class="text-center font-semibold text-xs">Ultimate Analysis</td>
                            <td scope="col" colspan="11" class="text-center font-semibold text-xs">Ultimate Analysis</td>
                            <td scope="col" colspan="4" class="text-center font-semibold text-xs">Ash Fusion Temperature (Reducing)</td>
                            <td scope="col" colspan="4" class="text-center font-semibold text-xs">Ash Fusion Temperature (Oxidicing)</td>
                            <td scope="col" colspan="2" class="text-center font-semibold text-xs">Water Soluble Alkalies</td>
                            <td scope="col" colspan="3" class="text-center font-semibold text-xs">Trace Element</td>
                            <th scope="col" rowspan="2" class="text-xs">
                                Aksi
                            </th>
                        </tr>
                        <tr>
                            <th scope="col" class="text-xs">
                                Sulphur
                            </th>
                            <th scope="col" class="text-xs">
                                Carbon
                            </th>
                            <th scope="col" class="text-xs">
                                Hydrogen
                            </th>
                            <th scope="col" class="text-xs">
                                Nitrogen
                            </th>
                            <th scope="col" class="text-xs">
                                Oxygen
                            </th>
                            <th scope="col" class="text-xs">
                                SiO2
                            </th>
                            <th scope="col" class="text-xs">
                                Al2O3
                            </th>
                            <th scope="col" class="text-xs">
                                TiO2
                            </th>
                            <th scope="col" class="text-xs">
                                Fe2O3
                            </th>
                            <th scope="col" class="text-xs">
                                CaO
                            </th>
                            <th scope="col" class="text-xs">
                                MgO
                            </th>
                            <th scope="col" class="text-xs">
                                K2O
                            </th>
                            <th scope="col" class="text-xs">
                                Na2O
                            </th>
                            <th scope="col" class="text-xs">
                                SO3
                            </th>
                            <th scope="col" class="text-xs">
                                P2O5
                            </th>
                            <th scope="col" class="text-xs">
                                Mn3O4
                            </th>
                            <th scope="col" class="text-xs">
                                Deformation Reducing
                            </th>
                            <th scope="col" class="text-xs">
                                Spherical Reducing
                            </th>
                            <th scope="col" class="text-xs">
                                Hemishare Reducing
                            </th>
                            <th scope="col" class="text-xs">
                                Flow Reducing
                            </th>
                            <th scope="col" class="text-xs">
                                Deformation Oxidicing
                            </th>
                            <th scope="col" class="text-xs">
                                Spherical Oxidicing
                            </th>
                            <th scope="col" class="text-xs">
                                Hemishare Oxidicing
                            </th>
                            <th scope="col" class="text-xs">
                                Flow Oxidicing
                            </th>
                            <th scope="col" class="text-xs">
                                Sudiom
                            </th>
                            <th scope="col" class="text-xs">
                                Potasium
                            </th>
                            <th scope="col" class="text-xs">
                                As
                            </th>
                            
                            <th scope="col" class="text-xs">
                                Hg
                            </th>
                            <th scope="col" class="text-xs">
                                Se
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index" >
                                <td :for="item.sample_id">
                                    <input type="checkbox" :id="item.sample_id" :value="item.id" v-model="checkedData">
                                </td>
                                <td class="text-xs">{{item.id}}</td>
                                <td class="text-xs" >
                                    {{item.Project_location}}
                                </td>
                                <td class="text-xs" >
                                    {{item.Sample_type}}
                                </td>
                                <td class="text-xs" >
                                    {{item.Lab_sample_id}}
                                </td>
                                <td class="text-xs" >
                                    {{item.Customer_sample_id}}
                                </td>
                                <td class="text-xs" >
                                    {{formatTgl(item.tanggal_mulai,'DD-MM-YYYY')}}
                                </td>
                                <td class="text-xs" >
                                    {{formatTgl(item.tanggal_akhir,'DD-MM-YYYY')}}
                                </td>
                                <td class="text-xs" >
                                    {{item.status}}
                                </td>
                                <td class="text-xs" >
                                    {{item.From_meter}}
                                </td>
                                <td class="text-xs" >
                                    {{item.To_meter}}
                                </td>
                                <td class="text-xs">
                                    {{item.Thick_meter}}
                                </td>
                                <td class="text-xs">
                                    {{item.Seam}}
                                </td>
                                <td class="text-xs">
                                    {{item.Weight_of_Recieved}}
                                </td>
                                <td class="text-xs">
                                    {{item.Total_moisture}}
                                </td>
                                <td class="text-xs">
                                    {{item.Moisture_in_sample}}
                                </td>
                                <td class="text-xs">
                                    {{item.Ash_content}}
                                </td>
                                <td class="text-xs">
                                    {{item.Volatil_matter}}
                                </td>
                                <td class="text-xs">
                                    {{item.Fixed_carbon}}
                                </td>
                                <td class="text-xs">
                                    {{item.Total_sulphu}}
                                </td>
                                <td class="text-xs">
                                    {{item.Gross_calorifi_adb}}
                                </td>
                                <td class="text-xs">
                                    {{item.Gross_calorifi_ar}}
                                </td>
                                <td class="text-xs">
                                    {{item.Gross_calorifi_daf}}
                                </td>
                                <td class="text-xs">
                                    {{item.Gross_calorifi_dab}}
                                </td>
                                <td class="text-xs">
                                    {{item.RD}}
                                </td>
                                <td class="text-xs">
                                    {{item.HGI}}
                                </td>
                                <td class="text-xs">
                                    {{item.EQM}}
                                </td>
                                <td class="text-xs">
                                    {{item.Sulphur}}
                                </td>
                                <td class="text-xs">
                                    {{item.Carbon}}
                                </td>
                                <td class="text-xs">
                                    {{item.Hydrogen}}
                                </td>
                                <td class="text-xs">
                                    {{item.Nitrogen}}
                                </td>
                                <td class="text-xs">
                                    {{item.Oxygen}}
                                </td>
                                <td class="text-xs">
                                    {{item.SiO2}}
                                </td>
                                <td class="text-xs">
                                    {{item.Al2O3}}
                                </td>
                                <td class="text-xs">
                                    {{item.TiO2}}
                                </td>
                                <td class="text-xs">
                                    {{item.Fe2O3}}
                                </td>
                                <td class="text-xs">
                                    {{item.CaO}}
                                </td>
                                <td class="text-xs">
                                    {{item.MgO}}
                                </td>
                                <td class="text-xs">
                                    {{item.K2O}}
                                </td>
                                <td class="text-xs">
                                    {{item.Na2O}}
                                </td>
                                <td class="text-xs">
                                    {{item.SO3}}
                                </td>
                                <td class="text-xs">
                                    {{item.P2O5}}
                                </td>
                                <td class="text-xs">
                                    {{item.Mn3O4}}
                                </td>
                                <td class="text-xs">
                                    {{item.Deformation_reducing}}
                                </td>
                                <td class="text-xs">
                                    {{item.Spherical_reducing}}
                                </td>
                                <td class="text-xs">
                                    {{item.Hemishare_reducing}}
                                </td>
                                <td class="text-xs">
                                    {{item.Flow_reducing}}
                                </td>
                                <td class="text-xs">
                                    {{item.Deformation_oxidicing}}
                                </td>
                                <td class="text-xs">
                                    {{item.Spherical_oxidicing}}
                                </td>
                                <td class="text-xs">
                                    {{item.Hemishare_oxidicing}}
                                </td>
                                <td class="text-xs">
                                    {{item.Flow_oxidicing}}
                                </td>
                                <td class="text-xs">
                                    {{item.Sudiom}}
                                </td>
                                <td class="text-xs">
                                    {{item.Potasium}}
                                </td>
                                <td class="text-xs">
                                    {{item.As}}
                                </td>
                                <td class="text-xs">
                                    {{item.Hg}}
                                </td>
                                <td class="text-xs">
                                    {{item.Se}}
                                </td>
                                <td>
                                    <button type="button" @click="modal=!modal;vdata=item;$forceUpdate();" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                        <span class="tipstextB">Edit</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button v-if="checkedData.length>0" type="button" class="btn btn-sm btn-danger " @click="checkData">Delete All Checked</button>
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
        el:"#qualityReportPage",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                tdDatanya:'',
                checkedData:[],
                disableInput:[],
                sortTable:[null,'id','sample_id','tgl_awal','tgl_akhir',"kategori",'status_progress',"TM_arb","M_adb","Ash_adb","VM_adb","FC_adb","TS_adb","CV_adb","CV_arb","CV_daf","CV_db"], // disusun berdasarkan urutan td td td
                modal:false,
                showInsert:false,
                // CUSTOM
                tgl_awal:'',
                tgl_akhir:'',
                aksi:'insert',
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
                this.totalPage = Math.ceil(data.length/this.perPage);
                data=data.slice((this.page-1)*this.perPage,this.page*this.perPage);
                this.tdDatanya=JSON.stringify(data);
                return data;
            }
        },  
        methods: {
            async checkData(){
                if(!confirm('Are you sure ? this list of data will be deleted.'))return;
                sdb.loadingOn()
                for(let i=0;i<this.checkedData.length;i++){
                    await this.deleteData2({id:this.checkedData[i]});
                }
                sdb.alert('Delete Success!','bg-green-400');
                this.getData();
                sdb.loadingOff()
            },
            showField(vmodel,e){
                this.modal=true;
                this.target.vmodel=vmodel;
                this.target.key=e.target.name;
                this.$forceUpdate();
            },
            async submit(){
                if(this.aksi=='update'){
                    this.updateData(this.vdata);
                }else{
                    this.deleteData(this.vdata);
                }
            },
            async updateData(data){
                if(!confirm('Apakah yakin Update data?'))return;
                sdb.loadingOn();
                this.modal=false;
                axios.put("<?= site_url() ?>" + `/api/quality-report/${data.id}`,data).then(res=>{
                    sdb.loadingOff();
                    sdb.alert('Update data berhasil!','bg-green-400');
                    // this.getData();
                    console.log(res)
                }).catch(err=>{
                    sdb.loadingOff();
                    sdb.alert('Update data gagal!');
                });   
            },
            async deleteData(data){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                sdb.loadingOn();
                this.modal=false;
                axios.delete("<?= site_url() ?>" + `/api/quality-report/${data.id}`).then(res=>{
                    sdb.loadingOff();
                    sdb.alert('Delete data berhasil!','bg-green-400');
                    this.getData();
                    console.log(res)
                }).catch(err=>{
                    sdb.loadingOff();
                    sdb.alert('Delete data gagal!');
                });  
            },
            async deleteData2(data){
                this.modal=false;
                await axios.delete("<?= site_url() ?>" + `/api/quality-report/${data.id}`) 
            },
            async getData(){
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/quality-report?tgl_awal=${this.tgl_awal}&tgl_akhir=${this.tgl_akhir}`);
                console.log('datanya',data)
                if(data.data){
                    if(data.data.length==0){
                        sdb.alert('Data Tidak ditemukan!');
                        return;
                    }
                }else{
                    sdb.alert('Data Tidak ditemukan!');
                    return;
                }
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
            formatTgl(tgl,pattern="YYYY-MM-DD") {
                return dateFns.format(
                    new Date(tgl),
                    pattern
                );
            },
           
        },
        mounted() {
            var date = new Date();
            var firstDay = new Date(date.getFullYear(), 0, 1);
            this.tgl_awal=this.format(firstDay);
            this.tgl_akhir=this.format(new Date());
            setTimeout(() => {
                this.getData();
            }, 1000);
            document.getElementById('qualityReportPage').classList.remove('d-none');
            document.getElementById('pageloading').remove()
        },
    })
</script>

<?= $this->endSection() ?>
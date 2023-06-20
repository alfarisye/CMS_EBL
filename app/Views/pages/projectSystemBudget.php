<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Project System Budget</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Project System</a></li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("projectSystem/budget") ?>">Budget</a></li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="row">
                <h4 class="text-center">Project System Budget</h4>
            </div>
            <div class="col-lg-12" id="budget">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-3">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <br>
                                        <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm">
                                                    <select onchange="MTDtypefetchData()" class="form-control" name="" id="MTDtypeFilter">
                                                        <!-- <option value="">--Pilih Expenditure--</option> -->
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,0,1) == "0"){echo("selected");}?>value="opex">OPEX</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,0,1) == "1"){echo("selected");}?> value="capex">CAPEX</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm">
                                                    <select onchange="MTDyearfetchData()" class="form-control" name="" id="MTDyearFilter">
                                                        <?php 
                                                        foreach ($year as $row) { ?>
                                                            <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,1,2) == substr($row['year'],2,2)){echo("selected");}?> value="<?= $row['year']?>"><?= $row['year']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-title text-center">
                                            MTD (in Million)
                                        </div>
                                        <!-- <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm">
                                                    <select onchange="MTDtypefetchData()" class="form-control" name="" id="MTDtypeFilter">
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,0,1) == "0"){echo("selected");}?>value="opex">OPEX</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,0,1) == "1"){echo("selected");}?> value="capex">CAPEX</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm">
                                                    <select onchange="MTDyearfetchData()" class="form-control" name="" id="MTDyearFilter">
                                                        <?php 
                                                        foreach ($year as $row) { ?>
                                                            <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,1,2) == substr($row['year'],2,2)){echo("selected");}?> value="<?= $row['year']?>"><?= $row['year']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div> 
                                        </div> -->
                                            <script>   
                                            // function MTDtypefetchData() {
                                            //     var x = document.getElementById("MTDtypeFilter").value;
                                            //     <?php
                                            //         if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                                            //             $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
                                            //         }
                                            //         else{
                                            //             $fullurl = "xxxxxxxxxxxxxxxx";
                                            //         }                           
                                            //     ?>
                                            //     if (x === "opex"){
                                            //         location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=0".substr($fullurl?? false,1)."#MTDtypeFilter" ?>"
                                            //     }
                                            //     else{
                                            //         location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=1".substr($fullurl?? false,1)."#MTDtypeFilter" ?>"
                                            //     }
                                            // }

                                            // function MTDyearfetchData() {
                                            //     var x = document.getElementById("MTDyearFilter").value;
                                            //     <?php
                                            //         if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                                            //             $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
                                            //         }
                                            //         else{
                                            //             $fullurl = "xxxxxxxxxxxxxxxx";
                                            //         }                           
                                            //     ?>
                                            //     y = x.substring(2, 4);
                                            //     location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,1)?>"+y+"<?= substr($fullurl?? false,3)."#MTDtypeFilter" ?>"
                                            // }
                                            </script>

                                        <div id="MTDchart"></div>
                                        <!--===========================================================-->
                                        <div class="card-title text-center">
                                            YTD (in Million)
                                        </div>
                                        <!-- <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm">
                                                    <select onchange="YTDtypefetchData()" class="form-control" name="" id="YTDtypeFilter">
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,3,1) == "0"){echo("selected");}?> value="opex">OPEX</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,3,1) == "1"){echo("selected");}?> value="capex">CAPEX</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm">
                                                    <select onchange="YTDyearfetchData()" class="form-control" name="" id="YTDyearFilter">
                                                        <?php foreach ($year as $row) { ?>
                                                            <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,4,2) == substr($row['year'],2,2)){echo("selected");}?> value="<?= $row['year']?>"><?= $row['year']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div> 
                                        </div>-->
                                        <script>   
                                            // function YTDtypefetchData() {
                                            //     var x = document.getElementById("YTDtypeFilter").value;
                                            //     <?php
                                            //         if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                                            //             $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
                                            //         }
                                            //         else{
                                            //             $fullurl = "xxxxxxxxxxxxxxxx";
                                            //         }                           
                                            //     ?>
                                            //     if (x === "opex"){
                                            //         location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,3)?>"+"0"+"<?= substr($fullurl?? false,4)."#YTDtypeFilter" ?>"
                                            //     }
                                            //     else{
                                            //         location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,3)?>"+"1"+"<?= substr($fullurl?? false,4)."#YTDtypeFilter" ?>"
                                            //     }
                                                
                                            // }

                                            // function YTDyearfetchData() {
                                            //     var x = document.getElementById("YTDyearFilter").value;
                                            //     <?php
                                            //         if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                                            //             $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
                                            //         }
                                            //         else{
                                            //             $fullurl = "xxxxxxxxxxxxxxxx";
                                            //         }                           
                                            //     ?>
                                            //     y = x.substring(2, 4);
                                            //     console.log(y);
                                            //     location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,4)?>"+y+"<?= substr($fullurl?? false,6)."#YTDtypeFilter" ?>"
                                            // }
                                        </script>
                                        <div id="YTDchart"></div>
                                        <!--===========================================================-->
                                        <div class="card-title text-center">
                                            Cost/MT MTD (in Million)
                                        </div>
                                        <!-- <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm">
                                                    <select onchange="CMMTDtypefetchData()" class="form-control" name="" id="cmmtdtypeFilter">
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,6,1) == "0"){echo("selected");}?> value="opex">OPEX</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,6,1) == "1"){echo("selected");}?> value="capex">CAPEX</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm">
                                                    <select onchange="CMMTDyearfetchData()" class="form-control" name="" id="cmmtdyearFilter">
                                                        <?php foreach ($year as $row) { ?>
                                                            <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,7,2) == substr($row['year'],2,2)){echo("selected");}?> value="<?= $row['year']?>"><?= $row['year']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div> -->
                                        <script>   
                                            // function CMMTDtypefetchData() {
                                            //     var x = document.getElementById("cmmtdtypeFilter").value;
                                            //     <?php
                                            //         if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                                            //             $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
                                            //         }
                                            //         else{
                                            //             $fullurl = "xxxxxxxxxxxxxxxx";
                                            //         }                           
                                            //     ?>
                                            //     if (x === "opex"){
                                            //         location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,6)?>"+"0"+"<?= substr($fullurl?? false,7)."#cmmtdtypeFilter" ?>"
                                            //     }
                                            //     else{
                                            //         location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,6)?>"+"1"+"<?= substr($fullurl?? false,7)."#cmmtdtypeFilter" ?>"
                                            //     }
                                                
                                            // }

                                            // function CMMTDyearfetchData() {
                                            //     var x = document.getElementById("cmmtdyearFilter").value;
                                            //     <?php
                                            //         if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                                            //             $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
                                            //         }
                                            //         else{
                                            //             $fullurl = "xxxxxxxxxxxxxxxx";
                                            //         }                           
                                            //     ?>
                                            //     y = x.substring(2, 4);
                                            //     console.log(y);
                                            //     location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,7)?>"+y+"<?= substr($fullurl?? false,9)."#cmmtdtypeFilter" ?>"
                                            // }
                                        </script>

                                        <div id="CMMTDchart"></div>
                                        <!--===========================================================-->
                                        <div class="card-title text-center">
                                            Cost/MT YTD (in Million)
                                        </div>
                                        <!-- <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm">
                                                    <select onchange="CMYTDtypefetchData()" class="form-control" name="" id="cmytdtypeFilter">
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,9,1) == "0"){echo("selected");}?> value="opex">OPEX</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,9,1) == "1"){echo("selected");}?> value="capex">CAPEX</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm">
                                                    <select onchange="CMYTDyearfetchData()" class="form-control" name="" id="cmytdyearFilter">
                                                        <?php foreach ($year as $row) { ?>
                                                            <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,10,2) == substr($row['year'],2,2)){echo("selected");}?> value="<?= $row['year']?>"><?= $row['year']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div> -->
                                        <script>   
                                            // function CMYTDtypefetchData() {
                                            //     var x = document.getElementById("cmytdtypeFilter").value;
                                            //     <?php
                                            //         if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                                            //             $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
                                            //         }
                                            //         else{
                                            //             $fullurl = "xxxxxxxxxxxxxxxx";
                                            //         }                           
                                            //     ?>
                                            //     if (x === "opex"){
                                            //         location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,9)?>"+"0"+"<?= substr($fullurl?? false,10)."#cmytdtypeFilter" ?>"
                                            //     }
                                            //     else{
                                            //         location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,9)?>"+"1"+"<?= substr($fullurl?? false,10)."#cmytdtypeFilter" ?>"
                                            //     }
                                                
                                            // }

                                            // function CMYTDyearfetchData() {
                                            //     var x = document.getElementById("cmytdyearFilter").value;
                                            //     <?php
                                            //         if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                                            //             $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
                                            //         }
                                            //         else{
                                            //             $fullurl = "xxxxxxxxxxxxxxxx";
                                            //         }                           
                                            //     ?>
                                            //     y = x.substring(2, 4);
                                            //     console.log(y);
                                            //     location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,10)?>"+y+"<?= substr($fullurl?? false,12)."#cmytdtypeFilter" ?>"
                                            // }
                                        </script>
                                        <div id="CMYTDchart"></div>
                                        <!--===========================================================-->
                                        <hr>
                                        <div class="card-title text-center">
                                        <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm">
                                                    <select onchange="WBSLV4fetchData()" class="form-control" name="" id="WBSLV4yearFilter">
                                                        <?php foreach ($year as $row) { ?>
                                                            <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,13,2) == substr($row['year'],2,2)){echo("selected");}?> value="<?= $row['year']?>"><?= $row['year']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <div class="col-sm">
                                                    <select onchange="WBSMonthFilter()" class="form-control" name="" id="WBSmonthFilter">
                                                        <option value="00">All Months</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "01"){echo("selected");}?> value="01">January</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "02"){echo("selected");}?> value="02">February</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "03"){echo("selected");}?> value="03">March</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "04"){echo("selected");}?> value="04">April</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "05"){echo("selected");}?> value="05">May</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "06"){echo("selected");}?> value="06">June</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "07"){echo("selected");}?> value="07">July</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "08"){echo("selected");}?> value="08">Augustus</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "09"){echo("selected");}?> value="09">September</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "10"){echo("selected");}?> value="10">October</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "11"){echo("selected");}?> value="11">November</option>
                                                        <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) == "12"){echo("selected");}?> value="12">December</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <br/>
                                            Actual Cost/MT (OPEX WBS Level 4)
                                            <br>(In Millions)
                                        </div>
                                        <!-- <div class="card col-sm-2">
                                            <select onchange="WBSLV4fetchData()" class="form-control" name="" id="WBSLV4yearFilter">
                                                <?php foreach ($year as $row) { ?>
                                                    <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,13,2) == substr($row['year'],2,2)){echo("selected");}?> value="<?= $row['year']?>"><?= $row['year']?></option>
                                                <?php }?>
                                            </select>
                                        </div> -->
                                        <script>
                                            // function WBSLV4fetchData() {
                                            //     var x = document.getElementById("WBSLV4yearFilter").value;
                                            //     <?php
                                            //         if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                                            //             $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
                                            //         }
                                            //         else{
                                            //             $fullurl = "xxxxxxxxxxxxxxxx";
                                            //         }                           
                                            //     ?>
                                            //     y = x.substring(2, 4);
                                            //     console.log(y);
                                            //     location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,12)?>"+y+"<?= substr($fullurl?? false,14)."#WBSLV4yearFilter" ?>"
                                            // }
                                        </script>
                                        <!--===========================================================-->
                                        <div class="card-title">
                                            <table class="table table-bordered" style="font-size:12px">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>WBS Code</th>
                                                        <th>Product</th>
                                                        <th>Kalkulasi</th>
                                                        <th>WBS Code</th>
                                                        <th>Product</th>
                                                        <th>Kalkulasi</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Periode</th>
                                                        <th>AB3.11-01.00.00.00 (Cost Mining)</th>
                                                        <th>Raw Coal</th>
                                                        <th>Cost/MT</th>
                                                        <th>AB3.11-02.00.00.00</th>
                                                        <th>Crush Coal</th>
                                                        <th>Cost/MT</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($table1 as $row) {
                                                        echo '<tr>';
                                                        echo '<td>'.$row[0].'</td>';
                                                        echo '<td> Rp'.number_format($row[1],2,',','.').'</td>';
                                                        echo '<td>'.number_format($row[2],2,',','.').'</td>';
                                                        echo '<td> Rp'.number_format($row[3],2,',','.').'</td>';
                                                        echo '<td> Rp'.number_format($row[4],2,',','.').'</td>';
                                                        echo '<td>'.number_format($row[5],2,',','.').'</td>';
                                                        echo '<td> Rp'.number_format($row[6],2,',','.').'</td>';
                                                        echo '</tr>';
                                                    }?>
                                                <tfoot>
                                                <?php
                                                    foreach ($totaltable1 as $row) {
                                                        if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "01"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "02" 
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "03"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "04"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "05"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "06"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "07"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "08"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "09"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "10"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "11"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "12"
                                                            ){
                                                                echo '<tr>';
                                                                echo '<td>'.$row[0].'</td>';
                                                                echo '<td> Rp'.number_format($row[1],2,',','.').'</td>';
                                                                echo '<td>'.number_format($row[2],2,',','.').'</td>';
                                                                echo '<td> Rp'.number_format($row[3],2,',','.').'</td>';
                                                                echo '<td> Rp'.number_format($row[4],2,',','.').'</td>';
                                                                echo '<td>'.number_format($row[5],2,',','.').'</td>';
                                                                echo '<td> Rp'.number_format($row[6],2,',','.').'</td>';
                                                                echo '</tr>';
                                                            }
                                                    }?>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <div class="card-title text-center">
                                            Actual Cost/MT Raw Coal (OPEX) Per Contractor
                                            <br>(In Millions)

                                        </div>
                                        <!-- <div class="card col-sm-2">
                                            <select onchange="WBSpercontractorfetchData()" class="form-control" name="" id="WBSpercontractoryearFilter">
                                            <?php foreach ($year as $row) { ?>
                                                    <option <?php if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,14,2) == substr($row['year'],2,2)){echo("selected");}?> value="<?= $row['year']?>"><?= $row['year']?></option>
                                                <?php }?>
                                            </select> 
                                        </div>-->
                                            <script>
                                                // function WBSpercontractorfetchData() {
                                                //     var x = document.getElementById("WBSpercontractoryearFilter").value;
                                                //     <?php
                                                //         if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                                                //             $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
                                                //         }
                                                //         else{
                                                //             $fullurl = "xxxxxxxxxxxxxxxx";
                                                //         }                           
                                                //     ?>
                                                //     y = x.substring(2, 4);
                                                //     console.log(y);
                                                //     location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,14)?>"+y+"#WBSpercontractoryearFilter"
                                                // }
                                            </script>
                                        
                                        <div class="card-title center">
                                            <table class="table" style="font-size:12px">
                                                <thead>
                                                    <tr>
                                                        <th>Contractor</th>
                                                        <th>GMT</th>
                                                        <th>CK</th>
                                                        <th>HRS</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr>
                                                        <th>WBS</th>
                                                        <th>AB3.11-01.01.01.00</th>
                                                        <th>AB3.11-01.01.02.00</th>
                                                        <th>AB3.11-01.01.03.00</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($table2 as $row) {
                                                        echo '<tr>';
                                                        echo '<td>'.$row[0].'</td>';
                                                        //if (str_contains($row[0], "QTY"))
                                                        if(preg_match("/QTY/i", $row[0])){
                                                            echo '<td>'.number_format($row[1],2,',','.').'</td>';
                                                            echo '<td>'.number_format($row[2],2,',','.').'</td>';
                                                            echo '<td>'.number_format($row[3],2,',','.').'</td>';
                                                            echo '<td>'.number_format($row[4],2,',','.').'</td>';
                                                        }
                                                        else{
                                                            echo '<td>Rp'.number_format($row[1],2,',','.').'</td>';
                                                            echo '<td>Rp'.number_format($row[2],2,',','.').'</td>';
                                                            echo '<td>Rp'.number_format($row[3],2,',','.').'</td>';
                                                            echo '<td>Rp'.number_format($row[4],2,',','.').'</td>';
                                                        } 
                                                        echo '</tr>';
                                                    }?>
                                                </tbody>
                                                <tfoot>
                                                <?php
                                                    foreach ($totaltable2 as $row) {
                                                        if(substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "01"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "02" 
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "03"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "04"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "05"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "06"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "07"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "08"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "09"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "10"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "11"
                                                            and substr($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false,16,2) != "12"
                                                            ){
                                                                echo '<tr>';
                                                                echo '<td>'.$row[0].'</td>';
                                                                echo '<td>Rp'.number_format($row[1],2,',','.').'</td>';
                                                                echo '<td>Rp'.number_format($row[2],2,',','.').'</td>';
                                                                echo '<td>Rp'.number_format($row[3],2,',','.').'</td>';
                                                                echo '<td>Rp'.number_format($row[4],2,',','.').'</td>';
                                                                echo '</tr>';
                                                            }
                                                    }
                                                    //dd($totaltable2);
                                                ?>
                                                </tfoot>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        
        var options = {
            chart: {
                height: 350,
                type: "line",
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
            },
            colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
            series: [
                {
                    name: 'Commitment',
                    type: 'column',
                    data: [<?php
                            foreach ($mtd as $row) {
                                echo $row['jan_cmm'].','.$row['feb_cmm'].','.$row['mar_cmm'].','.$row['apr_cmm'].','.$row['mei_cmm'].','.$row['jun_cmm'].','.$row['jul_cmm'].','.$row['aug_cmm'].','.$row['sep_cmm'].','.$row['oct_cmm'].','.$row['nov_cmm'].','.$row['dec_cmm'];
                            }
                            ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                    ]
                },
                {
                    name: 'Actual',
                    type: 'column',
                    data: [<?php
                            foreach ($mtd as $row) {
                                echo $row['jan_act'].','.$row['feb_act'].','.$row['mar_act'].','.$row['apr_act'].','.$row['mei_act'].','.$row['jun_act'].','.$row['jul_act'].','.$row['aug_act'].','.$row['sep_act'].','.$row['oct_act'].','.$row['nov_act'].','.$row['dec_act'];
                            }
                            ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                    ]
                },
                {
                    name: "Budget",
                    type: 'line',
                    data: [<?php
                            foreach ($mtd as $row) {
                                echo $row['jan_bgd'].','.$row['feb_bgd'].','.$row['mar_bgd'].','.$row['apr_bgd'].','.$row['mei_bgd'].','.$row['jun_bgd'].','.$row['jul_bgd'].','.$row['aug_bgd'].','.$row['sep_bgd'].','.$row['oct_bgd'].','.$row['nov_bgd'].','.$row['dec_bgd'];
                            }
                            ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                        ]
                },
            ],
            stroke: {
                width: [4, 4, 4]
            },
            plotOptions: {
                bar: {
                    columnWidth: "20%"
                }
            },
            xaxis: {
                categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
            },
            yaxis: {
            forceNiceScale: true,
            title: {
                text: 'Quantity',
                style: {
                color: "#fff",
                fontSize: "1px"
                }
            },
            min: 5,
            labels: {
                formatter: function(value) {
                return value.toLocaleString('id-ID', {
                    minimumFractionDigits: 2
                });
                }
            },
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                    return value.toLocaleString('id-ID', {
                        minimumFractionDigits: 2
                    });
                    }
                }
            },
            legend: {
                horizontalAlign: "left",
                offsetX: 40
            }
        };

    var MTDchart = new ApexCharts(document.querySelector("#MTDchart"), options);
    MTDchart.render();
//========================================================================================================
        var options = {
            chart: {
                height: 350,
                type: "line",
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
            },
            colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
            series: [
                {
                    name: 'Commitment',
                    type: 'column',
                    data: [<?php
                            foreach ($ytd as $row) {
                                echo  
                                $row['jan_cmm'].',
                                '.($row['jan_cmm']+$row['feb_cmm']).',
                                '.(($row['jan_cmm']+$row['feb_cmm'])+$row['mar_cmm']).',
                                '.(($row['jan_cmm']+$row['feb_cmm'])+($row['mar_cmm']+$row['apr_cmm'])).',
                                '.((($row['jan_cmm']+$row['feb_cmm'])+($row['mar_cmm']+$row['apr_cmm']))+$row['mei_cmm']).',
                                '.((($row['jan_cmm']+$row['feb_cmm'])+($row['mar_cmm']+$row['apr_cmm']))+($row['mei_cmm']+$row['jun_cmm'])).',
                                '.((($row['jan_cmm']+$row['feb_cmm'])+($row['mar_cmm']+$row['apr_cmm']))+(($row['mei_cmm']+$row['jun_cmm'])+($row['jul_cmm']))).',
                                '.((($row['jan_cmm']+$row['feb_cmm'])+($row['mar_cmm']+$row['apr_cmm']))+(($row['mei_cmm']+$row['jun_cmm'])+($row['jul_cmm']+$row['aug_cmm']))).',
                                '.((($row['jan_cmm']+$row['feb_cmm'])+($row['mar_cmm']+$row['apr_cmm']))+((($row['mei_cmm']+$row['jun_cmm'])+($row['jul_cmm']+$row['aug_cmm']))+($row['sep_cmm']))).',
                                '.((($row['jan_cmm']+$row['feb_cmm'])+($row['mar_cmm']+$row['apr_cmm']))+((($row['mei_cmm']+$row['jun_cmm'])+($row['jul_cmm']+$row['aug_cmm']))+($row['sep_cmm']+$row['oct_cmm']))).',
                                '.((($row['jan_cmm']+$row['feb_cmm'])+($row['mar_cmm']+$row['apr_cmm']))+(((($row['mei_cmm']+$row['jun_cmm'])+($row['jul_cmm']+$row['aug_cmm']))+($row['sep_cmm']+$row['oct_cmm']))+($row['nov_cmm']))).',
                                '.((($row['jan_cmm']+$row['feb_cmm'])+($row['mar_cmm']+$row['apr_cmm']))+(((($row['mei_cmm']+$row['jun_cmm'])+($row['jul_cmm']+$row['aug_cmm']))+($row['sep_cmm']+$row['oct_cmm']))+(($row['nov_cmm']+$row['dec_cmm']))));
                            }
                            ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                    ]
                },
                {
                    name: 'Actual',
                    type: 'column',
                    data: [<?php
                            foreach ($ytd as $row) {
                                echo 
                                $row['jan_act'].',
                                '.($row['jan_act']+$row['feb_act']).',
                                '.(($row['jan_act']+$row['feb_act'])+$row['mar_act']).',
                                '.(($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act'])).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+$row['mei_act']).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+($row['mei_act']+$row['jun_act'])).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+(($row['mei_act']+$row['jun_act'])+($row['jul_act']))).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+(($row['mei_act']+$row['jun_act'])+($row['jul_act']+$row['aug_act']))).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+((($row['mei_act']+$row['jun_act'])+($row['jul_act']+$row['aug_act']))+($row['sep_act']))).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+((($row['mei_act']+$row['jun_act'])+($row['jul_act']+$row['aug_act']))+($row['sep_act']+$row['oct_act']))).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+(((($row['mei_act']+$row['jun_act'])+($row['jul_act']+$row['aug_act']))+($row['sep_act']+$row['oct_act']))+($row['nov_act']))).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+(((($row['mei_act']+$row['jun_act'])+($row['jul_act']+$row['aug_act']))+($row['sep_act']+$row['oct_act']))+(($row['nov_act']+$row['dec_act']))));
                            }
                            ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                    ]
                },
                {
                    name: "Budget",
                    type: 'line',
                    data: [<?php
                            foreach ($ytd as $row) {
                                echo 
                                $row['jan_bgd'].',
                                '.($row['jan_bgd']+$row['feb_bgd']).',
                                '.(($row['jan_bgd']+$row['feb_bgd'])+$row['mar_bgd']).',
                                '.(($row['jan_bgd']+$row['feb_bgd'])+($row['mar_bgd']+$row['apr_bgd'])).',
                                '.((($row['jan_bgd']+$row['feb_bgd'])+($row['mar_bgd']+$row['apr_bgd']))+$row['mei_bgd']).',
                                '.((($row['jan_bgd']+$row['feb_bgd'])+($row['mar_bgd']+$row['apr_bgd']))+($row['mei_bgd']+$row['jun_bgd'])).',
                                '.((($row['jan_bgd']+$row['feb_bgd'])+($row['mar_bgd']+$row['apr_bgd']))+(($row['mei_bgd']+$row['jun_bgd'])+($row['jul_bgd']))).',
                                '.((($row['jan_bgd']+$row['feb_bgd'])+($row['mar_bgd']+$row['apr_bgd']))+(($row['mei_bgd']+$row['jun_bgd'])+($row['jul_bgd']+$row['aug_bgd']))).',
                                '.((($row['jan_bgd']+$row['feb_bgd'])+($row['mar_bgd']+$row['apr_bgd']))+((($row['mei_bgd']+$row['jun_bgd'])+($row['jul_bgd']+$row['aug_bgd']))+($row['sep_bgd']))).',
                                '.((($row['jan_bgd']+$row['feb_bgd'])+($row['mar_bgd']+$row['apr_bgd']))+((($row['mei_bgd']+$row['jun_bgd'])+($row['jul_bgd']+$row['aug_bgd']))+($row['sep_bgd']+$row['oct_bgd']))).',
                                '.((($row['jan_bgd']+$row['feb_bgd'])+($row['mar_bgd']+$row['apr_bgd']))+(((($row['mei_bgd']+$row['jun_bgd'])+($row['jul_bgd']+$row['aug_bgd']))+($row['sep_bgd']+$row['oct_bgd']))+($row['nov_bgd']))).',
                                '.((($row['jan_bgd']+$row['feb_bgd'])+($row['mar_bgd']+$row['apr_bgd']))+(((($row['mei_bgd']+$row['jun_bgd'])+($row['jul_bgd']+$row['aug_bgd']))+($row['sep_bgd']+$row['oct_bgd']))+(($row['nov_bgd']+$row['dec_bgd']))));
                                }
                            ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                        ]
                },
            ],
            stroke: {
                width: [4, 4, 4]
            },
            plotOptions: {
                bar: {
                    columnWidth: "20%"
                }
            },
            xaxis: {
                categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
            },
            yaxis: {
            forceNiceScale: true,
            title: {
                text: 'Quantity',
                style: {
                color: "#fff",
                fontSize: "1px"
                }
            },
            min: 5,
            labels: {
                formatter: function(value) {
                return value.toLocaleString('id-ID', {
                    minimumFractionDigits: 2
                });
                }
            },
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                    return value.toLocaleString('id-ID', {
                        minimumFractionDigits: 2
                    });
                    }
                }
            },
            legend: {
                horizontalAlign: "left",
                offsetX: 40
            }
        };

    var YTDchart = new ApexCharts(document.querySelector("#YTDchart"), options);
    YTDchart.render();
//========================================================================================================
var options = {
            chart: {
                height: 350,
                type: "line",
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
            },
            colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
            series: [
                {
                    name: 'Actual',
                    type: 'column',
                    data: [<?php
                            foreach ($cmmtd as $row) {
                                echo $row['jan_act'].','.$row['feb_act'].','.$row['mar_act'].','.$row['apr_act'].','.$row['mei_act'].','.$row['jun_act'].','.$row['jul_act'].','.$row['aug_act'].','.$row['sep_act'].','.$row['oct_act'].','.$row['nov_act'].','.$row['dec_act'];
                            }
                            ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                    ]
                },
            ],
            stroke: {
                width: [4, 4, 4]
            },
            plotOptions: {
                bar: {
                    columnWidth: "20%"
                }
            },
            xaxis: {
                categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
            },
            yaxis: {
            forceNiceScale: true,
            title: {
                text: 'Quantity',
                style: {
                color: "#fff",
                fontSize: "1px"
                }
            },
            min: 5,
            labels: {
                formatter: function(value) {
                return value.toLocaleString('id-ID', {
                    minimumFractionDigits: 2
                });
                }
            },
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                    return value.toLocaleString('id-ID', {
                        minimumFractionDigits: 2
                    });
                    }
                }
            },
            legend: {
                horizontalAlign: "left",
                offsetX: 40
            }
        };

    var CMMTDchart = new ApexCharts(document.querySelector("#CMMTDchart"), options);
    CMMTDchart.render();
//========================================================================================================
        var options = {
            chart: {
                height: 350,
                type: "line",
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
            },
            colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
            series: [
                {
                    name: 'Actual',
                    type: 'column',
                    data: [
                            <?php
                                foreach ($cmytd as $row) {
                                    echo 
                                $row['jan_act'].',
                                '.($row['jan_act']+$row['feb_act']).',
                                '.(($row['jan_act']+$row['feb_act'])+$row['mar_act']).',
                                '.(($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act'])).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+$row['mei_act']).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+($row['mei_act']+$row['jun_act'])).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+(($row['mei_act']+$row['jun_act'])+($row['jul_act']))).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+(($row['mei_act']+$row['jun_act'])+($row['jul_act']+$row['aug_act']))).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+((($row['mei_act']+$row['jun_act'])+($row['jul_act']+$row['aug_act']))+($row['sep_act']))).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+((($row['mei_act']+$row['jun_act'])+($row['jul_act']+$row['aug_act']))+($row['sep_act']+$row['oct_act']))).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+(((($row['mei_act']+$row['jun_act'])+($row['jul_act']+$row['aug_act']))+($row['sep_act']+$row['oct_act']))+($row['nov_act']))).',
                                '.((($row['jan_act']+$row['feb_act'])+($row['mar_act']+$row['apr_act']))+(((($row['mei_act']+$row['jun_act'])+($row['jul_act']+$row['aug_act']))+($row['sep_act']+$row['oct_act']))+(($row['nov_act']+$row['dec_act']))));
                                }
                            ?> <?php
                            // foreach ($cmytd as $row) {
                            //     echo $row['ttl_act'].',';
                            // }
                            ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                    ]
                },
            ],
            stroke: {
                width: [4, 4, 4]
            },
            plotOptions: {
                bar: {
                    columnWidth: "20%"
                }
            },
            xaxis: {
                categories: //[
                    ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
                    // <?php
                    //     foreach ($ytd as $row) {
                    //         echo $row['year'].',';
                    //     }
                    // ?>
                //]
            },
            yaxis: {
            forceNiceScale: true,
            title: {
                text: 'Quantity',
                style: {
                color: "#fff",
                fontSize: "1px"
                }
            },
            min: 5,
            labels: {
                formatter: function(value) {
                return value.toLocaleString('id-ID', {
                    minimumFractionDigits: 2
                });
                }
            },
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                    return value.toLocaleString('id-ID', {
                        minimumFractionDigits: 2
                    });
                    }
                }
            },
            legend: {
                horizontalAlign: "left",
                offsetX: 40
            }
        };

    var CMYTDchart = new ApexCharts(document.querySelector("#CMYTDchart"), options);
    CMYTDchart.render();
    $(document).ready( function () {
        $('#wbslv4').DataTable( {
            "pageLength": 12,
            "lengthChange": false,
            "order": [],
            "bPaginate": false,
            
        });
    } );

    function MTDtypefetchData() {
        var x = document.getElementById("MTDtypeFilter").value;
        <?php
            if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
            }
            else{
                $fullurl = "xxxxxxxxxxxxxxxxxx";
            }                           
        ?>
        if (x === "opex"){
            location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=0".substr($fullurl?? false,1)."#MTDtypeFilter" ?>"
        }
        else{
            location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=1".substr($fullurl?? false,1)."#MTDtypeFilter" ?>"
        }
    }

    function MTDyearfetchData() {
        var x = document.getElementById("MTDyearFilter").value;
        <?php
            if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
            }
            else{
                $fullurl = "xxxxxxxxxxxxxxxxxx";
            }                           
        ?>
        y = x.substring(2, 4);
        location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,1)?>"+y+"<?= substr($fullurl?? false,3)."#MTDtypeFilter" ?>"
    }
    function WBSLV4fetchData() {
        var x = document.getElementById("WBSLV4yearFilter").value;
        <?php
            if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
            }
            else{
                $fullurl = "xxxxxxxxxxxxxxxxxx";
            }                           
        ?>
        y = x.substring(2, 4);
        console.log(y);
        location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,12)?>"+y+"<?= substr($fullurl?? false,14)."#WBSLV4yearFilter" ?>"
    }

    function WBSMonthFilter(){
        var x = document.getElementById("WBSmonthFilter").value;
        <?php
            if($_GET['b2c97ae425dd751b0e48a3acae79cf4a']?? false){
                $fullurl = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'];
            }
            else{
                $fullurl = "xxxxxxxxxxxxxxxxxx";
            }                           
        ?>
        y = x;
        console.log(y);
        location.href = "<?= site_url("projectSystem/budget")."?b2c97ae425dd751b0e48a3acae79cf4a=".substr($fullurl?? false,0,16)?>"+y+"<?= substr($fullurl?? false,18)."#WBSmonthFilter" ?>"
    
    }
</script>
<?= $this->endSection() ?>

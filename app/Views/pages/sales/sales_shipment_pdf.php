<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->
<style>
  
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }
    .page {
        width: 210mm;
        padding: 20mm;
        margin: 10mm auto;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    .subpage {
        padding: 1cm;
    }
    
    @page {
        size: A4;
        margin: 0;
    }
    @media print {
        html, body {
            width: 210mm;
            height: 297mm;        
        }
        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }
    }
    @media print {
        .no-print,
        .no-print * {
            display: none !important;
        }
        .print * {
            display:initial !important;
        }
    }
</style>
<?php
$ship=$shipment;
$ship=$ship[0];
// var_dump($ship);
?>
<main id="main" class="main">
    <div class="page">
        <div class="row justify-content-center subpage">
          <div class="col-12">
                <img src="<?= base_url('assets/img/logo.png')?>" width="250px">
                <br>
                <br>
                <hr class="style2 m-0">
                <p class="text-center text-2xl font-semibold">SHIPMENT</p>
                <hr class="style2 m-0">
                <br>
                <br>
                 <table>
                    <tr>
                        <td>ID</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->id ?></td>
                    </tr>
                    <tr>
                        <td>Category Shipment</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->category ?></td>
                    </tr>
                    <tr>
                        <td>Buyer</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->customer_name ?></td>
                    </tr>
                    <tr>
                        <td>Contract Number</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->contract_no ?></td>
                    </tr>
                    <tr>
                        <td>Laycan Date</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->laycan_date ?></td>
                    </tr>
                    <tr>
                        <td>ETA Date</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->ETA_date ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <br>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>TB/BG</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->TBBG ?></td>
                    </tr>
                    <tr>
                        <td>Vessel</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->vessel ?></td>
                    </tr>
                    <tr>
                        <td>Return Cargo</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->gi_qty ?></td>
                    </tr>
                    <tr>
                        <td>POL Date</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->bl_date ?></td>
                    </tr>
                    <tr>
                        <td>POL Qty</td>
                        <td class="px-3">:</td>
                        <td><?= number_format($ship->bl_qty,2) ?></td>
                    </tr>
                    <tr>
                        <td>POD Date</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->discharging_date ?></td>
                    </tr>
                    <tr>
                        <td>POD Qty</td>
                        <td class="px-3">:</td>
                        <td><?= number_format($ship->discharging_qty,2) ?></td>
                    </tr>
                    <tr>
                        <td>UoM</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->uom ?></td>
                    </tr>
                    <tr>
                        <td>Type</td>
                        <td class="px-3">:</td>
                        <td><?= $ship->type_supply ?></td>
                    </tr>
                    <tr>
                        <td>Contract Quantity</td>
                        <td class="px-3">:</td>
                        <td><?= number_format($ship->quantity,2)?></td>
                    </tr>
                    <tr>
                        <td>Contract Price</td>
                        <td class="px-3">:</td>
                        <td><?= number_format($ship->contract_price,2) ?></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td class="px-3">:</td>
                        <td class="font-semibold">
                            <?php  
                            if($ship->status=='0'){
                                echo "Draft";
                            }else if($ship->status=='1'){
                                echo "Full Approved";
                            }else if($ship->status=='2'){
                                echo "Delete";
                            }else if($ship->status=='3'){
                                echo "Complete";
                            }else if($ship->status=='4'){
                                echo "Partial Approved 1";
                            }else if($ship->status=='5'){
                                echo "Partial Approved 2";
                            }else if($ship->status=='6'){
                                echo "Partial Approved 3";
                            }else if($ship->status=='7'){
                                echo "Partial Approved 4";
                            }else if($ship->status=='8'){
                                echo "Partial Approved 5";
                            }else{
                                echo "-";
                            }?>
                        </td>
                    </tr>
                 </table>
          </div>
        </div>
    </div>
</main><!-- End #main -->
<script>
    window.print();
</script>

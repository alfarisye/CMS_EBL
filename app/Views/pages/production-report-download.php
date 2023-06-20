<?php
    $data = $_GET['data']??false;
    if(!$data){
        $data = $_POST['datanya']??false;
        if(!$data){
            echo "Error data kosong";
            return;
        }
    }
    $nama_file=$_GET['nama_file'];
    $data=json_decode($data);
    $keys=get_object_vars($data[0]);
    $keys=array_keys($keys);
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=$nama_file.xls");
?>

<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->
<!-- 
 -->

<table width="1800px" cellspacing="0" style="border: 1px solid #BFBFBF; border-bottom: none; font-size: 0.9em;">
    <tr style="background-color: lightblue; text-align: center; font-weight: bold;">
<?php 
    for($i=0;$i<count($keys);$i++){
        ?>
            <td style="padding: 5px 0; border-right: 1px solid #BFBFBF; border-bottom: 1px solid #BFBFBF;  text-transform: capitalize;">
            <?php echo str_replace("_"," ",$keys[$i]); ?></td>
        <?php
    }
?>
    </tr>
<?php 
    foreach($data as $val){
        ?>
    <tr>
        <?php 
        for($i=0;$i<count($keys);$i++){
            $key=$keys[$i];
        ?>
            <td align='center' style='border-right: 1px solid #BFBFBF; border-bottom: 1px solid #BFBFBF;'>
                <?php print $val->$key?>
            </td>
        <?php
        }
        ?>
    </tr>
    <?php
    }
?>
</table>

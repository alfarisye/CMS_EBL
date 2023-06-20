<?php 
$data=$_POST['data'];
$namaFile=$_POST['namaFile'];
$folder=$_POST['folder'];
// echo "$data";
var_dump($folder.$namaFile.".php");
// array_map( 'unlink', array_filter((array) glob("$folder*") ?: [] ) );
$myfile = fopen($folder.$namaFile.".php", "w") or die("Unable to open file!");
$txt = "$data";
fwrite($myfile, $txt);
fclose($myfile);

?>
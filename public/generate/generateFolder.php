<?php
    @$folder=$_GET['folder'];
    @$namaFile=$_GET['namaFile'];
    function deleteAll($dir) {
        foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            deleteAll($file);
        else
            unlink($file);
        }
        rmdir($dir);
    }

    function createFolder($dir){
        if (!file_exists("$dir")) {
            mkdir("$dir", 0777, true);
        }
    }

    deleteAll("Models");
    deleteAll("Controllers");
    deleteAll("Views");
    deleteAll("Database/Migrations");

    createFolder("Views");
    createFolder("Views/Pages");
    createFolder("Controllers");
    createFolder("Models");
    createFolder("Database");
    createFolder("Database/Migrations");
    createFolder("Views/Pages/$folder");
    createFolder("Controllers/$folder");
    createFolder("Models/$folder");

?>
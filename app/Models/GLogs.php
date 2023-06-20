<?php

namespace App\Models;
use CodeIgniter\Model;

class GLogs extends Model
{
    public $db;
    public function __construct()
    {
        $this->db=\Config\Database::connect();
        $this->db->table('g_logs');
    }
    public function after_insert($table){
        $query = $this->db->query("Select * from $table order by created_at desc limit 1");
        $query= $query->getResult();
        $datas=json_encode($query[0]);
        $now=date("Y-m-d H:i:s");
        $actor=session()->get('username')??$_GET['actor']??'NULL';
        $this->db->query("Insert into g_logs (`table`,`action`,`data_after`,`created_at`,`created_by`)
        VALUES ('$table','insert','$datas','$now','$actor')");
    }

    public function before_update($id,$table,$primarys=null){
        $primary=$primarys??$this->get_primary_key($table);
        $query = $this->db->query("Select * from $table where $primary='$id'");
        $query= $query->getResult();
        $datas=json_encode($query[0]);
        $now=date("Y-m-d H:i:s");
        $actor=session()->get('username')??$_GET['actor']??'NULL';
        $this->db->query("Insert into g_logs (`table`,`action`,`data_before`,`updated_at`,`updated_by`)
        VALUES ('$table','update','$datas','$now','$actor')");
    }
    public function after_update($id,$table,$primarys=null){
        $primary=$primarys??$this->get_primary_key($table);
        $query = $this->db->query("Select * from $table where $primary='$id'");
        $query= $query->getResult();
        $datas=json_encode($query[0]);
        $query2 = $this->db->query("Select * from g_logs order by updated_at desc limit 1");
        $query2= $query2->getResult();
        $output= json_decode(json_encode($query2), true);
        $id=$output[0]['id'];
        $this->db->query("update g_logs set data_after='$datas' where id='$id'");
    }

    public function before_delete($id,$table,$primarys=null){
        $primary=$primarys??$this->get_primary_key($table);
        $query = $this->db->query("Select * from $table where $primary='$id'");
        $query= $query->getResult();
        $datas=json_encode($query[0]);
        $now=date("Y-m-d H:i:s");
        $actor=session()->get('username')??$_GET['actor']??'NULL';
        $this->db->query("Insert into g_logs (`table`,`action`,`data_after`,`deleted_at`,`deleted_by`)
        VALUES ('$table','delete','$datas','$now','$actor')");
    }

    public function get_primary_key($table){
        $query = $this->db->query("show index from $table where Key_name = 'PRIMARY' ");
        $output=$query->getResult();
        $output= json_decode(json_encode($output), true);
        return $output[0]['Column_name'];
      }

    // public function get_all_fields($table){
    //     $database=getenv('database.default.database');
    //     $query = $this->db->query("select COLUMN_NAME  
    //     from information_schema.columns 
    //     where table_schema = '$database' 
    //     and table_name = '$table'");
    //     $fields=$query->getResult();
    //     $fields= json_decode(json_encode($fields), true);
    //     $arr=[];
    //     foreach($fields as $key){
    //         array_push($arr,$key['COLUMN_NAME']);
    //     }
    //     return $arr;
    // }

    // public function insert_builder($data,$table){
    //     $fields=$this->get_all_fields($table);
    //     $value="";
    //     $key="";
    //     foreach($fields as $val){
    //         if($data[$val]??false){
    //             $key.="`$val`,";
    //             $value.="'".$data[$val]."',";
    //         }
    //     }
    //     $value=rtrim($value, ", ");
    //     $key=rtrim($key, ", ");
    //     $qry = "INSERT INTO $table ({$key}) VALUES ({$value})";
    //     return $qry;
    // }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class Purchasedd_model extends Model
{
    protected $db;

    public function __construct(){
        $this->db = \Config\Database::connect();
    }

    public function topSupplier(){
        $sql = "select ROUND(sum(t1.NETWR/1000000),0) as TOTAL_NETWR, t2.LIFNR, 
        (select NAME1 from t_vendor tv where tv.LIFNR = t2.LIFNR and tv.BUKRS = t1.BUKRS limit 1) AS nm_vendor
        from t_po_item t1 join t_po t2 on t1.EBELN = t2.EBELN
        where t1.LOEKZ <> 'L'
          and t1.BUKRS = 'HH10'
          group by t2.LIFNR
          order by TOTAL_NETWR DESC limit 5";

        $query = $this->db->query($sql);
        $data = $query->getResultArray();
        return $data;
    }

    public function topCategory(){
        $sql = "select ROUND(sum(t1.NETWR/1000000),0) as TOTAL_NETWR, t2.MATKL
        from t_po_item t1 join T_MDMATERIAL t2 on t1.MATNR = t2.MATNR
        where t1.LOEKZ <> 'L'
          and t1.BUKRS = 'HH10'
          group by t2.MATKL
          order by TOTAL_NETWR DESC;";

        $query = $this->db->query($sql);
        $data = $query->getResultArray();
        return $data;
    }
}
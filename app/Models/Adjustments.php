<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class Adjustments extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'T_Adjustment';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ["id", "id_contractor", "transaksi", "month", "year", "qty", "Transporter_Description", "change_by", "change_on", "deletion_status"];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'change_on';
    protected $updatedField  = 'change_on';
    protected $deletedField  = 'deletion_status';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function upsert($data)
    {
        // Check if the primary key field is set
        if (isset($data[$this->primaryKey])) {
            // If the primary key field is set, update the record
            $this->protect(false)->update($data[$this->primaryKey], $data);
        } else {
            // If the primary key field is not set, insert a new record
            $this->protect(false)->insert($data);
        }
    }
    public function updateOrInsert($id_key, $id_value, $update_data)
    {
        $db = \Config\Database::connect();
        $table_name = $this->table;

        // memeriksa apakah ada data dengan id yang diberikan di tabel
        $count = $db->table($table_name)
            ->where($id_key, $id_value)
            ->countAllResults();

        // jika ada data dengan id yang diberikan, update data tersebut
        if ($count > 0) {
            $db->table($table_name)
                ->where($id_key, $id_value)
                ->update($update_data);

            return "Data berhasil diupdate";
        }
        // jika tidak ada data dengan id yang diberikan, insert data baru
        else {
            $data = array_merge([$id_key => $id_value], $update_data);
            $db->table($table_name)
                ->insert($data);

            return "Data berhasil ditambahkan";
        }
    }
}

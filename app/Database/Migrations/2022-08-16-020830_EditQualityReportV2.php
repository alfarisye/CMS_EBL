<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditQualityReportV2 extends Migration
{
    public function up()
    {
        $fields = [
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            'Project_location' => [
                'type' => 'VARCHAR',
                "constraint" => 255,
            ],
            'Sample_type' => [
                'type' => 'VARCHAR',
                "constraint" => 255,
            ],
            'Lab_sample_id' => [
                'type' => 'VARCHAR',
                "constraint" => 255,
                'unique'     => true,
            ],
            'Customer_sample_id' => [
                'type' => 'VARCHAR',
                "constraint" => 255,
            ],
            'tanggal_mulai' => [
                "type" => "DATE",
                "null" => true,
            ],
            'tanggal_akhir' => [
                "type" => "DATE",
                "null" => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                "constraint" => 255,
            ],
            // ====
            'From_meter' => [
                'type' => 'VARCHAR',
                "constraint" => 255,
            ],
            'To_meter' => [
                'type' => 'VARCHAR',
                "constraint" => 255,
            ],
            
            'Thick_meter' => [
                'type' => 'VARCHAR',
                "constraint" => 255,
            ],
            
            'Seam' => [
                'type' => 'VARCHAR',
                "constraint" => 255,
            ],
            'Weight_of_Recieved' => [
                'type' => 'VARCHAR',
                "constraint" => 255,
            ],
            'Total_moisture' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Moisture_in_sample' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Ash_content' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Volatil_matter' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Fixed_carbon' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Total_sulphu' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Gross_calorifi_adb' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Gross_calorifi_ar' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Gross_calorifi_daf' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Gross_calorifi_dab' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'RD' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'HGI' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'EQM' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Sulphur' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Carbon' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Hydrogen' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Nitrogen' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Oxygen' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'SiO2' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Al2O3' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'TiO2' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Fe2O3' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'CaO' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'MgO' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'K2O' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Na2O' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'SO3' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'P2O5' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Mn3O4' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Deformation_reducing' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Spherical_reducing' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Hemishare_reducing' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Flow_reducing' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Deformation_oxidicing' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Spherical_oxidicing' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Hemishare_oxidicing' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Flow_oxidicing' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Sudiom' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Potasium' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'As' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Hg' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            'Se' => [
                "type" => "decimal",
                "constraint" => '10,2',
                'default'    => 0.00,
            ],
            "created_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "updated_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "deleted_at" => [
                "type" => "DATETIME",
                "null" => true,
            ]
        ];
        $this->forge->dropTable('quality_report');
        $this->forge->addKey("id", true);
        $this->forge->addField($fields);
        $this->forge->createTable("quality_report");
    }

    public function down()
    {
    }
}

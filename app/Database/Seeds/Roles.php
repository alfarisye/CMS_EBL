<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Roles extends Seeder
{
    public function run()
    {
        $data = [
            'name' => 'admin',
            'created_at' => new Time('now'),
            'description' => 'Administrator',
        ];
        $this->db->table('roles')->insert($data);
    }
}

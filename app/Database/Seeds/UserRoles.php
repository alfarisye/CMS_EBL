<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;
class UserRoles extends Seeder
{
    public function run()
    {
        $data = [
            'user_id' => 1,
            'created_at' => new Time('now'),
            'role_id' => 1,
        ];
        $this->db->table('user_roles')->insert($data);
    }
}

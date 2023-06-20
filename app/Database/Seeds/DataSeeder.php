<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataSeeder extends Seeder
{
    public function run()
    {
        $this->call('App\Database\Seeds\Users');
        $this->call('App\Database\Seeds\Roles');
        $this->call('App\Database\Seeds\UserRoles');
        $this->call('App\Database\Seeds\Contractors');
        $this->call('App\Database\Seeds\AnnualBudgets');
        $this->call('App\Database\Seeds\MonthlyBudgets');
        $this->call('App\Database\Seeds\TTypeMasterData');
        $this->call('App\Database\Seeds\CategoryMasterData');
        $this->call('App\Database\Seeds\CategoryMasterData');
        $this->call('App\Database\Seeds\FiPrdAct');
    }
}

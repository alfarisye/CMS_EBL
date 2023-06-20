<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('landing');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
header('Access-Control-Allow-Origin: *');
// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// simple github workflow webhooks
$routes->post('/secret/webhook', 'Webhook::index');

$routes->get('/', 'Landing::index', ['filter' => 'auth']);
$routes->get('/home', 'Home::index', ['filter' => 'auth']);
$routes->get('homeAPI', 'HomeAPI::index');
$routes->get('/login', 'User::login');
$routes->post('/sign-in', 'User::signIn');
$routes->get('/logout', 'User::logout');
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->group('user', function ($routes) {
        $routes->get('/', 'User::index');
        $routes->post('add', 'User::add');
        $routes->get('delete/(:num)', 'User::delete/$1');
        $routes->get('get/(:num)', 'User::get/$1');
        $routes->post('update', 'User::update');
    });
    $routes->group('role', function ($routes) {
        $routes->get('/', 'Role::index');
        $routes->post('add', 'Role::add');
        $routes->get('delete/(:num)', 'Role::delete/$1');
        $routes->get('get/(:num)', 'Role::get/$1');
        $routes->post('update', 'Role::update');
    });
    $routes->group('user-role', function ($routes) {
        $routes->get('/', 'UserRole::index');
        $routes->post('add', 'UserRole::add');
        $routes->get('delete/(:num)', 'UserRole::delete/$1');
        $routes->get('get/(:num)', 'UserRole::get/$1');
        $routes->post('update', 'UserRole::update');
    });
    $routes->group('user-release-pr', function ($routes) {
        $routes->get('/', 'UserReleasePR::index');
        $routes->post('update', 'UserReleasePR::update');
    });
    $routes->group('user-release-po', function ($routes) {
        $routes->get('/', 'UserReleasePO::index');
        $routes->post('update', 'UserReleasePO::update');
    });
});
$routes->group('master-data', function ($routes) {
    $routes->group('costtype', function ($routes) {
        $routes->get('/', 'CostTypes::index');
        $routes->post('add', 'CostTypes::add');
        $routes->get('delete/(:num)', 'CostTypes::delete/$1');
        $routes->get('get/(:num)', 'CostTypes::get/$1');
        $routes->post('update', 'CostTypes::update');
    });
    $routes->group('contractor', function ($routes) {
        $routes->get('/', 'Contractor::index');
        $routes->post('add', 'Contractor::add');
        $routes->get('delete/(:num)', 'Contractor::delete/$1');
        $routes->get('get/(:num)', 'Contractor::get/$1');
        $routes->post('update', 'Contractor::update');
    });
    $routes->group('customer', function ($routes) {
        $routes->get('/', 'MDCustomer::index');
    });
    $routes->group('annualbudget', function ($routes) {
        $routes->get('/', 'MDAnnualBudget::index');
        $routes->post('add', 'MDAnnualBudget::add');
        $routes->get('delete/(:num)', 'MDAnnualBudget::delete/$1');
        $routes->get('get/(:num)', 'MDAnnualBudget::get/$1');
        $routes->post('update', 'MDAnnualBudget::update');
    });
    $routes->group('monthlybudget', function ($routes) {
        $routes->get('/', 'MDMonthlyBudget::index');
        $routes->post('add', 'MDMonthlyBudget::add');
        $routes->get('delete/(:num)', 'MDMonthlyBudget::delete/$1');
        $routes->get('get/(:num)', 'MDMonthlyBudget::get/$1');
        $routes->post('update', 'MDMonthlyBudget::update');
    });
});

$routes->group('me', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'User::get_user_data');
    $routes->post('upload', 'User::upload_image');
});

// khusus menu contractor sub-menu distance
$routes->group('contractor-distance', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Distanceinput::index');
    $routes->post('add', 'Distanceinput::add');
    $routes->get('delete/(:num)', 'Distanceinput::delete/$1');
    $routes->get('edit/(:any)', 'Distanceinput::edit/$1');
    $routes->post('update', 'Distanceinput::update');
});

//Overburden
$routes->group('contractor-ob', function ($routes) {
    $routes->get('/', 'Overburden::index');
    $routes->post('add', 'Overburden::add');
    $routes->get('delete/(:num)', 'Overburden::delete/$1');
    $routes->get('edit/(:any)', 'Overburden::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'Overburden::update');
    $routes->get('/', 'OverburdenAdjustment::index');
    $routes->group('adjust', function ($routes) {
        $routes->post('add', 'OverburdenAdjustment::add');
        $routes->get('delete/(:num)', 'OverburdenAdjustment::delete/$1');
        $routes->get('edit/(:any)', 'OverburdenAdjustment::edit/$1');
        $routes->post('update', 'OverburdenAdjustment::update');
    });
});

// khusus menu operation
$routes->group('operation', ['filter' => 'auth'], function ($routes) {

    // timesheet
    $routes->group('timesheet', ['filter' => 'timesheet'], function ($routes) {
        $routes->get('/', 'Timesheet::index');
        $routes->post('add', 'Timesheet::add');
        $routes->get('delete/(:num)', 'Timesheet::delete/$1');
        $routes->get('edit/(:any)', 'Timesheet::edit/$1');
        // $routes->get('get/(:num)', 'Contractor::get/$1');
        $routes->post('update', 'Timesheet::update');
        $routes->group('adjust', function ($routes) {
            $routes->get('/', 'TimesheetAdjustment::index');
            $routes->post('add', 'TimesheetAdjustment::add');
            $routes->get('delete/(:num)', 'TimesheetAdjustment::delete/$1');
            $routes->get('edit/(:any)', 'TimesheetAdjustment::edit/$1');
            $routes->post('update', 'TimesheetAdjustment::update');
        });
    });

    //Overburden
    // $routes->group('overburden', function ($routes) {
    //     $routes->get('/', 'Overburden::index');
    //     $routes->post('add', 'Overburden::add');
    //     $routes->get('delete/(:num)', 'Overburden::delete/$1');
    //     $routes->get('edit/(:any)', 'Overburden::edit/$1');
    //     // $routes->get('get/(:num)', 'Contractor::get/$1');
    //     $routes->post('update', 'Overburden::update');
    //     $routes->group('adjust', function ($routes) {
    //         $routes->get('/', 'OverburdenAdjustment::index');
    //         $routes->post('add', 'OverburdenAdjustment::add');
    //         $routes->get('delete/(:num)', 'OverburdenAdjustment::delete/$1');
    //         $routes->get('edit/(:any)', 'OverburdenAdjustment::edit/$1');
    //         $routes->post('update', 'OverburdenAdjustment::update');
    //     });
    // });

    // crush coal
    $routes->group('crush-coal', function ($routes) {
        $routes->get('/', 'CrushCoal::index');
        $routes->post('add', 'CrushCoal::add');
        $routes->get('delete/(:num)', 'CrushCoal::delete/$1');
        $routes->get('edit/(:num)', 'CrushCoal::edit/$1');
        $routes->post('update/', 'CrushCoal::update');
        $routes->group('adjust', function ($routes) {
            $routes->get('/', 'CrushCoalAdjust::index');
            $routes->post('add', 'CrushCoalAdjust::add');
            $routes->get('delete/(:num)', 'CrushCoalAdjust::delete/$1');
            $routes->get('edit/(:any)', 'CrushCoalAdjust::edit/$1');
            $routes->post('update', 'CrushCoalAdjust::update');
        });
    });

    // operation dashboard
    $routes->group('operation-dashboard', function ($routes) {
        $routes->get('/', 'OperationDashboard::index');
        $routes->get('sum-cg/this-month', 'OperationDashboard::getCGProductionThisMonth');
        $routes->get('sum-cg/today', 'OperationDashboard::getCGProductionToday');
        $routes->get('sum-cg/this-year', 'OperationDashboard::getCGProductionThisYear');
        $routes->get('sum-ob/this-month', 'OperationDashboard::getOBProductionThisMonth');
        $routes->get('sum-ob/today', 'OperationDashboard::getOBProductionToday');
        $routes->get('sum-ob/this-year', 'OperationDashboard::getOBProductionThisYear');

        $routes->get('strip-ratio/today', 'OperationDashboard::strippingRatioToday');
        $routes->get('strip-ratio/this-month', 'OperationDashboard::strippingRatioThisMonth');
        $routes->get('strip-ratio/this-year', 'OperationDashboard::strippingRatioThisYear');
        $routes->post('strip-ratio/by-date', 'OperationDashboard::strippingRatioByDate');

        $routes->post('sum-cg/by-date', 'OperationDashboard::getCGProductionByDate');
        $routes->post('sum-ob/by-date', 'OperationDashboard::getOBProductionByDate');

        $routes->get('contractor-cg/this-month/(:any)', 'OperationDashboard::getContractorCGProductionThisMonth/$1');
        $routes->get('contractor-cg/this-year/(:any)', 'OperationDashboard::getContractorCGProductionThisYear/$1');
        $routes->get('contractor-cg/today/(:any)', 'OperationDashboard::getContractorCGProductionToday/$1');

        $routes->post('contractor-ob/by-date/(:num)', 'OperationDashboard::getContractorOBProductionByDate/$1');
        $routes->post('contractor-cg/by-date/(:any)', 'OperationDashboard::getContractorCGProductionByDate/$1');

        $routes->get('contractor-ob/this-month/(:num)', 'OperationDashboard::getContractorOBProductionThisMonth/$1');
        $routes->get('contractor-ob/this-year/(:num)', 'OperationDashboard::getContractorOBProductionThisYear/$1');
        $routes->get('contractor-ob/today/(:num)', 'OperationDashboard::getContractorOBProductionToday/$1');

        $routes->get('contractor-strip/this-month/(:num)', 'OperationDashboard::getContractorStrippingRatioThisMonth/$1');
        $routes->get('contractor-strip/this-year/(:num)', 'OperationDashboard::getContractorStrippingRatioThisYear/$1');
        $routes->get('contractor-strip/today/(:num)', 'OperationDashboard::getContractorStrippingRatioToday/$1');
        $routes->post('contractor-strip/by-date/(:num)', 'OperationDashboard::getContractorStrippingRatioByDate/$1');



        $routes->get('stripping-ratio', 'OperationDashboard::strippingRatio');
    });
    // by Ferry
    $routes->group('adjustment_wb', ['filter' => 'auth'], function ($routes) {
        $routes->get('/', 'Adjustment::index');
        $routes->post('add', 'Adjustment::add');
        $routes->get('edit/(:any)', 'Adjustment::edit/$1');
        $routes->post('update', 'Adjustment::update');
        $routes->get('delete/(:any)', 'Adjustment::delete/$1');
        $routes->get('bg_adjust', 'Adjustment::bg_adjust');
    });
});





$routes->group('group-email', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'GroupEmail::index');
    $routes->post('add', 'GroupEmail::add');
    $routes->get('delete/(:num)', 'GroupEmail::delete/$1');
    $routes->get('edit/(:num)', 'GroupEmail::edit/$1');
    $routes->post('update', 'GroupEmail::update');
});
$routes->group('new-csr-allocation', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Newallo::index');
    $routes->get('action/(:num)', 'Newallo::action/$1');
    $routes->post('add', 'Newallo::add');
    $routes->get('delete/(:num)', 'Newallo::delete/$1');
    $routes->get('edit/(:any)', 'Newallo::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'Newallo::update');
    $routes->get('download/(:any)', 'Newallo::download/$1');
});

$routes->group('tambah-bml', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Newbml::index');
    $routes->get('action/(:num)', 'Newbml::action/$1');
    $routes->post('add', 'Newbml::add');
    $routes->get('delete/(:num)', 'Newbml::delete/$1');
    $routes->get('edit/(:any)', 'Newbml::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'Newbml::update');
    $routes->get('download/(:any)', 'Newbml::download/$1');
});

$routes->group('tambah-parameter', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Newpara::index');
    $routes->get('action/(:num)', 'Newpara::action/$1');
    $routes->post('add', 'Newpara::add');
    $routes->get('delete/(:num)', 'Newpara::delete/$1');
    $routes->get('edit/(:any)', 'Newpara::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'Newpara::update');
    $routes->get('download/(:any)', 'Newpara::download/$1');
});

$routes->group('tambah-manform', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Manformkerja::index');
    $routes->get('action/(:num)', 'Manformkerja::action/$1');
    $routes->post('add', 'Manformkerja::add');
    $routes->get('delete/(:num)', 'Manformkerja::delete/$1');
    $routes->get('edit/(:any)', 'Manformkerja::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'Manformkerja::update');
    $routes->get('download/(:any)', 'Manformkerja::download/$1');
});

$routes->group('tambah-manstack', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Manstack::index');
    $routes->get('action/(:num)', 'Manstack::action/$1');
    $routes->post('add', 'Manstack::add');
    $routes->get('delete/(:num)', 'Manstack::delete/$1');
    $routes->get('edit/(:any)', 'Manstack::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'Manstack::update');
    $routes->get('download/(:any)', 'Manstack::download/$1');
});

$routes->group('doc-reminder', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DocReminder::index');
    $routes->get('dashboard', 'DocReminder::dashboard');
    $routes->get('api/get-data/(:num)', 'DocReminder::getJson/$1');
    $routes->post('add', 'DocReminder::add');
    $routes->get('download/(:any)', 'DocReminder::download/$1');
    $routes->get('delete/(:any)', 'DocReminder::delete/$1');
    $routes->get('edit/(:any)', 'DocReminder::edit/$1');
    $routes->post('update', 'DocReminder::update');
});

// background job via cli
$routes->cli('send_reminder', 'DocReminder::sendEmail');
$routes->cli('test_reminder', 'DocReminder::sendEmailTest');
if (getenv("CI_ENVIRONMENT") == 'development') {
    $routes->group('test', function ($routes) {
        $routes->get('dummy-email', 'DocReminder::sendEmailTest');
        $routes->get('reminder-email', 'DocReminder::sendEmail');
        $routes->get('reminder-cli', 'DocReminder::test_cli');
    });
}


// #Tempcode Malik ==
$routes->get('/production/report', 'Home::production_report', ['filter' => 'auth']);
$routes->get('/production/report/download', 'Home::production_report_download');
$routes->post('/production/report/download', 'Home::production_report_download');
// $routes->get('/bukaan-lahan/master', 'Home::peta', ['filter' => 'auth']);
$routes->get('/api/timesheet/get', 'Timesheet::get');
$routes->get('/api/timesheet-adjustment/get', 'TimesheetAdjustment::get');
$routes->post('/api/upload', 'BukaanLahan::upload');
$routes->post('/api/download/geojson', 'BukaanLahan::download_geojson');
$routes->post('/api/download/pdf', 'BukaanLahan::download_pdf');
$routes->get('/general/logs', 'BukaanLahan::logs', ['filter' => 'auth']);
$routes->get('/api/logs', 'BukaanLahan::get_logs');


// BUKAAN LAHAN BLOK
$routes->get('/bukaan-lahan/master', 'BukaanLahan::bl_master', ['filter' => 'auth']);
$routes->get('api/bukaan-lahan/master', 'BukaanLahan::bl_master_data');
$routes->get('/bukaan-lahan/blok', 'BukaanLahan::bl_blok', ['filter' => 'auth']);
$routes->get('api/bukaan-lahan/blok', 'BukaanLahan::bl_blok_get');
$routes->post('api/bukaan-lahan/blok', 'BukaanLahan::bl_blok_add'); //POST
$routes->put('api/bukaan-lahan/blok/(:any)', 'BukaanLahan::bl_blok_update/$1');
$routes->delete('api/bukaan-lahan/blok/(:any)', 'BukaanLahan::bl_blok_delete/$1');

// BUKAAN LAHAN TYPE
$routes->get('/bukaan-lahan/type', 'BukaanLahan::bl_type', ['filter' => 'auth']);
$routes->get('api/bukaan-lahan/type', 'BukaanLahan::bl_type_get');
$routes->post('api/bukaan-lahan/type', 'BukaanLahan::bl_type_add'); //POST
$routes->put('api/bukaan-lahan/type/(:any)', 'BukaanLahan::bl_type_update/$1');
$routes->delete('api/bukaan-lahan/type/(:any)', 'BukaanLahan::bl_type_delete/$1');

// BUKAAN LAHAN Produksi
$routes->get('/bukaan-lahan/produksi', 'BukaanLahan::bl_produksi', ['filter' => 'auth']);
$routes->get('api/bukaan-lahan/produksi', 'BukaanLahan::bl_produksi_get');
$routes->post('api/bukaan-lahan/produksi', 'BukaanLahan::bl_produksi_add'); //POST
$routes->put('api/bukaan-lahan/produksi/(:any)', 'BukaanLahan::bl_produksi_update/$1');
$routes->delete('api/bukaan-lahan/produksi/(:any)', 'BukaanLahan::bl_produksi_delete/$1');
$routes->get('bukaan-lahan/total/produksi', 'BukaanLahan::bl_total_produksi', ['filter' => 'auth']); //GET
$routes->get('api/bukaan-lahan/produksi-total', 'BukaanLahan::bl_produksi_total');

// BUKAAN LAHAN Blok Form
$routes->get('/bukaan-lahan/form', 'BukaanLahan::bl_form', ['filter' => 'auth']);
$routes->get('api/bukaan-lahan/form', 'BukaanLahan::bl_form_get');
$routes->get('api/bukaan-lahan/join/form', 'BukaanLahan::bl_form_join');
$routes->post('api/bukaan-lahan/form', 'BukaanLahan::bl_form_add'); //POST
$routes->put('api/bukaan-lahan/form/(:any)', 'BukaanLahan::bl_form_update/$1');
$routes->delete('api/bukaan-lahan/form/(:any)', 'BukaanLahan::bl_form_delete/$1');

// BUKAAN LAHAN blok geojson
$routes->get('api/bukaan-lahan/geojson', 'BukaanLahan::bl_geojson'); //GET
$routes->post('api/bukaan-lahan/geojson', 'BukaanLahan::bl_geojson_upsert'); //POST
$routes->post('api/bukaan-lahan/geojson/delete', 'BukaanLahan::bl_geojson_delete'); //POST

// Quality Report
$routes->get('/quality-report', 'QualityReports::quality_report', ['filter' => 'auth']);
$routes->get('/quality-report/upload', 'QualityReports::quality_report_upload', ['filter' => 'auth']);
$routes->get('api/quality-report/upload', 'QualityReports::quality_report_upload_excel');
$routes->get('api/quality-report', 'QualityReports::quality_report_get');
$routes->post('api/quality-report', 'QualityReports::quality_report_add'); //POST
$routes->post('api/upload/quality-report', 'QualityReports::quality_report_push_data'); //POST
$routes->put('api/quality-report/(:any)', 'QualityReports::quality_report_update/$1');
$routes->delete('api/quality-report/(:any)', 'QualityReports::quality_report_delete/$1');

// Sales 
$routes->get('test/test', 'BukaanLahan::test2', ['filter' => 'auth']); //GET
$routes->get('sales/display-report', 'Sales::display_report', ['filter' => 'auth']); //GET
$routes->get('/api/get/t_sal_target', 'Sales::get_t_sal_target'); //GET
$routes->get('/api/get/t_sal_shipment', 'Sales::get_t_sal_shipment'); //GET
$routes->get('/api/get/t_sal_price', 'Sales::get_t_sal_price'); //GET
$routes->get('/api/get/t_sal_contract_order', 'Sales::get_t_sal_contract_order'); //GET
$routes->get('/api/get/t_sal_approval_step', 'Sales::get_t_sal_approval_step'); //GET
$routes->get('api/get/t_sal_approval_step', 'Sales::get_t_sal_approval_step'); //GET
$routes->get('/api/get/fi_cur_exc', 'Sales::get_fi_cur_exc'); //GET
// MASTER ACTIVITY
$routes->get('master-data/master-activity', 'Sales::master_activity', ['filter' => 'auth']); //GET
$routes->get('/api/get/master-activity', 'Sales::get_master_activity'); //GET
$routes->post('/api/master-activity', 'Sales::master_activity_insert'); //GET
$routes->put('api/master-activity/(:any)', 'Sales::master_activity_update/$1');
$routes->delete('api/master-activity/(:any)', 'Sales::master_activity_delete/$1');
// PRODUCT MATERIAL
$routes->get('sales/product-material', 'Sales::product_material', ['filter' => 'auth']); //GET
$routes->get('/api/get/product-material', 'Sales::get_product_material'); //GET
$routes->get('/api/get/customer', 'Sales::get_customer'); //GET
$routes->post('api/test', 'BukaanLahan::test'); //POST
// MASTER ACTIVITY
$routes->get('sales/sales-order', 'Sales::sales_order', ['filter' => 'auth']); //GET
$routes->get('sales/invoice', 'Sales::invoice', ['filter' => 'auth']); //GET
$routes->get('sales/salesinvoice', 'Sales::salesinvoice', ['filter' => 'auth']); //GET
$routes->get('sales/demurageinvoice', 'Sales::demurageinvoice', ['filter' => 'auth']); //GET
// $routes->get('sales/despatchinvoice', 'Sales::despatchinvoice', ['filter' => 'auth']);//GET
$routes->get('sales/royaltyinvoice', 'Sales::royaltyinvoice', ['filter' => 'auth']); //GET
$routes->post('sales/royaltyinvoice/add', 'Sales::add_royaltyinvoice', ['filter' => 'auth']);
$routes->get('sales/royaltyinvoice/get/(:any)', 'Sales::get_royaltyinvoice::get/$1', ['filter' => 'auth']);
$routes->get('sales/royaltyinvoice/delete/(:any)', 'Sales::del_royaltyinvoice::delete/$1', ['filter' => 'auth']);
$routes->post('sales/royaltyinvoice/update', 'Sales::upd_royaltyinvoice', ['filter' => 'auth']);
$routes->get('sales/royaltyinvoice/download/(:any)', 'Sales::dwl_royaltyinvoice/$1');
// TSAL_PRICE
$routes->get('sales/sal-price', 'sales\TSalPrices::t_sal_price', ['filter' => 'auth']); //GET
$routes->get('/api/get/t-sal-price', 'sales\TSalPrices::get_t_sal_price'); //GET
$routes->post('/api/t-sal-price', 'sales\TSalPrices::t_sal_price_insert'); //GET
$routes->put('api/t-sal-price/(:any)', 'sales\TSalPrices::t_sal_price_update/$1');
$routes->delete('api/t-sal-price/(:any)', 'sales\TSalPrices::t_sal_price_delete/$1');
$routes->post('/api/add-sales-price', 'sales\TSalPrices::t_sal_price_add'); //GET

// Despatch Inv
$routes->group('despatchinvoice', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DespatchInvoice::index');
    $routes->post('add', 'DespatchInvoice::add');
    $routes->get('delete/(:any)', 'DespatchInvoice::delete/$1');
    $routes->get('get/(:any)', 'DespatchInvoice::get/$1');
    $routes->get('getbuyer/(:any)', 'DespatchInvoice::get/$1');
    $routes->get('getEdit/(:any)', 'DespatchInvoice::getEdit/$1');
    $routes->post('update', 'DespatchInvoice::update');
    $routes->get('download/(:any)', 'DespatchInvoice::download/$1');
});


// Demurage Inv
$routes->group('demurage-invoice', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DemurageInvoice::index');
    $routes->post('add', 'DemurageInvoice::add');
    $routes->get('delete/(:any)', 'DemurageInvoice::delete/$1');
    $routes->get('get/(:any)', 'DemurageInvoice::get/$1');
    $routes->get('getEdit/(:any)', 'DemurageInvoice::getEdit/$1');
    $routes->post('update', 'DemurageInvoice::update');
    $routes->get('download/(:any)', 'DemurageInvoice::download/$1');
});

// $routes->get('/api/get/sales-order', 'Sales::get_sales_order');//GET
// $routes->post('/api/sales-order', 'Sales::sales_order_insert');//GET
// $routes->put('api/sales-order/(:any)', 'Sales::sales_order_update/$1');
// $routes->delete('api/sales-order/(:any)', 'Sales::sales_order_delete/$1');
// Sales Order
$routes->get('sales/contract-order-approval', 'Sales::contract_order_approval', ['filter' => 'auth']); //GET
$routes->get('sales/sales-to-contract', 'Sales::sales_to_contract', ['filter' => 'auth']); //GET
$routes->get('/api/get/sales-order', 'Sales::get_sales_order'); //GET 
$routes->get('api/get/sales-order', 'Sales::get_sales_order'); //GET //for mobile BY FERRY
$routes->post('/api/sales-order', 'Sales::sales_order_insert'); //GET
$routes->put('api/sales-order/(:any)', 'Sales::sales_order_update/$1');
$routes->post('api/sales-order-mobile', 'UserMobile::sales_order_update_mobile');
$routes->delete('api/sales-order/(:any)', 'Sales::sales_order_delete/$1');
$routes->post('/api/sales-order/pdf', 'Sales::generate_sales_order_pdf'); //GET
// SHIPMENT
$routes->get('sales/sales-shipment', 'Sales::sales_shipment', ['filter' => 'auth']); //GET
$routes->get('sales/sales-shipment/pdf', 'Sales::sales_shipment_pdf', ['filter' => 'auth']); //GET
$routes->get('/api/get/sales-shipment', 'Sales::get_sales_shipment'); //GET
$routes->post('/api/sales-shipment', 'Sales::sales_shipment_insert'); //GET
$routes->put('api/sales-shipment/(:any)', 'Sales::sales_shipment_update/$1');
$routes->delete('api/sales-shipment/(:any)', 'Sales::sales_shipment_delete/$1');

// LAYCAN
$routes->get('sales/sales-laycan', 'Sales::sales_laycan', ['filter' => 'auth']); //GET
$routes->get('/api/get/sales-laycan', 'Sales::get_sales_laycan'); //GET
$routes->post('/api/sales-laycan', 'Sales::sales_laycan_insert'); //GET
$routes->post('/api/method/sales-laycan', 'Sales::sales_laycan_method');
$routes->put('api/sales-laycan/(:any)', 'Sales::sales_laycan_update/$1');
$routes->delete('api/sales-laycan/(:any)', 'Sales::sales_laycan_delete/$1');

// COAL INDEX
// $routes->get('sales/coal', 'Sales::coal_index', ['filter' => 'auth']);//GET
$routes->group('sales/coal', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Sales::coal_index');
    $routes->get('dashboard', 'Sales::coal_dashboard');
    $routes->post('add', 'Sales::coal_add');
    $routes->get('delete/(:any)', 'Sales::coal_delete/$1');
    $routes->get('edit/(:any)', 'Sales::coal_edit/$1');
    $routes->post('update', 'Sales::coal_update');
});


// ADD COST MINING
$routes->get('sales/costmining', 'Sales::cost_index', ['filter' => 'auth']); //GET
$routes->post('sales/costmining/add', 'Sales::cost_add', ['filter' => 'auth']); //GET
$routes->get('sales/costmining/edit/(:any)', 'Sales::cost_edit/$1');
$routes->get('sales/costmining/delete/(:any)', 'Sales::cost_delete/$1');
$routes->post('sales/costmining/update', 'Sales::cost_update');

// $route->group("addcost", function($routes) {
//     $routes->get('/', 'Finance::addcostindex');
//     $routes->post('add', 'Finance::addcost');
// });

// T_SAL_RC
$routes->get('sales/sales-rc', 'Sales::sales_rc', ['filter' => 'auth']); //GET
$routes->get('/api/get/sales-rc', 'Sales::get_sales_rc'); //GET
$routes->post('/api/sales-rc', 'Sales::sales_rc_insert'); //GET
$routes->post('/api/method/sales-rc', 'Sales::sales_rc_method');
$routes->put('api/sales-rc/(:any)', 'Sales::sales_rc_update/$1');
$routes->delete('api/sales-rc/(:any)', 'Sales::sales_rc_delete/$1');

// DMO
$routes->get('master-data/master-dmo', 'DMO\Dmo::master_dmo', ['filter' => 'auth']); //GET
$routes->get('/api/get/master-dmo', 'DMO\Dmo::get_master_dmo'); //GET
$routes->post('/api/master-dmo', 'DMO\Dmo::master_dmo_insert'); //GET
$routes->put('api/master-dmo/(:any)', 'DMO\Dmo::master_dmo_update/$1');
$routes->delete('api/master-dmo/(:any)', 'DMO\Dmo::master_dmo_delete/$1');

// Parameter SalesDMO
$routes->get('sales/sales-dmo', 'DMO\Dmo::sales_dmo', ['filter' => 'auth']); //GET
$routes->get('/api/get/sales-dmo', 'DMO\Dmo::get_sales_dmo'); //GET
$routes->post('/api/sales-dmo', 'DMO\Dmo::sales_dmo_insert'); //GET
$routes->put('api/sales-dmo/(:any)', 'DMO\Dmo::sales_dmo_update/$1');
$routes->delete('api/sales-dmo/(:any)', 'DMO\Dmo::sales_dmo_delete/$1');

// MASTER COA
$routes->get('master/master-coa', 'master\TSalMasterCoas::t_sal_master_coa', ['filter' => 'auth']); //GET
$routes->get('/api/get/t-sal-master-coa', 'master\TSalMasterCoas::get_t_sal_master_coa'); //GET
$routes->post('/api/t-sal-master-coa', 'master\TSalMasterCoas::t_sal_master_coa_insert'); //GET
$routes->put('api/t-sal-master-coa/(:any)', 'master\TSalMasterCoas::t_sal_master_coa_update/$1');
$routes->delete('api/t-sal-master-coa/(:any)', 'master\TSalMasterCoas::t_sal_master_coa_delete/$1');

// 
$routes->get('sales/parameter-coa', 'TSalCoas::t_sal_coa', ['filter' => 'auth']); //GET
$routes->get('/api/get/t-sal-coa', 'TSalCoas::get_t_sal_coa'); //GET
$routes->post('/api/t-sal-coa', 'TSalCoas::t_sal_coa_insert'); //GET
$routes->put('api/t-sal-coa/(:any)', 'TSalCoas::t_sal_coa_update/$1');
$routes->delete('api/t-sal-coa/(:any)', 'TSalCoas::t_sal_coa_delete/$1');


// MASTER Laytime
$routes->get('master-data/master-laytime', 'Sales::master_laytime', ['filter' => 'auth']); //GET
$routes->get('/api/get/master-laytime', 'Sales::get_master_laytime'); //GET
$routes->post('/api/master-laytime', 'Sales::master_laytime_insert'); //GET
$routes->put('api/master-laytime/(:any)', 'Sales::master_laytime_update/$1');
$routes->delete('api/master-laytime/(:any)', 'Sales::master_laytime_delete/$1');

// MASTER Laytime entry
$routes->get('sales/sales-laytime', 'Sales::sales_laytime', ['filter' => 'auth']); //GET
$routes->get('sales/sales-laytime/pdf', 'Sales::sales_laytime_pdf', ['filter' => 'auth']); //GET
$routes->get('/api/get/sales-laytime', 'Sales::get_sales_laytime'); //GET
$routes->post('/api/sales-laytime', 'Sales::sales_laytime_insert'); //GET
$routes->put('api/sales-laytime/(:any)', 'Sales::sales_laytime_update/$1');
$routes->delete('api/sales-laytime/(:any)', 'Sales::sales_laytime_delete/$1');
//  Laytime item
$routes->get('/api/get/sales-laytime-item', 'Sales::get_sales_laytime_item'); //GET
$routes->post('/api/sales-laytime-item', 'Sales::sales_laytime_item_insert'); //GET
$routes->put('api/sales-laytime-item/(:any)', 'Sales::sales_laytime_item_update/$1');
$routes->delete('api/sales-laytime-item/(:any)', 'Sales::sales_laytime_item_delete/$1');

//  Purchasing
$routes->get('purchasing', 'Purchasing\Purchasing::index', ['filter' => 'auth']); //GET
$routes->get('/api/purchasing/total', 'Purchasing\Purchasing::get_purchasing_total'); //GET
$routes->get('/api/purchasing/chart', 'Purchasing\Purchasing::get_purchasing_chart'); //GET
$routes->get('/api/purchasing/estimate', 'Purchasing\Purchasing::get_purchasing_estimate'); //GET
$routes->get('/api/purchasing/average', 'Purchasing\Purchasing::get_average'); //GET
$routes->get('/api/purchasing/list-wbs', 'Purchasing\Purchasing::get_list_wbs'); //GET
$routes->get('/api/purchasing/list-wbs-budget', 'Purchasing\Purchasing::get_list_wbs_budget'); //GET

//  Inventory
$routes->get('inventory/dashboard', 'Inventory::dashboard', ['filter' => 'auth']); //GET
$routes->get('inventory/ex-material', 'Inventory::ex_material', ['filter' => 'auth']); //GET
$routes->get('/api/get/inquery-receive', 'Inventory::get_inquiry_receive'); //GET
$routes->get('/api/get/inquery-transfer', 'Inventory::get_inquiry_transfer'); //GET
$routes->get('/api/get/inquery-port', 'Inventory::get_inquiry_port'); //GET
$routes->get('/api/get/t-crushcoal', 'Inventory::get_t_crushcoal'); //GET
$routes->get('/api/get/t-crushcoal-latest', 'Inventory::get_t_crushcoal_latest'); //GET
$routes->get('/api/get/crushcoal', 'Inventory::get_crushcoal'); //GET
$routes->get('/api/get/stock-explosive-material', 'Inventory::get_stock_explosive_material'); //GET
$routes->post('/api/stock-explosive-material', 'Inventory::stock_explosive_material_insert'); //GET
$routes->put('api/stock-explosive-material/(:any)', 'Inventory::stock_explosive_material_update/$1');
$routes->delete('api/stock-explosive-material/(:any)', 'Inventory::stock_explosive_material_delete/$1');

// BUDGET 
// md-annualcrushcoal
$routes->get('budget/annualcrushcoal', 'budget\MdAnnualcrushcoals::md_annualcrushcoal', ['filter' => 'auth']); //GET
$routes->get('/api/get/md-annualcrushcoal', 'budget\MdAnnualcrushcoals::get_md_annualcrushcoal'); //GET
$routes->post('/api/md-annualcrushcoal', 'budget\MdAnnualcrushcoals::md_annualcrushcoal_insert'); //GET
$routes->put('api/md-annualcrushcoal/(:any)', 'budget\MdAnnualcrushcoals::md_annualcrushcoal_update/$1');
$routes->delete('api/md-annualcrushcoal/(:any)', 'budget\MdAnnualcrushcoals::md_annualcrushcoal_delete/$1');
// md-annualhp
$routes->get('budget/annualhp', 'budget\MdAnnualhps::md_annualhp', ['filter' => 'auth']); //GET
$routes->get('/api/get/md-annualhp', 'budget\MdAnnualhps::get_md_annualhp'); //GET
$routes->post('/api/md-annualhp', 'budget\MdAnnualhps::md_annualhp_insert'); //GET
$routes->put('api/md-annualhp/(:any)', 'budget\MdAnnualhps::md_annualhp_update/$1');
$routes->delete('api/md-annualhp/(:any)', 'budget\MdAnnualhps::md_annualhp_delete/$1');
// md-annualdisob
$routes->get('budget/annualdisob', 'budget\MdAnnualdisobs::md_annualdisob', ['filter' => 'auth']); //GET
$routes->get('/api/get/md-annualdisob', 'budget\MdAnnualdisobs::get_md_annualdisob'); //GET
$routes->post('/api/md-annualdisob', 'budget\MdAnnualdisobs::md_annualdisob_insert'); //GET
$routes->put('api/md-annualdisob/(:any)', 'budget\MdAnnualdisobs::md_annualdisob_update/$1');
$routes->delete('api/md-annualdisob/(:any)', 'budget\MdAnnualdisobs::md_annualdisob_delete/$1');
// md-annualdiscg
$routes->get('budget/annualdiscg', 'budget\MdAnnualdiscgs::md_annualdiscg', ['filter' => 'auth']); //GET
$routes->get('/api/get/md-annualdiscg', 'budget\MdAnnualdiscgs::get_md_annualdiscg'); //GET
$routes->post('/api/md-annualdiscg', 'budget\MdAnnualdiscgs::md_annualdiscg_insert'); //GET
$routes->put('api/md-annualdiscg/(:any)', 'budget\MdAnnualdiscgs::md_annualdiscg_update/$1');
$routes->delete('api/md-annualdiscg/(:any)', 'budget\MdAnnualdiscgs::md_annualdiscg_delete/$1');
//md-monthlybudget-cc
$routes->get('budget/monthlybudget-cc', 'budget\MdMonthlybudgetCcs::md_monthlybudget_cc', ['filter' => 'auth']); //GET
$routes->get('/api/get/md-monthlybudget-cc', 'budget\MdMonthlybudgetCcs::get_md_monthlybudget_cc'); //GET
$routes->get('/api/get/md-contractors', 'budget\MdMonthlybudgetCcs::get_md_contractors'); //GET
$routes->post('/api/md-monthlybudget-cc', 'budget\MdMonthlybudgetCcs::md_monthlybudget_cc_insert'); //GET
$routes->put('api/md-monthlybudget-cc/(:any)', 'budget\MdMonthlybudgetCcs::md_monthlybudget_cc_update/$1');
$routes->delete('api/md-monthlybudget-cc/(:any)', 'budget\MdMonthlybudgetCcs::md_monthlybudget_cc_delete/$1');
// md-monthlybudget-hp
$routes->get('budget/md-monthlybudget-hp', 'budget\MdMonthlybudgetHps::md_monthlybudget_hp', ['filter' => 'auth']); //GET
$routes->get('/api/get/md-monthlybudget-hp', 'budget\MdMonthlybudgetHps::get_md_monthlybudget_hp'); //GET
$routes->post('/api/md-monthlybudget-hp', 'budget\MdMonthlybudgetHps::md_monthlybudget_hp_insert'); //GET
$routes->put('api/md-monthlybudget-hp/(:any)', 'budget\MdMonthlybudgetHps::md_monthlybudget_hp_update/$1');
$routes->delete('api/md-monthlybudget-hp/(:any)', 'budget\MdMonthlybudgetHps::md_monthlybudget_hp_delete/$1');
//md-monthly-disob
$routes->get('budget/md-monthly-disob', 'budget\MdMonthlyDisobs::md_monthly_disob', ['filter' => 'auth']); //GET
$routes->get('/api/get/md-monthly-disob', 'budget\MdMonthlyDisobs::get_md_monthly_disob'); //GET
$routes->post('/api/md-monthly-disob', 'budget\MdMonthlyDisobs::md_monthly_disob_insert'); //GET
$routes->put('api/md-monthly-disob/(:any)', 'budget\MdMonthlyDisobs::md_monthly_disob_update/$1');
$routes->delete('api/md-monthly-disob/(:any)', 'budget\MdMonthlyDisobs::md_monthly_disob_delete/$1');
//md-monthly-discg
$routes->get('budget/md-monthly-discg', 'budget\MdMonthlyDiscgs::md_monthly_discg', ['filter' => 'auth']); //GET
$routes->get('/api/get/md-monthly-discg', 'budget\MdMonthlyDiscgs::get_md_monthly_discg'); //GET
$routes->post('/api/md-monthly-discg', 'budget\MdMonthlyDiscgs::md_monthly_discg_insert'); //GET
$routes->put('api/md-monthly-discg/(:any)', 'budget\MdMonthlyDiscgs::md_monthly_discg_update/$1');
$routes->delete('api/md-monthly-discg/(:any)', 'budget\MdMonthlyDiscgs::md_monthly_discg_delete/$1');
// fi sales inv
$routes->get('sales/sales-inv', 'sales\FiSalesInvs::fi_sales_inv', ['filter' => 'auth']); //GET
$routes->get('/api/get/fi-sales-inv', 'sales\FiSalesInvs::get_fi_sales_inv'); //GET
$routes->get('/api/sap/fi-sales-inv', 'sales\FiSalesInvs::fi_sales_inv_sap'); //GET
$routes->post('/api/fi-sales-inv', 'sales\FiSalesInvs::fi_sales_inv_insert'); //GET
$routes->put('api/fi-sales-inv/(:any)', 'sales\FiSalesInvs::fi_sales_inv_update/$1');
$routes->delete('api/fi-sales-inv/(:any)', 'sales\FiSalesInvs::fi_sales_inv_delete/$1');





// Mailer
$routes->post('/api/send/mail', 'Notifications::send_mail'); //GET
$routes->post('/api/send/mail/eproc', 'Notifications::send_mail_eproc'); //GET
$routes->post('/api/send/mail2', 'Notifications::send_mail2'); //GET
$routes->post('/api/send/mail3', 'Notifications::send_mail3'); //GET
$routes->get('/api/send/mail', 'Notifications::get_send_mail'); //GET
$routes->post('/api/send/notification', 'Notifications::send_notification'); //GET
$routes->get('/api/get/notification', 'Notifications::get_notification'); //GET
$routes->put('/api/put/notification/(:any)', 'Notifications::update_notification/$1');

// TOOLS
$routes->get('tools', 'Tools\Tools::index', ['filter' => 'auth']); //GET
$routes->get('tools/chart', 'Tools\Tools::chart', ['filter' => 'auth']); //GET
$routes->get('tools/test', 'Tools\Tools::test', ['filter' => 'auth']); //GET
$routes->get('/api/get/table', 'Tools\Tools::get_table_field'); //GET
$routes->get('/monitor', 'Tools\Tools::monitor'); //GET
$routes->get('/api/monitor', 'Tools\Tools::monitor_api'); //GET

//master data WBS Element
$routes->get('/master-data/wbs_element/', 'WBSElements::index', ['filter' => 'auth']); //GET



// BUKAAN LAHAN Total Produksi 


// $routes->group('bukaan-lahan', ['filter' => 'admin'], function($routes) {
//     $routes->get('/blok', 'BukaanLahan::bl_blok');
//     $routes->get('/blok/type', 'BukaanLahan::bl_type');
//     $routes->get('/blok/produksi', 'BukaanLahan::bl_produksi');
//     $routes->get('/blok/form', 'BukaanLahan::bl_form');
// });
// == #Tempcode Malik

// == code alfa K3LH
$routes->group('k3lh', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'K3LH::index');
    $routes->get('action/(:num)', 'K3LH::action/$1');
    $routes->post('add', 'K3LH::add');
    $routes->get('delete/(:num)', 'K3LH::delete/$1');
    $routes->get('edit/(:any)', 'K3LH::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'K3LH::update');
    $routes->get('monitoring', 'K3LH::monitoring');
    $routes->get('get-card/(:num)/(:num)', 'K3LH::getCard/$1/$2');
});

// == code said pr-tracking
$routes->group('pr-tracking', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'tracking::index');
    $routes->get('action/(:num)', 'tracking::action/$1');
    $routes->post('add', 'tracking::add');
    $routes->get('delete/(:num)', 'tracking::delete/$1');
    $routes->get('edit/(:any)', 'tracking::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'tracking::update');
    $routes->get('monitoring', 'tracking::monitoring');
    $routes->get('get-card/(:num)/(:num)', 'tracking::getCard/$1/$2');
});
$routes->group('kualitasair', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Air::index');
    $routes->get('action/(:num)', 'Air::action/$1');
    $routes->post('add', 'Air::add');
    $routes->get('delete/(:num)', 'Air::delete/$1');
    $routes->get('edit/(:any)', 'Air::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'Air::update');
    $routes->get('monitoring', 'Air::monitoring');
    $routes->get('get-card/(:num)/(:num)', 'Air::getCard/$1/$2');
});

$routes->group('Manpower', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ManKerja::index');
    $routes->get('action/(:num)', 'ManKerja::action/$1');
    $routes->post('add', 'ManKerja::add');
    $routes->get('delete/(:num)', 'ManKerja::delete/$1');
    $routes->get('edit/(:any)', 'ManKerja::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'ManKerja::update');
    $routes->get('monitoring', 'ManKerja::monitoring');
    $routes->get('get-card/(:num)/(:num)', 'ManKerja::getCard/$1/$2');
});

$routes->group('Jamkerja', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ManJam::index');
    $routes->get('action/(:num)', 'ManJam::action/$1');
    $routes->post('add', 'ManJam::add');
    $routes->get('delete/(:num)', 'ManJam::delete/$1');
    $routes->get('edit/(:any)', 'ManJam::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'ManJam::update');
    $routes->get('get-card/(:num)/(:num)', 'ManJam::getCard/$1/$2');
});

$routes->group('addnew', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'addnew::index');
    $routes->get('action/(:num)', 'addnew::action/$1');
    $routes->post('add', 'addnew::add');
    $routes->get('delete/(:num)', 'addnew::delete/$1');
    $routes->get('edit/(:any)', 'addnew::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'addnew::update');
});

// == code alfa CRS
$routes->group('CSRBudget', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CSRBudget::index');
    $routes->get('action/(:num)', 'CSRBudget::action/$1');
    $routes->get('get/(:num)/(:num)', 'CSRBudget::getJson/$1/$2');
    $routes->post('add', 'CSRBudget::add');
    $routes->get('delete/(:num)', 'CSRBudget::delete/$1');
    $routes->get('edit/(:any)', 'CSRBudget::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'CSRBudget::update');
    $routes->get('monitoring', 'CSRBudget::monitoring');
});
$routes->group('CSRAct', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CSRAct::index');
    $routes->get('action/(:num)', 'CSRAct::action/$1');
    $routes->get('get/(:num)/(:num)', 'CSRAct::getJson/$1/$2');
    $routes->post('add', 'CSRAct::add');
    $routes->get('delete/(:num)', 'CSRAct::delete/$1');
    $routes->get('edit/(:any)', 'CSRAct::edit/$1');
    // $routes->get('get/(:num)', 'Contractor::get/$1');
    $routes->post('update', 'CSRAct::update');
    $routes->get('dashboard', 'CSRAct::dashboard');
    $routes->get('download/(:any)', 'CSRAct::download/$1');
});



// $routes->group('Profitratio', ['filter' => 'auth'], function($routes) {
//     $routes->get('/', 'Profitratio::profitability');
// });

$routes->group("finance", ['filter' => 'auth'], function ($route) {
    $route->group("cashflow", function ($routes) {
        $routes->get('/', 'Finance::index');
    });
    $route->group("salesandproduction", function ($routes) {
        $routes->get('/', 'Finance::salesandproduction');
        $routes->get('get/(:num)', 'FiAddCvp::get/$1');
    });
    $route->group("salesCOA", function ($routes) {
        $routes->get('/', 'Finance::salesCOA');
    });
    $route->group("profit", function ($routes) {
        $routes->get('/', 'Finance::profitability');
    });
    // $route->group("profit", function($routes) {
    //     $routes->get('/', 'ProfitAndLoss::profitability');
    // });
    $route->group("profitpershipment", function ($routes) {
        $routes->get('/', 'Finance::profitpershipment');
    });
    $route->group("balance", function ($routes) {
        $routes->get('/', 'Finance::balanceSheet');
    });
    $route->group("updatedata", function ($routes) {
        $routes->get('/', 'Finance::updatedata');
    });
    // $route->group("addcost", function($routes) {
    //     $routes->get('/', 'Finance::addcostindex');
    //     $routes->post('add', 'Finance::addcost');
    // });
    $route->group("pph22", function ($routes) {
        $routes->get('/', 'FiBktPtg::index');
        $routes->get('get/(:num)', 'FiBktPtg::get/$1');
        $routes->post('update', 'FiBktPtg::update');
        $routes->get('delete/(:num)', 'FiBktPtg::delete/$1');
    });
    $route->group("cvpanlysis", function ($routes) {
        $routes->get('/', 'FiAddCvp::index');
        $routes->post('add', 'FiAddCvp::add');
        $routes->get('delete/(:num)', 'FiAddCvp::delete/$1');
        $routes->get('get/(:num)', 'FiAddCvp::get/$1');
        $routes->post('update', 'FiAddCvp::update');
    });
    $route->group("rkap", function ($routes) {
        $routes->get('/', 'FiInRkp::index');
        $routes->post('add', 'FiInRkp::add');
        $routes->post('get_upl', 'FiInRkp::get_upl');
        $routes->get('upl', 'FiInRkp::upl_data');
        $routes->get('upload', 'FiInRkp::form_upload');
        $routes->get('delete/(:num)', 'FiInRkp::delete/$1');
        $routes->get('get/(:num)', 'FiInRkp::get/$1');
        $routes->post('update', 'FiInRkp::update');
    });
    $route->group("budgetfinance", function ($routes) {
        $routes->get('/', 'FiBudget::index');
        $routes->post('add', 'FiBudget::add');
        $routes->get('delete/(:num)', 'FiBudget::delete/$1');
        $routes->get('get/(:num)', 'FiBudget::get/$1');
        $routes->post('update', 'FiBudget::update');
        $routes->get('upl', 'FiBudget::upl_data');
        $routes->get('upload', 'FiBudget::form_upload');
        $routes->post('get_upl', 'FiBudget::get_upl');
    });
    $route->group("updateproductiondata", function ($routes) {
        $routes->get('/', 'FIActData::index');
        $routes->post('add', 'FIActData::add');
        $routes->get('delete/(:num)', 'FIActData::delete/$1');
        $routes->get('getActivity/(:num)', 'FIActData::getActivity/$1');
        $routes->get('get/(:num)', 'FIActData::get/$1');
        $routes->post('update', 'FIActData::update');
    });
    $route->group("updateproductionvendor", function ($routes) {
        $routes->get('/', 'FiPrdLifnr::index');
    });
});

$routes->group("projectSystem", ['filter' => 'auth'], function ($route) {
    $route->group("budget", function ($routes) {
        $routes->get('/', 'ProjectSystem::index');
    });
});

// mobile cms
$routes->get('api/mobile/SalesOrderTotal', 'UserMobile::SalesOrderTotal');
$routes->get('api/mobile/getReleaseCode', 'UserMobile::getReleaseCode');

$routes->get('api/mobile/test', 'BukaanLahan::bl_blok_get');
$routes->get('api/mobile/testDetail', 'BukaanLahan::bl_blok_getDetail');
$routes->get('api/mobile/getDataEss', 'UserMobile::getDataEss');
$routes->get('api/mobile/getDataTotalEss', 'UserMobile::getDataTotalEss');
$routes->get('api/mobile/getDataEssHistory', 'UserMobile::getDataEssHistory');
$routes->get('api/mobile/getUser', 'UserMobile::getUser');
$routes->post('api/mobile/ApproveCuti', 'UserMobile::ApproveCuti');
$routes->post('api/mobile/RejectCuti', 'UserMobile::RejectCuti');

$routes->get('api/mobile/getDataPO', 'UserMobile::getDataPO');
$routes->get('api/mobile/getDataTotalPO', 'UserMobile::getDataTotalPO');
$routes->get('api/mobile/getDetailPO', 'UserMobile::getDetailPO');
$routes->post('api/mobile/ApprovePO', 'UserMobile::ApprovePO');
$routes->post('api/mobile/RejectPO', 'UserMobile::RejectPO');
$routes->get('api/mobile/getDataPOhistory', 'UserMobile::getDataPOhistory');
$routes->post('api/mobile/CancelPO', 'UserMobile::CancelPO');
$routes->get('api/mobile/getVendor', 'UserMobile::getVendor'); // udah sekalian sama plant FOR PO
$routes->get('api/mobile/ApproveSales', 'UserMobile::ApproveSales');

$routes->get('api/mobile/getDataPR', 'UserMobile::getDataPR');
$routes->get('api/mobile/getDataTotalPR', 'UserMobile::getDataTotalPR');
$routes->get('api/mobile/getDetailPR', 'UserMobile::getDetailPR');
$routes->get('api/mobile/getPlant', 'UserMobile::getPlant'); // cuman plant for PR
$routes->post('api/mobile/ApprovePR', 'UserMobile::ApprovePR');
$routes->post('api/mobile/RejectPR', 'UserMobile::RejectPR');
$routes->post('api/mobile/CancelPR', 'UserMobile::CancelPR');
$routes->get('api/mobile/getPRhistory', 'UserMobile::getPRhistory');

//notifikasi mobile CMS
$routes->get('api/mobile/notification_mobile', 'UserMobile::notification_mobile');
$routes->get('api/mobile/check_id_mobile', 'UserMobile::check_id_mobile'); // cek id device untuk auto login jika ganti user ganti device

$routes->get('api/bg/transferWB', 'UserMobile::getTransferWB');
$routes->get('api/bg/receiveWB', 'UserMobile::getReceiveWB');

$routes->post('api/mobile/signInMobile', 'UserMobile::signIn');
$routes->post('api/mobile/test', 'UserMobile::test');
$routes->post('api/mobile/signIn2', 'UserMobile::mobileSignIn');
$routes->post('api/mobile/signIn2', 'User::signIn');
$routes->get('bg_adjust_cms', 'Adjustment::bg_adjust');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

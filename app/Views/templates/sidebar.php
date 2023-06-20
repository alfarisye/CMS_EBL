<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <?php if (preg_match('~home~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?= site_url('/home') ?>">
          <i class="bi bi-file-bar-graph"></i>
          <span>Home</span>
        </a>
      </li>
    <?php } ?>
    <!-- Bukaan Lahan -->
    <?php if (preg_match('~bukaan~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#bukaanlahan-nav" data-bs-toggle="collapse" href="#">
          <i class="ri-landscape-fill"></i><span>Bukaan Lahan</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="bukaanlahan-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('bukaan-lahan/master') ?>">
              <i class="bi bi-circle"></i><span>Bukaan Lahan Home</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('bukaan-lahan/total/produksi') ?>">
              <i class="bi bi-circle"></i><span>Total Produksi</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('bukaan-lahan/produksi') ?>">
              <i class="bi bi-circle"></i><span>Tambah Data Bukaan Lahan</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('bukaan-lahan/blok?page=blok') ?>">
              <i class="bi bi-circle"></i><span>Tambah Data Blok</span>
            </a>
          </li>
        </ul>
      </li>
    <?php } ?>

    <!-- Operation -->
    <?php if (preg_match('~operation|contractor-ob|contractor-distance~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#operation-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-gear"></i><span>Operation</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="operation-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <!-- //by ferry  -->
          <?php if (preg_match('~operation~', session()->get('access'))) { ?>
            <li>
              <a href="<?= site_url('operation/adjustment_wb') ?>">
                <i class="bi bi-circle"></i><span>Adjustment</span>
              </a>
            </li>
          <?php } ?>
          <?php if (preg_match('~timesheet~', session()->get('access'))) { ?>
            <li>
              <a href="<?= site_url('operation/timesheet/') ?>">
                <i class="bi bi-circle"></i><span>Timesheet</span>
              </a>
            </li>
          <?php } ?>
          <?php if (preg_match('~contractor-distance~', session()->get('access'))) { ?>
            <li>
              <a href="<?= site_url('/contractor-distance') ?>">
                <i class="bi bi-circle"></i><span>Distance</span>
              </a>
            </li>
          <?php } ?>
          <?php if (preg_match('~contractor-ob~', session()->get('access'))) { ?>
            <li>
              <a href="<?= site_url('/contractor-ob') ?>">
                <i class="bi bi-circle"></i><span>Overburden</span>
              </a>
            </li>
          <?php } ?>
          <?php if (preg_match('~operation~', session()->get('access'))) { ?>
            <li>
              <a href="<?= site_url('operation/crush-coal/') ?>">
                <i class="bi bi-circle"></i><span>Crushed Coals</span>
              </a>
            </li>
          <?php } ?>
          <?php if (preg_match('~operation~', session()->get('access'))) { ?>
            <li>
              <a href="<?= site_url('operation/operation-dashboard') ?>">
                <i class="bi bi-circle"></i><span>Display Report</span>
              </a>
            </li>
          <?php } ?>

        </ul>
      </li>
    <?php } ?>

    <!-- Inventory -->
    <?php if (preg_match('~inventory~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#inventory-menu" data-bs-toggle="collapse" href="#">
          <i class="ri-archive-line"></i><span>Inventory</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="inventory-menu" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('/inventory/dashboard') ?>">
              <i class="bi bi-circle"></i><span>Management</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/inventory/ex-material') ?>">
              <i class="bi bi-circle"></i><span>Stockof Explosive Material</span>
            </a>
          </li>
        </ul>
      </li>
    <?php } ?>

    <!-- quality report -->
    <?php if (preg_match('~quality-report~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?= site_url('/quality-report') ?>">
          <i class="ri-checkbox-circle-line"></i><span>Quality Report</span>
        </a>
      </li>
    <?php } ?>

    <!-- sales -->
    <?php if (preg_match('~sales~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#procurement-nav" data-bs-toggle="collapse" href="#">
          <i class="ri-money-dollar-box-line"></i><span>Sales</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="procurement-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('/sales/display-report') ?>">
              <i class="bi bi-circle"></i><span>Display Report</span>
            </a>
          </li>

          <li>
            <a href="<?= site_url('/sales/sales-order') ?>">
              <i class="bi bi-circle"></i><span>Sales Order</span>
            </a>
          </li>

          <li>
            <a href="<?= site_url('/sales/contract-order-approval') ?>">
              <i class="bi bi-circle"></i><span>Contract Order Approval</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/sales/sales-to-contract') ?>">
              <i class="bi bi-circle"></i><span>Contract</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/sales/sales-shipment') ?>">
              <i class="bi bi-circle"></i><span>Shipment</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/sales/sales-laycan') ?>">
              <i class="bi bi-circle"></i><span>Vessel Update</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/sales/sales-rc') ?>">
              <i class="bi bi-circle"></i><span>Sales RC</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/sales/sales-laytime') ?>">
              <i class="bi bi-circle"></i><span>Sales Laytime</span>
            </a>
          </li>
          <!-- <li>
          <a href="<?= site_url('/sales/sal-price') ?>"> 
            <i class="bi bi-circle"></i><span>Sales Price</span>
          </a>
        </li> -->
          <li>
            <a href="<?= site_url('/sales/sales-dmo') ?>">
              <i class="bi bi-circle"></i><span>Parameter DMO</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/sales/parameter-coa') ?>">
              <i class="bi bi-circle"></i><span>Parameter COA</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/sales/coal') ?>">
              <i class="bi bi-circle"></i><span>Coal Index</span>
            </a>
          </li>
          <!-- <li>
          <a href="<?= site_url('/sales/costmining') ?>"> 
            <i class="bi bi-circle"></i><span>Cost Mining</span>
          </a>
        </li> -->
        </ul>
      </li>
    <?php } ?>

    <!-- k3lh -->
    <?php if (preg_match('~k3lh~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#K3LH-nav" data-bs-toggle="collapse" href="#">
          <i class="ri-heart-add-fill"></i><span>K3LH</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="K3LH-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('k3lh/') ?>">
              <i class="ri-heart-add-fill"></i><span>Safety Report</span>
            </a>
            <a href="<?= site_url('/kualitasair') ?>">
              <i class="ri-heart-add-fill"></i><span>Kualitas Air</span>
            </a>
            <a href="<?= site_url('/Manpower') ?>">
              <i class="ri-heart-add-fill"></i><span>Manpower</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('k3lh/monitoring') ?>">
              <i class="ri-heart-add-fill"></i><span>Monitoring Dashboard</span>
            </a>
          </li>
        </ul>
      </li>
    <?php } ?>

    <!-- CSR -->
    <?php if (preg_match('~CSRBudget|CSRAct~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#CRS-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-recycle"></i><span>CSR</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="CRS-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('CSRBudget/') ?>">
              <i class="bi bi-recycle"></i><span>CSR Budget</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('CSRAct/') ?>">
              <i class="bi bi-recycle"></i><span>CSR Activity</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('CSRAct/dashboard') ?>">
              <i class="bi bi-recycle"></i><span>CSR Dashboard</span>
            </a>
          </li>
        </ul>
      </li>
    <?php } ?>

    <!-- document reminder -->
    <?php if (preg_match('~doc-reminder~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#doc-reminder-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-calendar-check"></i><span>Document Reminder</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="doc-reminder-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('doc-reminder') ?>">
              <i class="bi bi-circle"></i><span>Cron Jobs</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('doc-reminder/dashboard') ?>">
              <i class="bi bi-circle"></i><span>Dashboard</span>
            </a>
          </li>
        </ul>
      </li>
    <?php } ?>

    <!-- procurement -->
    <?php if (preg_match('~pr-tracking|purchasing~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#procurement2-nav" data-bs-toggle="collapse" href="#">
          <i class="ri-shopping-cart-fill"></i><span>Procurement</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="procurement2-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('pr-tracking') ?>">
              <i class="bi bi-circle"></i><span>PR Tracking</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('purchasing') ?>">
              <i class="bi bi-circle"></i><span>Purchasing</span>
            </a>
          </li>
        </ul>
      </li>
    <?php } ?>

    <!-- <li class="nav-item">
      <a  class="nav-link collapsed" href="<?= site_url('/purchasing') ?>" >
        <i class="ri-checkbox-circle-line"></i><span>Purchasing</span>
      </a>
    </li> -->

    <!-- Finance -->
    <?php if (preg_match('~finance~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#finance-nav" data-bs-toggle="collapse" href="#">
          <i class="ri-money-dollar-circle-line"></i><span>Finance</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="finance-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('finance/cashflow') ?>">
              <i class="bi bi-circle"></i><span>Cash Flow</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('finance/profit') ?>">
              <i class="bi bi-circle"></i><span>Profitability</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('finance/balance') ?>">
              <i class="bi bi-circle"></i><span>Balance Sheet</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('finance/salesandproduction') ?>">
              <i class="bi bi-circle"></i><span>Sales & Production</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('finance/updatedata') ?>">
              <i class="bi bi-circle"></i><span>Update Data</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/sales/invoice') ?>">
              <i class="bi bi-circle"></i><span>Invoices</span>
            </a>
          </li>
        </ul>
      </li>
    <?php } ?>

    <!-- project system -->
    <?php if (preg_match('~projectSystem~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#projectsystem-nav" data-bs-toggle="collapse" href="#">
          <i class="ri-projector-line"></i><span>Project System</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="projectsystem-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('projectSystem/budget') ?>">
              <i class="bi bi-circle"></i><span>Budget</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/master-data/wbs_element') ?>">
              <i class="bi bi-circle"></i><span>WBS List</span>
            </a>
          </li>
        </ul>
      </li>
    <?php } ?>
    <!-- Monitoring Slide TV -->
    <!-- <li class="nav-item">
      <a class="nav-link collapsed"  href="site_url('/monitor')">
        <i class="ri-projector-line"></i><span>TV Monitor</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
    </li> -->
    <li class="nav-heading">Administration</li>

    <!-- user management -->
    <?php if (preg_match('~admin~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#user-admin-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i>
          <span>User Management</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="user-admin-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('/admin/user') ?>">
              <i class="bi bi-circle"></i><span>User Account</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/admin/role') ?>">
              <i class="bi bi-circle"></i><span>Role Management</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/admin/user-role') ?>">
              <i class="bi bi-circle"></i><span>User Roles</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/admin/user-release-pr') ?>">
              <i class="bi bi-circle"></i><span>User Release Code PR</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/admin/user-release-po') ?>">
              <i class="bi bi-circle"></i><span>User Release Code PO</span>
            </a>
          </li>
        </ul>
      </li>
    <?php } ?>

    <!-- master data -->
    <?php if (preg_match('~group-email|master-data|new-csr-allocation|budget|tambah|sales~', session()->get('access'))) { ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#master-data-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Master Data</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="master-data-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= site_url('/master-data/costtype') ?>">
              <i class="bi bi-circle"></i><span>Cost Type</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/master-data/contractor') ?>">
              <i class="bi bi-circle"></i><span>Contractor</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/group-email') ?>">
              <i class="bi bi-circle"></i><span>Group Email</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/master-data/customer') ?>">
              <i class="bi bi-circle"></i><span>Customer</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/new-csr-allocation') ?>">
              <i class="bi bi-circle"></i><span>New Allocation CSR</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/master-data/master-activity') ?>">
              <i class="bi bi-circle"></i><span>Master Activity</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/master-data/master-laytime') ?>">
              <i class="bi bi-circle"></i><span>Master laytime</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/master-data/master-dmo') ?>">
              <i class="bi bi-circle"></i><span>Master DMO</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/master/master-coa') ?>">
              <i class="bi bi-circle"></i><span>Master COA</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/sales/product-material') ?>">
              <i class="bi bi-circle"></i><span>Product Material</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/tambah-parameter') ?>">
              <i class="bi bi-circle"></i><span>Tambah Kualitas Air</span>
            </a>
            <a href="<?= site_url('/tambah-manform') ?>">
              <i class="bi bi-circle"></i><span>Tambah Manpower</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/master-data/annualbudget') ?>">
              <i class="bi bi-circle"></i><span>Annual Budget</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/master-data/monthlybudget') ?>">
              <i class="bi bi-circle"></i><span>Monthly Budget</span>
            </a>
          </li>
          <!--  -->
          <li>
            <a href="<?= site_url('/budget/annualcrushcoal') ?>">
              <i class="bi bi-circle"></i><span>Annual Budget Crushcoal</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/budget/annualhp') ?>">
              <i class="bi bi-circle"></i><span>Annual Budget HP</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/budget/annualdisob') ?>">
              <i class="bi bi-circle"></i><span>Annual Budget OB Distance</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/budget/annualdiscg') ?>">
              <i class="bi bi-circle"></i><span>Annual Budget CG Distance</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/budget/monthlybudget-cc') ?>">
              <i class="bi bi-circle"></i><span>Monthly Budget CC</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/budget/md-monthlybudget-hp') ?>">
              <i class="bi bi-circle"></i><span>Monthly Budget HP</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/budget/md-monthly-disob') ?>">
              <i class="bi bi-circle"></i><span>Monthly Budget OB Distance</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/budget/md-monthly-discg') ?>">
              <i class="bi bi-circle"></i><span>Monthly Budget CG Distance</span>
            </a>
          </li>
          <li>
            <a href="<?= site_url('/master-data/wbs_element') ?>">
              <i class="bi bi-circle"></i><span>WBS List</span>
            </a>
          </li>
          <!--  -->
        </ul>
      </li>
    <?php } ?>



  </ul>
</aside>
<!-- End Sidebar-->
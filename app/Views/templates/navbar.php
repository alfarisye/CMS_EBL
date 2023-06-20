<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">
  <div class="d-flex align-items-center justify-content-between">
    <a href="<?= site_url('/') ?>" class="logo d-flex align-items-center">
      <img src="<?= base_url("assets/img/logo.png") ?>" alt="" />
      <span class="d-none d-lg-block">CMS</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div>
  <!-- End Logo -->

  <!-- <div class="search-bar">
    <form class="search-form d-flex align-items-center" method="POST" action="#">
      <input type="text" name="query" placeholder="Search" title="Enter search keyword" />
      <button type="submit" title="Search"><i class="bi bi-search"></i></button>
    </form>
  </div> -->
  <!-- End Search Bar -->

  <nav id="vue1" class="header-nav ms-auto">
    <!-- MODAL -->
    <div v-show="modalNotif" id="modalNotif-1" class="d-none" @click="modalNotif=false" style="position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:1000;background:black;opacity:0.5;"></div>
    <div v-show="modalNotif" id="modalNotif-2" class="d-none" style="position: fixed;top: 50%;left: 50%;transform: translateX(-50%) translateY(-50%);z-index:1005;min-width:40vw;">
      <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn" style="height:80vh;overflow: scroll;">
        <p class="text-xs float-right">{{formatDistance(new Date(pilih.created_at))}} ago</p>
        <p class="text-sm font-bold">Notification Detail</p>
        <hr class="my-2">
        <p class="text-xs font-semibold float-right">From : {{pilih.from_name}}</p>
        <p class="text-xs font-semibold">To : {{pilih.to_name}}</p>
        <p class="text-xs font-semibold">Subject : {{pilih.subject}}</p>
        <hr>
        <p class="text-sm" v-html="pilih.message"></p>
      </div>
    </div>
    <div v-show="showAllNotif" id="showAllNotif-1" @click="showAllNotif=false" class="d-none" style="position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:1000;background:black;opacity:0.5;"></div>
    <div style="position:absolute;left:0;top:0;width:300px;z-index:10000;background:white;height:100vh;overflow:scroll;" class="p-2 d-none" id="showAllNotif-2" v-show="showAllNotif">
      <p class="font-semibold">
        Notification
      </p>
      <hr>
      <div :class="item.status.indexOf(username)==-1?'bg-blue-200 text-black':''" @click="updateDibaca(item)" v-for="(item, index) in datanya" :key="index+'notification'" class="notification-item cursor-pointer border-b-2 border-black my-1 rounded-lg p-2 hover:bg-blue-300">
        <div>
          <p class="text-xs font-semibold">{{item.from_name}}</p>
          <p class="text-xs" v-html="item.message.replace(/<\/?[^>]+(>|$)/g, '').substring(0,100)"></p>
          <p class="text-xs">{{formatDistance(new Date(item.created_at))}} ago</p>
        </div>
        <hr class="style2">
      </div>

    </div>
    <!-- MODAL -->
    <ul class="d-flex align-items-center">
      <!-- <li class="nav-item d-block d-lg-none">
        <a class="nav-link nav-icon search-bar-toggle" href="#">
          <i class="bi bi-search"></i>
        </a>
      </li> -->
      <!-- End Search Icon-->

      <li class="nav-item dropdown d-none" id="notification-id">
        <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-bell"></i>
          <span class="badge bg-primary badge-number">{{belumdibaca.length}}</span> </a><!-- End Notification Icon -->

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
          <li class="dropdown-header">
            You have {{belumdibaca.length}} new notifications
            <a href="#" @click="showAllNotif=true"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
          </li>
          <li>
            <hr class="dropdown-divider" />
          </li>

          <li :class="item.status.indexOf(username)==-1?'bg-blue-200 text-black':''" @click="updateDibaca(item)" v-for="(item, index) in datanya.slice(0,3)" :key="index+'notification'" class="notification-item cursor-pointer border-b-2 border-black">
            <i class="bi bi-exclamation-circle text-warning"></i>
            <div>
              <h4>{{item.from_name}}</h4>
              <p>{{item.message.replace(/<\/?[^>]+(>|$)/g, '').substring(0,100)}}</p>
              <p>{{formatDistance(new Date(item.created_at))}} ago</p>
            </div>
          </li>
          <li>
            <hr class="dropdown-divider" />
          </li>
          <li class="dropdown-footer">
            <a href="#" @click="showAllNotif=true">Show all notifications</a>
          </li>
        </ul>
        <!-- End Notification Dropdown Items -->
      </li>
      <!-- End Notification Nav -->

      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img src="<?= base_url(session()->get('profile_image') ?? 'assets/img/user.png') ?>" alt="Profile" class="rounded-circle" />
          <span class="d-none d-md-block dropdown-toggle ps-2"><?= $_SESSION['fullname'] ?></span> </a><!-- End Profile Iamge Icon -->

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6><?= $_SESSION['fullname'] ?></h6>
            <!-- <span>Web Designer</span> -->
          </li>
          <li>
            <hr class="dropdown-divider" />
          </li>

          <!-- <li>
            <a
              class="dropdown-item d-flex align-items-center"
              href="#"
            >
              <i class="bi bi-person"></i>
              <span>My Profile</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider" />
          </li> -->

          <li>
            <a class="dropdown-item d-flex align-items-center" href="<?= site_url('me') ?>">
              <i class="bi bi-gear"></i>
              <span>Account Settings</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider" />
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="https://www.hitdigital.id/" target="_new">
              <i class="bi bi-question-circle"></i>
              <span>Need Help?</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider" />
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="<?= site_url('/logout') ?>">
              <i class="bi bi-box-arrow-right"></i>
              <span>Sign Out</span>
            </a>
          </li>
        </ul>
        <!-- End Profile Dropdown Items -->
      </li>
      <!-- End Profile Nav -->
    </ul>
  </nav>
  <!-- End Icons Navigation -->
</header>
<!-- End Header -->

<script type="module">
  import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
  let sdb = new myplugin();
  new Vue({
    el: "#vue1",
    data() {
      return {
        // CUSTOM
        datanya: [],
        belumdibaca: [],
        dibaca: [],
        pilih: {},
        vdata: {},
        username: '',
        modalNotif: false,
        showAllNotif: false
      }
    },
    methods: {
      async updateDibaca(item) {
        let that = this;
        this.pilih = item;
        this.modalNotif = true;
        this.$forceUpdate();
        let username = "<?php echo session()->get('username') ?>";
        axios.put("<?= site_url() ?>" + `/api/put/notification/${item.id}?username=${username}`).then(res => {
          sdb.loadingOff();
          this.getData();
        }).catch(err => {
          this.getData()
        });
      },

      async getData() {
        let username = "<?php echo session()->get('username') ?>";
        this.username = username;
        let data = await axios.get("<?= site_url() ?>" + `/api/get/notification?username=${username}`);
        this.datanya = data.data;
        this.belumdibaca = this.datanya.filter(e => e.status.indexOf(username) == -1);
        this.dibaca = this.datanya.filter(e => e.status.indexOf(username) != -1);
        this.$forceUpdate();
      },
      formatNotif(tgl) {
        return dateFns.format(
          new Date(tgl),
          "DD-MM-YYYY | HH:mm"
        );
      },
      formatDistance(tgl) {
        return dateFns.distanceInWordsToNow(
          new Date(tgl)
        );
      },
    },
    mounted() {
      this.getData();

      document.getElementById('vue1').classList.remove('d-none');
      document.getElementById('notification-id').classList.remove('d-none');
      document.getElementById('showAllNotif-1').classList.remove('d-none');
      document.getElementById('showAllNotif-2').classList.remove('d-none');
      document.getElementById('modalNotif-1').classList.remove('d-none');
      document.getElementById('modalNotif-2').classList.remove('d-none');
    },
  })
</script>
<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Production - Timesheets</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Administration</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("group-email") ?>">Group Emails</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <!-- //! this will be the filter -->
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Group Email Data</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
                        <form id="timesheetForm" action="<?= site_url("/group-email/update") ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="col">
                                <input type="number" name="group_id" value="<?= $group_email['group_id'] ?>" hidden>
                                <div class="row mb-3">
                                    <label for="group_name" class="col-sm-2 col-form-label">Group Name</label>
                                    <div class="col-sm-4">
                                        <input name="group_name" type="text" class="form-control" value="<?= $group_email['group_name'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="email_list" class="col-sm-2 col-form-label">Email List</label>
                                    <div class="col-sm-4" id="email_list">
                                        <?php foreach($group_emails as $ge): ?>
                                            <input type="email" class="form-control" name="group_email[]" value="<?= $ge['email'] ?>" required>
                                        <?php endforeach ?>
                                    </div>
                                    <div class="col-sm-2 d-flex align-items-end gap-3">
                                        <button onclick="addNewEmail()" type="button" class="btn btn-primary">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                        <button onclick="removeEmail()" type="button" class="btn btn-danger">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<script>
    $(document).ready(function() {
        // stop enter key
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
    function addNewEmail() {
        const email_list = document.getElementById('email_list');
        const input = "<input type='email' class='form-control mt-2' name='group_email[]' required>";
        $(email_list).append(input);
    }
    function removeEmail() {
        if ($('#email_list input').length > 0) {
            if ($('#email_list input').length == 1) {
                alert('You must have at least one email');
            } else {
                $('#email_list input:last').remove();
            }
        }
    }
</script>

<?= $this->endSection() ?>
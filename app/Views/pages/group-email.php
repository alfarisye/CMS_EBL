<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Group Email</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Administration</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("group-email/") ?>">Group Email</a></li>
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

                        <!-- button with modal -->
                        <div class="row mb-3">
                            <div class="col-lg-9 d-flex">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Add new Group Email
                                </button>
                            </div>
                        </div>

                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("/group-email/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Group Email</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 mt-3">
                                                <!-- <label for="username" class="form-label">Production ID</label>
                                                <input type="text" class="form-control" id="prod_id" name="prod_id"> -->
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="group_name" class="form-label">Group Name</label>
                                                <input type="text" class="form-control" id="group_name" name="group_name" required>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-10">
                                                    <label for="group_email" class="form-label">Email List</label>
                                                    <div id="email_list">
                                                        <input type="email" class="form-control" name="group_email[]" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end gap-3">
                                                    <button onclick="addNewEmail()" type="button" class="btn btn-primary">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </button>
                                                    <button onclick="removeEmail()" type="button" class="btn btn-danger">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary" type="submit">Submit form</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <!-- Table users -->
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">Group Name</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($group_email as $ge) : ?>
                                        <tr>
                                            <td><?= $ge['group_name'] ?></td>
                                            <td class="text-center">
                                                <a href="<?= site_url("/group-email/edit/{$ge['group_id']}") ?>" class="btn btn-primary btn-sm">
                                                    <i class="bi-pencil"></i>
                                                </a>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url("/group-email/delete/{$ge['group_id']}") ?>" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<script>
    $(document).ready(function() {
    });
    function addNewEmail() {
        const email_list = document.getElementById('email_list');
        const input = "<input type='email' class='form-control mt-2' name='group_email[]' required>";
        $(email_list).append(input);
    }
    function removeEmail() {
        if ($('#email_list input').length > 1) {
            $('#email_list input:last-child').remove();
        }
    }
</script>

<?= $this->endSection() ?>
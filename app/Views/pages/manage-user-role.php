<main id="main" class="main">

    <div class="pagetitle">
        <h1>User Role Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item active">User Role</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">User Role</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                            <i class="bi bi-plus me-1"></i> Add new user role
                        </button>
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("/admin/user-role/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create User Role</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative">
                                                <label class="form-label">Username</label>
                                                <div class="col-sm-12">
                                                    <select name="user_id" class="form-select" aria-label="Default select example">
                                                        <option selected>Please Select Users</option>
                                                        <?php foreach ($users as $user) : ?>
                                                            <option value="<?= $user['id'] ?>"><?= $user['username'] ?> - <?= $user['fullname'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label class="form-label">Role</label>
                                                <div class="col-sm-12">
                                                    <select name="role_id" class="form-select" aria-label="Default select example">
                                                        <option selected>Please Select Role</option>
                                                        <?php foreach ($roles as $role) : ?>
                                                            <option value="<?= $role['id'] ?>"><?= $role['description'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="status" class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="status" name="status" checked>
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
                        <div class="modal fade" id="editForm" tabindex="-1">
                            <form action="<?= site_url("/admin/user-role/update") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit User Role</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- id User -->
                                            <input type="hidden" id="id" name="id">
                                            <div class="col-md-12 position-relative">
                                                <label class="form-label">Username</label>
                                                <div class="col-sm-12">
                                                    <select name="user_id" id="editUser" class="form-select" aria-label="Default select example">
                                                        <option selected>Please Select Users</option>
                                                        <?php foreach ($users as $user) : ?>
                                                            <option value="<?= $user['id'] ?>"><?= $user['username'] ?> - <?= $user['fullname'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label class="form-label">Role</label>
                                                <div class="col-sm-12">
                                                    <select name="role_id" class="form-select" id="editRole" aria-label="Default select example">
                                                        <option selected>Please Select Role</option>
                                                        <?php foreach ($roles as $role) : ?>
                                                            <option value="<?= $role['id'] ?>"><?= $role['description'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="status" class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="editStatus" name="status" checked>
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

                        <!-- Table users -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">User</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userRoles as $ur) : ?>
                                    <tr>
                                        <td><?= $ur['username'] ?></td>
                                        <td><?= $ur['role_name'] ?></td>
                                        <td><?= $ur['status'] ? 'Active' : 'Inactive' ?></td>
                                        <td class="text-center">
                                            <a onclick="editForm(<?= $ur['id'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('Are you sure?')" href="<?= site_url('/admin/user-role/delete/') ?><?= $ur['id'] ?>" class="btn btn-danger btn-sm col">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->
<script>
    function editForm(id) {
        fetch("<?= site_url('/admin/user-role/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                $('#editUser').val(res.user_id);
                $('#editRole').val(res.role_id);
                $('#editStatus').prop("checked", res.status);
                $('#id').val(id);
            })
            .then(() => {
                $('#editForm').modal('show');
            })
    }
</script>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Users Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                            <i class="bi bi-plus me-1"></i> Add new user
                        </button>
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("/admin/user/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label for="username" class="form-label">Username</label>
                                                    <input type="text" class="form-control" id="username" name="username" required>
                                                    <div class="valid-tooltip">
                                                        Looks good!
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="fullname" class="form-label">Full name</label>
                                                <input type="text" class="form-control" id="fullname" name="fullname" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="fullname" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email">
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="status" class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="internal" class="form-label">Internal User</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="internal" name="internal" checked>
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="password" class="form-label">Password</label>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan untuk internal user">
                                                <div class="valid-tooltip">
                                                    Looks good!
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
                            <form action="<?= site_url("/admin/user/update") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- id User -->
                                            <input type="hidden" id="id" name="id">
                                            <div class="col-md-12 position-relative">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" class="form-control" id="editUsername" name="username" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="fullname" class="form-label">Full name</label>
                                                <input type="text" class="form-control" id="editFullname" name="fullname" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="fullname" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="editEmail" name="email" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-6 position-relative">
                                                <label for="status" class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="editStatus" name="status">
                                                </div>
                                            </div>
                                            <div class="col-md-6 position-relative">
                                                <label for="internal" class="form-label">Internal User</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="editInternal" name="internal">
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="password" class="form-label">Password</label>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan untuk internal user">
                                                <div class="valid-tooltip">
                                                    Looks good!
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
                                    <th scope="col">Username</th>
                                    <th scope="col">Full name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Updated At</th>
                                    <th scope="col">Last Login</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" colspan="2" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user) : ?>
                                    <tr>
                                        <td><?= $user['username'] ?></td>
                                        <td><?= $user['fullname'] ?></td>
                                        <td><?= $user['email'] ?></td>
                                        <td><?= $user['created_at'] ?></td>
                                        <td><?= $user['updated_at'] ?></td>
                                        <td><?= $user['last_login'] ?></td>
                                        <td><?= $user['status'] ? 'Active' : 'Inactive' ?></td>
                                        <td>
                                            <a onclick="editForm(<?= $user['id'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a onclick="return confirm('Are you sure?')" href="<?= site_url('/admin/user/delete/') ?><?= $user['id'] ?>" class="btn btn-danger btn-sm col">
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
        fetch("<?= site_url('/admin/user/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#editUsername').val(res.username);
                $('#editFullname').val(res.fullname);
                $('#editEmail').val(res.email);
                $('#editStatus').prop("checked", res.status);
                $('#editInternal').prop("checked", res.internal);
                $('#id').val(id);
            })
            .then(() => {
                $('#editForm').modal('show');
            })
    }

    // function handleSubmit(e) {
    //     e.preventDefault();
    //     let form = $('#editForm')[0];
    //     let formData = new FormData(form);
    //     fetch('/admin/user/update', {
    //             method: 'POST',
    //             body: formData
    //         })
    //         .then(res => res.json())
    //         .then(res => {
    //             if (res.status) {
    //                 $('#editForm').modal('hide');
    //                 location.reload();
    //             } else {
    //                 alert(res.message);
    //             }
    //         });
    // }
</script>
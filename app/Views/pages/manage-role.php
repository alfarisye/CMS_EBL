<main id="main" class="main">

    <div class="pagetitle">
        <h1>Roles Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Roles</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                            <i class="bi bi-plus me-1"></i> Add new role
                        </button>
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("/admin/role/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Role</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative">
                                                <label for="username" class="form-label">Role name</label>
                                                <input type="text" class="form-control" id="name" name="name" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="fullname" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="description" name="description" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <span>URI Group</span>
                                                <div class="col-md-12">
                                                    <input class="form-check-input" type="checkbox" onclick="toggleCheckAll(this)">
                                                    <label class="form-check-label font-monospace" style="font-size: 0.7rem;" for="flexCheckDefault">
                                                        Toggle All
                                                    </label>
                                                </div>
                                                <?php foreach ($routes as $route) : ?>
                                                    <div class="col-md-6 position-relative">
                                                        <input class="form-check-input" type="checkbox" value="<?= $route ?>" id="flexCheckDefault" name='routes[]'>
                                                        <label class="form-check-label font-monospace" style="font-size: 0.7rem;" for="flexCheckDefault">
                                                            <?= $route ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
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
                            <form action="<?= site_url("/admin/role/update") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Role</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- id User -->
                                            <input type="hidden" id="id" name="id">
                                            <div class="col-md-12 position-relative">
                                                <label for="name" class="form-label">Role</label>
                                                <input type="text" class="form-control" id="editName" name="name" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="description" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="editDescription" name="description" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <span class="col-md-12 mt-6">URI Group</span>
                                            <div id="target-roles" class="row"></div>
                                            <div class="col-md-12 position-relative">
                                                <label for="status" class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="editStatus" name="status">
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
                                    <th scope="col">Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $role) : ?>
                                    <tr>
                                        <td><?= $role['name'] ?></td>
                                        <td><?= $role['description'] ?></td>
                                        <td><?= $role['status'] ? 'Active' : 'Inactive' ?></td>
                                        <td class="text-center">
                                            <a onclick="editForm(<?= $role['id'] ?>)" href="#" class="btn btn-primary btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('Are you sure?')" href="<?= site_url('/admin/role/delete/') ?><?= $role['id'] ?>" class="btn btn-danger btn-sm">
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
        let checkbox_target = document.getElementById('target-roles');
        checkbox_target.innerHTML = '';
        fetch("<?= site_url('/admin/role/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                let uri_group = (res.data.uri_group ?? '').split('|');
                $('#editName').val(res.data.name);
                $('#editDescription').val(res.data.description);
                $('#editStatus').prop("checked", res.data.status);
                $('#id').val(id);
                res.routes.forEach((x) => {
                    let checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'routes[]';
                    checkbox.value = x;
                    checkbox.classList.add('form-check-input');
                    if (uri_group.includes(x)) {
                        checkbox.checked = true;
                    }

                    let divs = document.createElement('div');
                    divs.classList.add('col-md-6');

                    let label = document.createElement('label');
                    label.appendChild(document.createTextNode(x));
                    label.classList.add('form-check-label');
                    label.classList.add('font-monospace');
                    label.style = 'font-size: 0.7rem';
                    divs.appendChild(checkbox);
                    divs.appendChild(label);
                    checkbox_target.appendChild(divs);
                    checkbox_target.appendChild(divs);
                });
            })
            .then(() => {
                $('#editForm').modal('show');
            });
    }

    function toggleCheckAll(source) {
        checkboxes = document.getElementsByName('routes[]');
        console.log(checkboxes);
        for (let checkbox in checkboxes)
            checkboxes[checkbox].checked = source.checked;
    }
</script>
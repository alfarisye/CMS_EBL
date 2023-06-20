<main id="main" class="main">

    <div class="pagetitle">
        <h1>Cost Type</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Master Data</li>
                <li class="breadcrumb-item active">Cost Type</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->


    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cost Type</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- // ! Add contractor modal -->
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                            <i class="bi bi-plus me-1"></i> Add new cost type
                        </button>
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("/master-data/costtype/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Cost Type</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="namecost" class="form-label">Cost Type</label>
                                                <input type="text" class="form-control" id="namecost" name="namecost" required>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <select name="coststatus" id="coststatus" class="form-select" aria-label="Default select example">
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Tidak Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary" type="submit">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>


                        <!-- // ! edit contractor modal -->
                        <div class="modal fade" id="editForm" tabindex="-1">
                            <form action="<?= site_url("/master-data/costtype/update") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Cost Type</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- id User -->
                                            <input type="hidden" id="id" name="id_costtype">
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="namecost" class="form-label">Cost Type</label>
                                                <input type="text" class="form-control" id="editNameCost" name="namecost" required>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="coststatus" class="form-label">Status</label>
                                                <select class="form-control" name="coststatus" id="editStatus" required>
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Tidak Aktif</option>
                                                </select>
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
                                    <th scope="col">Id cost type</th>
                                    <th scope="col">Cost Type</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($costs as $cost) : ?>
                                    <tr>
                                        <td><?= $cost['id_costtype'] ?></td>
                                        <td><?= $cost['cost_type'] ?></td>
                                        <td><?= $cost['status'] ? 'Active' : 'Inactive' ?></td>
                                        <td class="row">
                                            <a onclick="editForm(<?= $cost['id_costtype'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('Are you sure?')" href="<?= site_url('/master-data/costtype/delete/') ?><?= $cost['id_costtype'] ?>" class="btn btn-danger btn-sm col">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<script>

    function editForm(id_costtype) {
        fetch("<?= site_url('/master-data/costtype/get/') ?>" + id_costtype)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#editNameCost').val(res.cost_type);
                $('#editStatus').val(res.status);
                $('#id').val(id_costtype);
            })
            .then(() => {
                $('#editForm').modal('show');
            })
    }
</script>

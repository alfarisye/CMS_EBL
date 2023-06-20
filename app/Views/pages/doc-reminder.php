<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Document Reminder</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item active">Document Reminder</li>
                <!-- <li class="breadcrumb-item active"><a href="<?= site_url("group-email/") ?>">Group Email</a></li> -->
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
                        <h5 class="card-title">Document Reminder Data</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->
                        <div class="row mb-3">
                            <div class="col-md-9 d-flex">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Add new Document Reminder
                                </button>
                            </div>
                            <?php if (getenv("CI_ENVIRONMENT") == 'development') : ?>
                                <div class="col-md-3 d-flex">
                                    <a type="button" class="btn btn-warning" href="<?= site_url('test/reminder-cli') ?>">
                                        <i class="ri-mail-send-line"></i> Test Send
                                    </a>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <?= form_open_multipart('doc-reminder/add') ?>
                            <?= csrf_field() ?>
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Create Document Reminder</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Custom Styled Validation with Tooltips -->
                                        <div class="col-md-12 mt-3">
                                            <!-- <label for="username" class="form-label">Production ID</label>
                                                <input type="text" class="form-control" id="prod_id" name="prod_id"> -->
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <label for="doc_no" class="form-label">Document No.</label>
                                            <input type="text" class="form-control" id="doc_no" name="doc_no" required>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-10">
                                                <label for="group_email" class="form-label">Group Email (TO)</label>
                                                <select class="form-control" name="group_email" id="group_email" required>
                                                    <?php foreach ($group_email as $ge) : ?>
                                                        <option value="<?= $ge['group_id'] ?>"><?= $ge['group_name'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <a href="<?= site_url('group-email') ?>" target="new" class="btn btn-primary">Add new</a>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-10">
                                                <label for="group_email_cc" class="form-label">Group Email (CC)</label>
                                                <select class="form-control" name="group_email_cc" id="group_email_cc" required>
                                                    <option>None (no group selected)</option>
                                                    <?php foreach ($group_email as $ge) : ?>
                                                        <option value="<?= $ge['group_id'] ?>"><?= $ge['group_name'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <a href="<?= site_url('group-email') ?>" target="new" class="btn btn-primary">Add new</a>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <label for="doc_desc" class="form-label">Document Description</label>
                                            <input type="text" class="form-control" id="doc_desc" name="doc_desc" required>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <label for="due_date" class="form-label">Due Date</label>
                                            <input type="date" class="form-control" id="due_date" name="due_date" required>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <label for="remind_on" class="form-label">Remind on</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="remind_on" name="remind_on" required>
                                                <span class="input-group-text" id="basic-addon2">Month(s)</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <label for="file_data" class="form-label">Upload File</label>
                                            <input type="file" class="form-control" id="file_data" name="file_data" required>
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
                                        <th scope="col" class="text-center">Document ID</th>
                                        <th scope="col" class="text-center">Document No.</th>
                                        <th scope="col" class="text-center">To</th>
                                        <th scope="col" class="text-center">Cc</th>
                                        <th scope="col" class="text-center">Document Desc</th>
                                        <th scope="col" class="text-center">Due Date</th>
                                        <th scope="col" class="text-center">Email Status</th>
                                        <th scope="col" class="text-center">Download</th>
                                        <th scope="col" colspan="2" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($doc_reminder as $dr) : ?>
                                        <tr>
                                            <td><?= $dr['code'] ?></td>
                                            <td><?= $dr['doc_no'] ?></td>
                                            <td><?= $dr['to_name'] ?></td>
                                            <td><?= $dr['cc_name'] ?></td>
                                            <td><?= $dr['doc_desc'] ?></td>
                                            <td><?= date("d M Y", strtotime($dr['due_date'])) ?></td>
                                            <td>
                                                <?php if ($dr['email_status'] == 'undelivered') : ?>
                                                    <span class="badge rounded-pill bg-danger"><?= $dr['email_status'] ?></span>
                                                <?php else : ?>
                                                    <span class="badge rounded-pill bg-success"><?= $dr['email_status'] ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= site_url("/doc-reminder/download/{$dr['code']}") ?>" class="btn btn-success btn-sm">
                                                    <i class="bi-download"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= site_url("/doc-reminder/edit/{$dr['code']}") ?>" class="btn btn-primary btn-sm">
                                                    <i class="bi-pencil"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url("/doc-reminder/delete/{$dr['code']}") ?>" class="btn btn-danger btn-sm">
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
    $(document).ready(function() {});

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
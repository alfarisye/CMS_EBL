<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Production - Timesheets</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("doc-reminder") ?>">Document Reminder</a></li>
                <li class="breadcrumb-item active"><a href="#"><?= $code ?></a></li>
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
                        <?php

                        use CodeIgniter\I18n\Time;

                        if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
                        <?= form_open_multipart('doc-reminder/update') ?>
                            <?= csrf_field() ?>
                            <div class="col">
                                <input type="number" name="id" value="<?= $doc_reminder['id'] ?>" hidden>
                                <div class="col-md-12 mt-3">
                                    <label for="doc_no" class="form-label">Document No.</label>
                                    <input type="text" class="form-control" id="doc_no" name="doc_no" value="<?= $doc_reminder['doc_no'] ?>" required>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-10">
                                        <label for="group_email" class="form-label">Group Email (TO)</label>
                                        <select class="form-control" name="group_email" id="group_email" required>
                                            <?php foreach ($group_email as $ge) : ?>
                                                <option value="<?= $ge['group_id'] ?>" <?= $ge['group_id'] == $doc_reminder['group_email_id'] ? 'selected' : '' ?>><?= $ge['group_name'] ?></option>
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
                                                <option value="<?= $ge['group_id'] ?>" <?= $ge['group_id'] == $doc_reminder['group_email_cc'] ? 'selected' : '' ?>><?= $ge['group_name'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <a href="<?= site_url('group-email') ?>" target="new" class="btn btn-primary">Add new</a>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="doc_desc" class="form-label">Document Description</label>
                                    <input type="text" class="form-control" id="doc_desc" name="doc_desc" value="<?= $doc_reminder['doc_desc'] ?>" required>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?= $doc_reminder['due_date'] ?>" required>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="remind_on" class="form-label">Remind on</label>
                                    <div class="input-group">
                                        <?php
                                        $due_date = Time::parse($doc_reminder['due_date']);
                                        $remind_on = Time::parse($doc_reminder['remind_on']);
                                        $diff = $remind_on->difference($due_date);
                                        ?>
                                        <input type="number" class="form-control" id="remind_on" name="remind_on" value="<?= $diff->getMonths() ?>" required>
                                        <span class="input-group-text" id="basic-addon2">Month(s)</span>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="file_data" class="form-label">Upload File</label>
                                    <input type="file" class="form-control" id="file_data" name="file_data">
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
</script>

<?= $this->endSection() ?>
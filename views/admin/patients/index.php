<div class="wrap">
    <h1 class="wp-heading-inline">Patients</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=clinic-patients&action=create')); ?>" class="page-title-action">Add New Patient</a>
    <hr class="wp-header-end">

    <?php if (isset($_GET['created']) && $_GET['created'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Patient created successfully.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['updated']) && $_GET['updated'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Patient updated successfully.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Patient deleted successfully.</p></div>
    <?php endif; ?>

    <form method="get">
        <input type="hidden" name="page" value="clinic-patients" />
        <?php
        $listTable->display();
        ?>
    </form>
</div>

<div class="wrap">
    <h1 class="wp-heading-inline">Doctors</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=clinic-doctors&action=create')); ?>" class="page-title-action">Add New Doctor</a>
    <hr class="wp-header-end">

    <?php if (isset($_GET['created']) && $_GET['created'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Doctor created successfully.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['updated']) && $_GET['updated'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Doctor updated successfully.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Doctor deleted successfully.</p></div>
    <?php endif; ?>

    <form method="get">
        <input type="hidden" name="page" value="clinic-doctors" />
        <?php
        $listTable->display();
        ?>
    </form>
</div>

<div class="wrap">
    <h1 class="wp-heading-inline">Departments</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=clinic-departments&action=create')); ?>" class="page-title-action">Add New Department</a>
    <hr class="wp-header-end">

    <?php if (isset($_GET['created']) && $_GET['created'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Department created successfully.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['updated']) && $_GET['updated'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Department updated successfully.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Department deleted successfully.</p></div>
    <?php endif; ?>

    <form method="get">
        <input type="hidden" name="page" value="clinic-departments" />
        <?php
        $listTable->display();
        ?>
    </form>
</div>

<div class="wrap">
    <h1 class="wp-heading-inline">Appointments</h1>
    <hr class="wp-header-end">

    <?php if (isset($_GET['updated']) && $_GET['updated'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Appointment updated successfully.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Appointment deleted successfully.</p></div>
    <?php endif; ?>

    <form method="get">
        <input type="hidden" name="page" value="clinic-appointments" />
        <?php
        $listTable->display();
        ?>
    </form>
</div>

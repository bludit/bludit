<h2 class="title"><i class="fa fa-user-plus"></i> <?php $Language->p('Add a new user') ?></h2>

<?php makeNavbar('users'); ?>

<form method="post" action="" class="forms">
    <label>
        <?php $Language->p('Username') ?>
        <input type="text" name="username" class="width-50" value="<?php echo (isset($_POST['username'])?$_POST['username']:'') ?>">
    </label>

    <label>
        <?php $Language->p('Password') ?>
        <input type="password" name="password" class="width-50">
    </label>

    <label>
        <?php $Language->p('Confirm Password') ?>
        <input type="password" name="confirm-password" class="width-50">
    </label>

    <label for="country">
        <?php $Language->p('Role') ?>
        <select name="role" class="width-50">
            <option value="editor"><?php $Language->p('Editor') ?></option>
            <option value="admin"><?php $Language->p('Administrator') ?></option>
        </select>
        <div class="forms-desc"><?php $Language->p('you-can-choose-the-users-privilege') ?></div>
    </label>

    <label>
        Email
        <input type="text" name="email" class="width-50" value="<?php echo (isset($_POST['email'])?$_POST['email']:'') ?>">
        <div class="forms-desc"><?php $Language->p('email-will-not-be-publicly-displayed') ?></div>
    </label>

    <input type="submit" class="btn btn-blue" value="<?php $Language->p('Add') ?>" name="add-user">
    <a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>" class="btn"><?php $Language->p('Cancel') ?></a>
</form>
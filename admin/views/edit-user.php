<h2 class="title"><i class="fa fa-user"></i> <?php $Language->p('Edit user') ?></h2>

<nav class="navbar nav-pills sublinks" data-tools="tabs" data-active="#profile">
    <ul>
        <li><a href="#profile"><?php $Language->p('Profile') ?></a></li>
        <li><a href="#email"><?php $Language->p('Email') ?></a></li>
        <li><a href="#password"><?php $Language->p('Password') ?></a></li>
    </ul>
</nav>

<!-- ===================================== -->
<!-- Profile -->
<!-- ===================================== -->

<div id="profile">
<form method="post" action="" class="forms">
    <input type="hidden" name="username" value="<?php echo $_user['username'] ?>">

    <label>
        <?php $Language->p('First name') ?>
        <input type="text" name="firstName" class="width-50" value="<?php echo $_user['firstName'] ?>">
    </label>

    <label>
        <?php $Language->p('Last name') ?>
        <input type="text" name="lastName" class="width-50" value="<?php echo $_user['lastName'] ?>">
    </label>

<?php
    if($Login->username()==='admin')
    {
?>
    <label for="role">
        <?php $Language->p('Role') ?>
        <select name="role" class="width-50">
        <?php
            $htmlOptions = array('admin'=>$Language->get('Administrator'), 'editor'=>$Language->get('Editor'));
            foreach($htmlOptions as $value=>$text) {
                echo '<option value="'.$value.'"'.( ($_user['role']===$value)?' selected="selected"':'').'>'.$text.'</option>';
            }
        ?>
        </select>
        <div class="forms-desc"><?php $Language->p('you-can-choose-the-users-privilege') ?></div>
    </label>
<?php
    }
?>
    <input type="submit" class="btn btn-blue" value="Save" name="user-profile">
    <a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>" class="btn"><?php $Language->p('Cancel') ?></a>
</form>
</div>

<!-- ===================================== -->
<!-- E-mail -->
<!-- ===================================== -->

<div id="email">
<form method="post" action="" class="forms">
    <input type="hidden" name="username" value="<?php echo $_user['username'] ?>">

    <label>
        <?php $Language->p('Email') ?>
        <input type="text" name="email" class="width-50" value="<?php echo $_user['email'] ?>">
        <div class="forms-desc"><?php $Language->p('email-will-not-be-publicly-displayed') ?></div>
    </label>

    <input type="submit" class="btn btn-blue" value="Save" name="user-email">
    <a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>" class="btn"><?php $Language->p('Cancel') ?></a>
</form>
</div>

<!-- ===================================== -->
<!-- Password -->
<!-- ===================================== -->

<div id="password">
<form method="post" action="" class="forms">
    <input type="hidden" name="username" value="<?php echo $_user['username'] ?>">

    <label>
        <?php $Language->p('New password') ?>
        <input type="password" name="password" class="width-50">
    </label>

    <label>
        <?php $Language->p('Confirm password') ?>
        <input type="password" name="confirm-password" class="width-50">
    </label>

    <input type="submit" class="btn btn-blue" value="Save" name="user-password">
    <a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>" class="btn"><?php $Language->p('Cancel') ?></a>
</form>
</div>
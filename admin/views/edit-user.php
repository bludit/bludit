<h2 class="title"><i class="fa fa-user"></i> Edit User</h2>

<nav class="navbar nav-pills sublinks" data-tools="tabs" data-active="#profile">
    <ul>
        <li><a href="#profile">Profile</a></li>
        <li><a href="#email">Email</a></li>
        <li><a href="#password">Password</a></li>
    </ul>
</nav>

<!-- ===================================== -->
<!-- Profile -->
<!-- ===================================== -->

<div id="profile">
<form method="post" action="" class="forms">
    <input type="hidden" name="username" value="<?php echo $_user['username'] ?>">

    <label>
        First name
        <input type="text" name="firstName" class="width-50" value="<?php echo $_user['firstName'] ?>">
    </label>

    <label>
        Last name
        <input type="text" name="lastName" class="width-50" value="<?php echo $_user['lastName'] ?>">
    </label>

<?php
    if($Login->username()==='admin')
    {
?>
    <label for="role">
        Role
        <select name="role" class="width-50">
        <?php
            $htmlOptions = array('admin'=>'Administrator', 'editor'=>'Editor');
            foreach($htmlOptions as $value=>$text) {
                echo '<option value="'.$value.'"'.( ($_user['role']===$value)?' selected="selected"':'').'>'.$text.'</option>';
            }
        ?>
        </select>
        <div class="forms-desc">Small and concise description of the field ???</div>
    </label>
<?php
    }
?>
    <input type="submit" class="btn btn-blue" value="Save" name="user-profile">
    <a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>" class="btn">Cancel</a>
</form>
</div>

<!-- ===================================== -->
<!-- E-mail -->
<!-- ===================================== -->

<div id="email">
<form method="post" action="" class="forms">
    <input type="hidden" name="username" value="<?php echo $_user['username'] ?>">

    <label>
        Email
        <input type="text" name="email" class="width-50" value="<?php echo $_user['email'] ?>">
        <div class="forms-desc">Email will not be publicly displayed.</div>
    </label>

    <input type="submit" class="btn btn-blue" value="Save" name="user-email">
    <a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>" class="btn">Cancel</a>
</form>
</div>

<!-- ===================================== -->
<!-- Password -->
<!-- ===================================== -->

<div id="password">
<form method="post" action="" class="forms">
    <input type="hidden" name="username" value="<?php echo $_user['username'] ?>">

    <label>
        New Password
        <input type="password" name="password" class="width-50">
    </label>

    <label>
        Confirm the new Password
        <input type="password" name="confirm-password" class="width-50">
    </label>

    <input type="submit" class="btn btn-blue" value="Save" name="user-password">
    <a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>" class="btn">Cancel</a>
</form>
</div>

<h2 class="title"><i class="fa fa-user-plus"></i> Add a new user</h2>

<?php makeNavbar('users'); ?>

<form method="post" action="" class="forms">
    <label>
        Username
        <input type="text" name="username" class="width-50">
    </label>

    <label>
        Password
        <input type="password" name="password" class="width-50">
    </label>

    <label>
        Confirm Password
        <input type="password" name="confirm-password" class="width-50">
    </label>

    <label for="country">
        Role
        <select name="role" class="width-50">
            <option value="editor">Editor</option>
            <option value="admin">Administrator</option>
        </select>
        <div class="forms-desc">Small and concise description of the field ???</div>
    </label>

    <label>
        Email
        <input type="text" name="email" class="width-50">
        <div class="forms-desc">Email will not be publicly displayed. Recommended for recovery password and notifications.</div>
    </label>

    <input type="submit" class="btn btn-blue" value="Add" name="add-user">
    <a href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>" class="btn">Cancel</a>
</form>

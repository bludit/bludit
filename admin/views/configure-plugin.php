<h2 class="title"><i class="fa fa-rocket"></i> <?php echo $_Plugin->name() ?></h2>

<form id="jsformplugin" method="post" action="" class="forms">

    <input type="hidden" id="jskey" name="key" value="">

    <div>
    <label><?php $Language->p('Plugin label') ?></label>
	<input name="label" type="text" value="<?php echo $_Plugin->getDbField('label') ?>">
	</div>

    <?php
        echo $_Plugin->form();
    ?>

    <div>
    <button class="btn btn-blue" name="publish"><?php echo $Language->p('Save') ?></button>
    </div>

</form>
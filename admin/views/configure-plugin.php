<h2 class="title"><i class="fa fa-rocket"></i> <?php echo $_Plugin->name() ?></h2>

<form id="jsformplugin" method="post" action="" class="forms">

    <input type="hidden" id="jskey" name="key" value="">

    <?php
        echo $_Plugin->form();
    ?>

    <div>
    <button class="btn btn-blue" name="publish"><i class="fa fa-sun-o fa-right"></i><?php echo $Language->p('Save') ?></button>
    </div>

</form>
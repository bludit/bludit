<h2 class="title"><i class="fa fa-cogs"></i> <?php $Language->p('Settings') ?></h2>

<nav class="navbar nav-pills sublinks" data-tools="tabs" data-active="#general">
    <ul>
        <li class="active"><a href="#general"><?php $Language->p('General') ?></a></li>
        <li><a href="#advanced"><?php $Language->p('Advanced') ?></a></li>
        <li><a href="#regional"><?php $Language->p('Regional') ?></a></li>
        <li><a href="#about"><?php $Language->p('About') ?></a></li>
    </ul>
</nav>

<!-- ===================================== -->
<!-- General Settings -->
<!-- ===================================== -->

<div id="general">
<form method="post" action="" class="forms">
    <label>
    <?php $Language->p('Site title') ?>
    <input type="text" name="title" class="width-50" value="<?php echo $Site->title() ?>">
    <div class="forms-desc"><?php $Language->p('use-this-field-to-name-your-site') ?></div>
    </label>

    <label>
    <?php $Language->p('Site slogan') ?>
    <input type="text" name="slogan" class="width-50" value="<?php echo $Site->slogan() ?>">
    <div class="forms-desc"><?php $Language->p('use-this-field-to-add-a-catchy-prhase') ?></div>
    </label>

    <label>
    <?php $Language->p('Site description') ?>
    <input type="text" name="description" class="width-50" value="<?php echo $Site->description() ?>">
    <div class="forms-desc"><?php $Language->p('you-can-add-a-site-description-to-provide') ?></div>
    </label>

    <label>
    <?php $Language->p('Footer text') ?>
    <input type="text" name="footer" class="width-50" value="<?php echo $Site->footer() ?>">
    <div class="forms-desc"><?php $Language->p('you-can-add-a-small-text-on-the-bottom') ?></div>
    </label>

    <input type="submit" class="btn btn-blue" value="<?php $Language->p('Save') ?>" name="form-general">
</form>
</div>


<!-- ===================================== -->
<!-- Advanced Settings -->
<!-- ===================================== -->

<div id="advanced">
<form method="post" action="" class="forms">
    <label for="postsperpage">
        <?php $Language->p('Posts per page') ?>
        <select name="postsperpage" class="width-50">
        <?php
            $htmlOptions = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8');
            foreach($htmlOptions as $text=>$value) {
                echo '<option value="'.$value.'"'.( ($Site->postsPerPage()===$value)?' selected="selected"':'').'>'.$text.'</option>';
            }
        ?>
        </select>
        <div class="forms-desc"><?php $Language->p('number-of-posts-to-show-per-page') ?></div>
    </label>

    <label>
    <?php $Language->p('Site URL') ?>
    <input type="text" name="url" class="width-50" value="<?php echo $Site->url() ?>">
    <div class="forms-desc"><?php $Language->p('the-url-of-your-site') ?></div>
    </label>

    <h4><?php $Language->p('Writting settings') ?></h4>

    <ul class="forms-list">
        <li>
        <input type="checkbox" name="advancedOptions" id="advancedOptions" value="true" <?php echo $Site->advancedOptions()?'checked':'' ?>>
        <label for="advancedOptions"><?php $Language->p('Advanced options') ?></label>
        <div class="forms-desc"><?php $Language->p('add-or-edit-description-tags-or') ?></div>
        </li>
    </ul>

    <h4><?php $Language->p('URL Filters') ?></h4>

    <label>
        <div class="input-groups width-50">
            <span class="input-prepend"><?php $Language->p('Posts') ?></span><input type="text" name="uriPost" value="<?php echo $Site->uriFilters('post') ?>">
        </div>
    </label>

    <label>
        <div class="input-groups width-50">
            <span class="input-prepend"><?php $Language->p('Pages') ?></span><input type="text" name="uriPage" value="<?php echo $Site->uriFilters('page') ?>">
        </div>
    </label>

    <label>
        <div class="input-groups width-50">
            <span class="input-prepend"><?php $Language->p('Tags') ?></span><input type="text" name="uriTag" value="<?php echo $Site->uriFilters('tag') ?>">
        </div>
    </label>

    <input type="submit" class="btn btn-blue" value="<?php $Language->p('Save') ?>" name="form-advanced">
</form>
</div>


<!-- ===================================== -->
<!-- Regional Settings -->
<!-- ===================================== -->

<div id="regional">
<form method="post" action="" class="forms" name="form-regional">
    <label for="jslanguage">
        <?php $Language->p('Language') ?>
        <select id="jslanguage" name="language" class="width-50">
        <?php
            $htmlOptions = $Language->getLanguageList();
            foreach($htmlOptions as $locale=>$nativeName) {
                echo '<option value="'.$locale.'"'.( ($Site->language()===$locale)?' selected="selected"':'').'>'.$nativeName.'</option>';
            }
        ?>
        </select>
        <div class="forms-desc"><?php $Language->p('select-your-sites-language') ?></div>
    </label>

    <label for="jstimezone">
        <?php $Language->p('Timezone') ?>
        <select id="jstimezone" name="timezone" class="width-50">
        <?php
            $htmlOptions = Date::timezoneList();
            foreach($htmlOptions as $text=>$value) {
                echo '<option value="'.$value.'"'.( ($Site->timezone()===$value)?' selected="selected"':'').'>'.$text.'</option>';
            }
        ?>
        </select>
        <div class="forms-desc"><?php $Language->p('select-a-timezone-for-a-correct') ?></div>
    </label>

    <label>
        <?php $Language->p('Locale') ?>
        <input id="jslocale" type="text" name="locale" class="width-50" value="<?php echo $Site->locale() ?>">
        <div class="forms-desc"><?php $Language->p('you-can-use-this-field-to-define-a-set-of') ?></div>
    </label>

    <input type="submit" class="btn btn-blue" value="<?php $Language->p('Save') ?>" name="form-regional">
</form>
</div>

<script>

$(document).ready(function() {

	$("#jslanguage").change(function () {
		var locale = $("#jslanguage option:selected").val();
		$("#jslocale").attr("value",locale);
	});

});

</script>

<!-- ===================================== -->
<!-- About -->
<!-- ===================================== -->

<div id="about">
    <p><i class="fa fa-pencil-square-o"></i> Bludit version <?php echo BLUDIT_VERSION.' ('.BLUDIT_RELEASE_DATE.')' ?></p>
</div>
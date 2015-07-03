<h2 class="title"><i class="fa fa-cogs"></i> Settings</h2>

<nav class="navbar nav-pills sublinks" data-tools="tabs" data-active="#general">
    <ul>
        <li class="active"><a href="#general">General</a></li>
        <li><a href="#advanced">Advanced</a></li>
        <li><a href="#regional">Regional</a></li>
        <li><a href="#about">About</a></li>
    </ul>
</nav>

<!-- ===================================== -->
<!-- General Settings -->
<!-- ===================================== -->

<div id="general">
<form method="post" action="" class="forms">
    <label>
    Site title
    <input type="text" name="title" class="width-50" value="<?php echo $Site->title() ?>">
    <div class="forms-desc">Use this field to name your site, it will appear at the top of every page of your site.</div>
    </label>

    <label>
    Site slogan
    <input type="text" name="slogan" class="width-50" value="<?php echo $Site->slogan() ?>">
    <div class="forms-desc">Use this field to add a catchy prhase on your site.</div>
    </label>

    <label>
    Site description
    <input type="text" name="description" class="width-50" value="<?php echo $Site->description() ?>">
    <div class="forms-desc">You can add a site description to provide a short bio or description of your site.</div>
    </label>

    <label>
    Footer text
    <input type="text" name="footer" class="width-50" value="<?php echo $Site->footer() ?>">
    <div class="forms-desc">You can add a small text on the bottom of every page. eg: copyright, owner, dates, etc.</div>
    </label>

    <input type="submit" class="btn" value="Save" name="form-general">
</form>
</div>


<!-- ===================================== -->
<!-- Advanced Settings -->
<!-- ===================================== -->
<div id="advanced">
<form method="post" action="" class="forms">
    <label for="postsperpage">
        Posts per page
        <select name="postsperpage" class="width-50">
        <?php
            $htmlOptions = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8');
            foreach($htmlOptions as $text=>$value) {
                echo '<option value="'.$value.'"'.( ($Site->postsPerPage()===$value)?' selected="selected"':'').'>'.$text.'</option>';
            }
        ?>
        </select>
        <div class="forms-desc">Number of posts to show per page.</div>
    </label>

    <label>
    Site URL
    <input type="text" name="url" class="width-50" value="<?php echo $Site->url() ?>">
    <div class="forms-desc">The URL of your site.</div>
    </label>

    <h4>Writting Settings</h4>

    <ul class="forms-list">
        <li>
        <input type="checkbox" name="advancedOptions" id="advancedOptions" value="true" <?php echo $Site->advancedOptions()?'checked':'' ?>>
        <label for="advancedOptions">Advanced options</label>
        <div class="forms-desc">Add or edit description, tags or modify the friendly URL.</div>
        </li>
    </ul>

    <h4>URL Filters</h4>

    <label>
        <div class="input-groups width-50">
            <span class="input-prepend">Post</span><input type="text" name="uriPost" value="<?php echo $Site->uriFilters('post') ?>">
        </div>
    </label>

    <label>
        <div class="input-groups width-50">
            <span class="input-prepend">Page</span><input type="text" name="uriPage" value="<?php echo $Site->uriFilters('page') ?>">
        </div>
    </label>

    <label>
        <div class="input-groups width-50">
            <span class="input-prepend">Tag</span><input type="text" name="uriTag" value="<?php echo $Site->uriFilters('tag') ?>">
        </div>
    </label>

    <input type="submit" class="btn" value="Save" name="form-advanced">
</form>
</div>


<!-- ===================================== -->
<!-- Regional Settings -->
<!-- ===================================== -->

<div id="regional">
<form method="post" action="" class="forms" name="form-regional">
    <label for="jslanguage">
        Language
        <select id="jslanguage" name="language" class="width-50">
        <?php
            $htmlOptions = $Language->getLanguageList();
            foreach($htmlOptions as $locale=>$nativeName) {
                echo '<option value="'.$locale.'"'.( ($Site->language()===$locale)?' selected="selected"':'').'>'.$nativeName.'</option>';
            }
        ?>
        </select>
        <div class="forms-desc">Select your site's language.</div>
    </label>

    <label for="jstimezone">
        Timezone
        <select id="jstimezone" name="timezone" class="width-50">
        <?php
            $htmlOptions = Date::timezoneList();
            foreach($htmlOptions as $text=>$value) {
                echo '<option value="'.$value.'"'.( ($Site->timezone()===$value)?' selected="selected"':'').'>'.$text.'</option>';
            }
        ?>
        </select>
        <div class="forms-desc">Select a timezone for a correct date/time display on your site.</div>
    </label>

    <label>
        Locale
        <input id="jslocale" type="text" name="locale" class="width-50" value="<?php echo $Site->locale() ?>">
        <div class="forms-desc">You can use this field to define a set of parameters related to the languege, country and special preferences.</div>
    </label>

    <input type="submit" class="btn" value="Save" name="form-regional">
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

<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::formOpen(array('id' => 'jsform', 'class' => 'tab-content')); ?>

<div class="align-middle">
	<div class="float-right mt-1">
		<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
		<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'dashboard' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
	<?php echo Bootstrap::pageTitle(array('title' => $L->g('Settings'), 'icon' => 'cog')); ?>
</div>

<!-- TABS -->
<nav class="mb-3">
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="nav-general" aria-selected="false"><?php $L->p('General') ?></a>
		<a class="nav-item nav-link" id="nav-advanced-tab" data-toggle="tab" href="#advanced" role="tab" aria-controls="nav-advanced" aria-selected="false"><?php $L->p('Advanced') ?></a>
		<a class="nav-item nav-link" id="nav-seo-tab" data-toggle="tab" href="#seo" role="tab" aria-controls="nav-seo" aria-selected="false"><?php $L->p('SEO') ?></a>
		<a class="nav-item nav-link" id="nav-social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="nav-social" aria-selected="false"><?php $L->p('Social Networks') ?></a>
		<a class="nav-item nav-link" id="nav-images-tab" data-toggle="tab" href="#images" role="tab" aria-controls="nav-images" aria-selected="false"><?php $L->p('Images') ?></a>
		<a class="nav-item nav-link" id="nav-language-tab" data-toggle="tab" href="#language" role="tab" aria-controls="nav-language" aria-selected="false"><?php $L->p('Language') ?></a>
		<a class="nav-item nav-link" id="nav-custom-fields-tab" data-toggle="tab" href="#custom-fields" role="tab" aria-controls="nav-custom-fields" aria-selected="false"><?php $L->p('Custom fields') ?></a>
		<a class="nav-item nav-link" id="nav-logo-tab" data-toggle="tab" href="#logo" role="tab" aria-controls="nav-logo" aria-selected="false"><?php $L->p('Logo') ?></a>
	</div>
</nav>

<?php
// Token CSRF
echo Bootstrap::formInputHidden(array(
	'name' => 'tokenCSRF',
	'value' => $security->getTokenCSRF()
));
?>

<!-- General tab -->
<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">

	<?php
	echo Bootstrap::formTitle(array('title' => $L->g('Site')));

	echo Bootstrap::formInputText(array(
		'name' => 'title',
		'label' => $L->g('Site title'),
		'value' => $site->title(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('use-this-field-to-name-your-site')
	));

	echo Bootstrap::formInputText(array(
		'name' => 'slogan',
		'label' => $L->g('Site slogan'),
		'value' => $site->slogan(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('use-this-field-to-add-a-catchy-phrase')
	));

	echo Bootstrap::formInputText(array(
		'name' => 'description',
		'label' => $L->g('Site description'),
		'value' => $site->description(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('you-can-add-a-site-description-to-provide')
	));

	echo Bootstrap::formInputText(array(
		'name' => 'footer',
		'label' => $L->g('Footer text'),
		'value' => $site->footer(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('you-can-add-a-small-text-on-the-bottom')
	));
	?>
</div>

<!-- Advanced tab -->
<div class="tab-pane fade" id="advanced" role="tabpanel" aria-labelledby="advanced-tab">
	<?php
	echo Bootstrap::formTitle(array('title' => $L->g('Content')));

	echo Bootstrap::formInputText(array(
		'name' => 'itemsPerPage',
		'label' => $L->g('Items per page'),
		'value' => $site->itemsPerPage(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Number of items to show per page')
	));

	echo Bootstrap::formSelect(array(
		'name' => 'orderBy',
		'label' => $L->g('Order content by'),
		'options' => array('date' => $L->g('Date'), 'position' => $L->g('Position')),
		'selected' => $site->orderBy(),
		'class' => '',
		'tip' => $L->g('order-the-content-by-date-to-build-a-blog')
	));

	echo Bootstrap::formTitle(array('title' => $L->g('Predefined pages')));

	// Homepage
	try {
		$options = array();
		$homeKey = $site->homepage();
		if (!empty($homeKey)) {
			$home = new Page($homeKey);
			$options = array($homeKey => $home->title());
		}
	} catch (Exception $e) {
		// continue
	}
	echo Bootstrap::formSelect(array(
		'name' => 'homepage',
		'label' => $L->g('Homepage'),
		'options' => $options,
		'selected' => false,
		'class' => '',
		'tip' => $L->g('Returning page for the main page')
	));
	?>
	<script>
		$(document).ready(function() {
			var homepage = $("#jshomepage").select2({
				placeholder: "<?php $L->p('Start typing to see a list of suggestions.') ?>",
				allowClear: true,
				theme: "bootstrap4",
				minimumInputLength: 2,
				ajax: {
					url: HTML_PATH_ADMIN_ROOT + "ajax/get-published",
					data: function(params) {
						var query = {
							query: params.term
						}
						return query;
					},
					processResults: function(data) {
						return data;
					}
				},
				escapeMarkup: function(markup) {
					return markup;
				}
			});
		});
	</script>

	<?php
	// Page not found 404
	try {
		$options = array();
		$pageNotFoundKey = $site->pageNotFound();
		if (!empty($pageNotFoundKey)) {
			$pageNotFound = new Page($pageNotFoundKey);
			$options = array($pageNotFoundKey => $pageNotFound->title());
		}
	} catch (Exception $e) {
		// continue
	}
	echo Bootstrap::formSelect(array(
		'name' => 'pageNotFound',
		'label' => $L->g('Page not found'),
		'options' => $options,
		'selected' => false,
		'class' => '',
		'tip' => $L->g('Returning page when the page doesnt exist')
	));
	?>

	<script>
		$(document).ready(function() {
			var homepage = $("#jspageNotFound").select2({
				placeholder: "<?php $L->p('Start typing to see a list of suggestions.') ?>",
				allowClear: true,
				theme: "bootstrap4",
				minimumInputLength: 2,
				ajax: {
					url: HTML_PATH_ADMIN_ROOT + "ajax/get-published",
					data: function(params) {
						var query = {
							query: params.term
						}
						return query;
					},
					processResults: function(data) {
						return data;
					}
				},
				escapeMarkup: function(markup) {
					return markup;
				}
			});
		});
	</script>

	<?php
	echo Bootstrap::formTitle(array('title' => $L->g('Email account settings')));

	echo Bootstrap::formInputText(array(
		'name' => 'emailFrom',
		'label' => $L->g('Sender email'),
		'value' => $site->emailFrom(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Emails will be sent from this address')
	));

	echo Bootstrap::formTitle(array('title' => $L->g('Autosave')));

	echo Bootstrap::formInputText(array(
		'name' => 'autosaveInterval',
		'label' => $L->g('Interval'),
		'value' => $site->autosaveInterval(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Number in minutes for every execution of autosave')
	));

	echo Bootstrap::formTitle(array('title' => $L->g('Site URL')));

	echo Bootstrap::formInputText(array(
		'name' => 'url',
		'label' => 'URL',
		'value' => $site->url(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('full-url-of-your-site'),
		'placeholder' => 'https://'
	));

	echo Bootstrap::formTitle(array('title' => $L->g('Page content')));

	echo Bootstrap::formSelect(array(
		'name' => 'markdownParser',
		'label' => $L->g('Markdown parser'),
		'options' => array('true' => $L->g('Enabled'), 'false' => $L->g('Disabled')),
		'selected' => ($site->markdownParser() ? 'true' : 'false'),
		'class' => '',
		'tip' => $L->g('Enable the markdown parser for the content of the page.')
	));

	echo Bootstrap::formTitle(array('title' => $L->g('URL Filters')));

	echo Bootstrap::formInputText(array(
		'name' => 'uriPage',
		'label' => $L->g('Pages'),
		'value' => $site->uriFilters('page'),
		'class' => '',
		'placeholder' => '',
		'tip' => DOMAIN_PAGES
	));

	echo Bootstrap::formInputText(array(
		'name' => 'uriTag',
		'label' => $L->g('Tags'),
		'value' => $site->uriFilters('tag'),
		'class' => '',
		'placeholder' => '',
		'tip' => DOMAIN_TAGS
	));

	echo Bootstrap::formInputText(array(
		'name' => 'uriCategory',
		'label' => $L->g('Category'),
		'value' => $site->uriFilters('category'),
		'class' => '',
		'placeholder' => '',
		'tip' => DOMAIN_CATEGORIES
	));

	echo Bootstrap::formInputText(array(
		'name' => 'uriBlog',
		'label' => $L->g('Blog'),
		'value' => $site->uriFilters('blog'),
		'class' => '',
		'placeholder' => '',
		'tip' => DOMAIN . $site->uriFilters('blog'),
		'disabled' => Text::isEmpty($site->uriFilters('blog'))
	));
	?>
</div>

<!-- SEO tab -->
<div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
	<?php
	echo Bootstrap::formTitle(array('title' => $L->g('Extreme friendly URL')));

	echo Bootstrap::formSelect(array(
		'name' => 'extremeFriendly',
		'label' => $L->g('Allow Unicode'),
		'options' => array('true' => $L->g('Enabled'), 'false' => $L->g('Disabled')),
		'selected' => ($site->extremeFriendly() ? 'true' : 'false'),
		'class' => '',
		'tip' => $L->g('Allow unicode characters in the URL and some part of the system.')
	));

	echo Bootstrap::formTitle(array('title' => $L->g('Title formats')));

	echo Bootstrap::formInputText(array(
		'name' => 'titleFormatHomepage',
		'label' => $L->g('Homepage'),
		'value' => $site->titleFormatHomepage(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Variables allowed') . ' <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>',
		'placeholder' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'titleFormatPages',
		'label' => $L->g('Pages'),
		'value' => $site->titleFormatPages(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Variables allowed') . ' <code>{{page-title}}</code> <code>{{page-description}}</code> <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>',
		'placeholder' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'titleFormatCategory',
		'label' => $L->g('Category'),
		'value' => $site->titleFormatCategory(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Variables allowed') . ' <code>{{category-name}}</code> <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>',
		'placeholder' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'titleFormatTag',
		'label' => $L->g('Tag'),
		'value' => $site->titleFormatTag(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Variables allowed') . ' <code>{{tag-name}}</code> <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>',
		'placeholder' => ''
	));
	?>
</div>

<!-- Social Network tab -->
<div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
	<?php
	echo Bootstrap::formInputText(array(
		'name' => 'twitter',
		'label' => 'Twitter',
		'value' => $site->twitter(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'facebook',
		'label' => 'Facebook',
		'value' => $site->facebook(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'codepen',
		'label' => 'CodePen',
		'value' => $site->codepen(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'instagram',
		'label' => 'Instagram',
		'value' => $site->instagram(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'gitlab',
		'label' => 'GitLab',
		'value' => $site->gitlab(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'github',
		'label' => 'GitHub',
		'value' => $site->github(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'linkedin',
		'label' => 'LinkedIn',
		'value' => $site->linkedin(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'xing',
		'label' => 'Xing',
		'value' => $site->xing(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'mastodon',
		'label' => 'Mastodon',
		'value' => $site->mastodon(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'dribbble',
		'label' => 'Dribbble',
		'value' => $site->dribbble(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));

	echo Bootstrap::formInputText(array(
		'name' => 'vk',
		'label' => 'VK',
		'value' => $site->vk(),
		'class' => '',
		'placeholder' => '',
		'tip' => ''
	));
	?>
</div>

<!-- Images tab -->
<div class="tab-pane fade" id="images" role="tabpanel" aria-labelledby="images-tab">
	<?php
	echo Bootstrap::formTitle(array('title' => $L->g('Thumbnails')));

	echo Bootstrap::formInputText(array(
		'name' => 'thumbnailWidth',
		'label' => $L->g('Width'),
		'value' => $site->thumbnailWidth(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Thumbnail width in pixels')
	));

	echo Bootstrap::formInputText(array(
		'name' => 'thumbnailHeight',
		'label' => $L->g('Height'),
		'value' => $site->thumbnailHeight(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Thumbnail height in pixels')
	));

	echo Bootstrap::formInputText(array(
		'name' => 'thumbnailQuality',
		'label' => $L->g('Quality'),
		'value' => $site->thumbnailQuality(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Thumbnail quality in percentage')
	));
	?>
</div>

<!-- Timezone and language tab -->
<div class="tab-pane fade" id="language" role="tabpanel" aria-labelledby="language-tab">
	<?php
	echo Bootstrap::formTitle(array('title' => $L->g('Language and timezone')));

	echo Bootstrap::formSelect(array(
		'name' => 'language',
		'label' => $L->g('Language'),
		'options' => $L->getLanguageList(),
		'selected' => $site->language(),
		'class' => '',
		'tip' => $L->g('select-your-sites-language')
	));

	echo Bootstrap::formSelect(array(
		'name' => 'timezone',
		'label' => $L->g('Timezone'),
		'options' => Date::timezoneList(),
		'selected' => $site->timezone(),
		'class' => '',
		'tip' => $L->g('select-a-timezone-for-a-correct')
	));

	echo Bootstrap::formInputText(array(
		'name' => 'locale',
		'label' => $L->g('Locale'),
		'value' => $site->locale(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('with-the-locales-you-can-set-the-regional-user-interface')
	));

	echo Bootstrap::formTitle(array('title' => $L->g('Date and time formats')));

	echo Bootstrap::formInputText(array(
		'name' => 'dateFormat',
		'label' => $L->g('Date format'),
		'value' => $site->dateFormat(),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('Current format') . ': ' . Date::current($site->dateFormat())
	));
	?>
</div>

<!-- Custom fields -->
<div class="tab-pane fade" id="custom-fields" role="tabpanel" aria-labelledby="custom-fields-tab">
	<?php
	echo Bootstrap::formTitle(array('title' => $L->g('Custom fields')));

	echo Bootstrap::formTextarea(array(
		'name' => 'customFields',
		'label' => 'JSON Format',
		'value' => json_encode($site->customFields(), JSON_PRETTY_PRINT),
		'class' => '',
		'placeholder' => '',
		'tip' => $L->g('define-custom-fields-for-the-content'),
		'rows' => 15
	));
	?>
</div>

<!-- Site logo tab -->
<div class="tab-pane fade" id="logo" role="tabpanel" aria-labelledby="logo-tab">
	<?php
	echo Bootstrap::formTitle(array('title' => $L->g('Site logo')));
	?>

	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-sm-12 p-0 pr-2">
				<div class="custom-file">
					<input id="jssiteLogoInputFile" class="custom-file-input" type="file" name="inputFile">
					<label for="jssiteLogoInputFile" class="custom-file-label"><?php $L->p('Upload image'); ?></label>
				</div>
				<button id="jsbuttonRemoveLogo" type="button" class="btn btn-primary w-100 mt-4 mb-4"><i class="fa fa-trash"></i><?php $L->p('Remove logo') ?></button>
			</div>
			<div class="col-lg-8 col-sm-12 p-0 text-center">
				<img id="jssiteLogoPreview" class="img-fluid img-thumbnail" alt="Site logo preview" src="<?php echo ($site->logo() ? DOMAIN_UPLOADS . $site->logo(false) . '?version=' . time() : HTML_PATH_CORE_IMG . 'default.svg') ?>" />
			</div>
		</div>
	</div>
	<script>
		$("#jsbuttonRemoveLogo").on("click", function() {
			bluditAjax.removeLogo();
			$("#jssiteLogoPreview").attr("src", "<?php echo HTML_PATH_CORE_IMG . 'default.svg' ?>");
		});

		$("#jssiteLogoInputFile").on("change", function() {
			var formData = new FormData();
			formData.append('tokenCSRF', tokenCSRF);
			formData.append('inputFile', $(this)[0].files[0]);
			$.ajax({
				url: HTML_PATH_ADMIN_ROOT + "ajax/logo-upload",
				type: "POST",
				data: formData,
				cache: false,
				contentType: false,
				processData: false
			}).done(function(data) {
				if (data.status == 0) {
					$("#jssiteLogoPreview").attr('src', data.absoluteURL + "?time=" + Math.random());
				} else {
					showAlert(data.message);
				}
			});
		});
	</script>
</div>

<?php echo Bootstrap::formClose(); ?>

<script>
	// Open current tab after refresh page
	$(function() {
		$('a[data-toggle="tab"]').on('click', function(e) {
			window.localStorage.setItem('activeTab', $(e.target).attr('href'));
		});
		var activeTab = window.localStorage.getItem('activeTab');
		if (activeTab) {
			$('#nav-tab a[href="' + activeTab + '"]').tab('show');
			//window.localStorage.removeItem("activeTab");
		}
	});
</script>

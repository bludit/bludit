<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	// This function catch all key press
	// Provide shortcuts for the view
	function keypress(event) {
		logs(event);

		// Shortcuts
		// ------------------------------------------------------------------------
		// Ctrl+S or Command+S
		if ((event.ctrlKey || event.metaKey) && event.which == 83) {
			event.preventDefault();
			save();
			return false;
		}

		return true;
	}

	// Save the settings
	function save() {
		var args = {}

		$('input[data-save="true"]').each(function() {
			var key = $(this).attr('name');
			var value = $(this).val();
			args[key] = value;
		});

		$('select[data-save="true"]').each(function() {
			var key = $(this).attr('name');
			var value = $(this).val();
			args[key] = value;
		});

		$('textarea[data-save="true"]').each(function() {
			var key = $(this).attr('name');
			var value = $(this).val();
			args[key] = value;
		});

        if (!isJson($('#customFields').val())) {
            logs('Invalid JSON format for custom fields.');
			showAlertError("<?php $L->p('Invalid JSON format for custom fields') ?>");
            return false;
        }

		api.saveSettings(args).then(function(response) {
			if (response.status == 0) {
				logs('Settings saved.');
				showAlertInfo("<?php $L->p('The changes have been saved') ?>");
			} else {
				logs('An error occurred while trying to save the settings.');
				showAlertError(response.message);
			}
		});

		return true;
	}

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {

		$(this).keydown(function(event) {
			keypress(event);
		});

		$('#btnSave').on('click', function() {
			save();
		});

		$('#inputSiteLogo').on("change", function(e) {
			var inputSiteLogo = $('#inputSiteLogo')[0].files;
			var formData = new FormData();
			formData.append("file", inputSiteLogo[0]);
			formData.append("token", api.body.token);
			formData.append("authentication", api.body.authentication);
			$.ajax({
				url: api.apiURL + 'settings/logo',
				type: "POST",
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				xhr: function() {
					var xhr = $.ajaxSettings.xhr();
					if (xhr.upload) {
						xhr.upload.addEventListener("progress", function(e) {
							if (e.lengthComputable) {
								var percentComplete = (e.loaded / e.total) * 100;
								logs('Uploading site logo: ' + percentComplete + '%');
							}
						}, false);
					}
					return xhr;
				}
			}).done(function(response) {
				logs(response);
				if (response.status == 0) {
					logs("Site logo uploaded.");
					showAlertInfo("<?php $L->p('The changes have been saved') ?>");
					$('#siteLogoPreview').attr('src', response.data.absoluteURL);
				} else {
					logs("An error occurred while trying to upload the site logo.");
					showAlertError(response.message);
				}
			});
			return true;
		});

		$('#btnRemoveSiteLogo').on('click', function() {
			bootbox.confirm({
				message: '<?php $L->p('Are you sure you want to delete the site logo') ?>',
				buttons: {
					cancel: {
						label: '<i class="fa fa-times"></i><?php $L->p('Cancel') ?>',
						className: 'btn-sm btn-secondary'
					},
					confirm: {
						label: '<i class="fa fa-check"></i><?php $L->p('Confirm') ?>',
						className: 'btn-sm btn-primary'
					}
				},
				closeButton: false,
				callback: function(result) {
					if (result) {
						api.deleteSiteLogo().then(function(response) {
							if (response.status == 0) {
								logs('Site logo deleted.');
								showAlertInfo("<?php $L->p('The changes have been saved') ?>");
								$('#siteLogoPreview').attr('src', '<?php echo HTML_PATH_CORE_IMG . 'default.svg' ?>');
							} else {
								logs("An error occurred while trying to delete the site logo.");
								showAlertError(response.message);
							}
						});
						return true;
					}
				}
			});
		});

	});

	// ============================================================================
	// Initlization for the view
	// ============================================================================
	$(document).ready(function() {
		$("#homepage").select2({
			placeholder: "Search for a page",
			allowClear: true,
			theme: "bootstrap-5",
			minimumInputLength: 2,
			ajax: {
				url: HTML_PATH_ADMIN_ROOT+"ajax/get-published",
				data: function (params) {
					var query = { query: params.term }
					return query;
				},
				processResults: function (data) {
					return data;
				},
				escapeMarkup: function(markup) {
					return markup;
				}
			}
		});

		$("#pageNotFound").select2({
			placeholder: "Search for a page",
			allowClear: true,
			theme: "bootstrap-5",
			minimumInputLength: 2,
			ajax: {
				url: HTML_PATH_ADMIN_ROOT+"ajax/get-published",
				data: function (params) {
					var query = { query: params.term }
					return query;
				},
				processResults: function (data) {
					return data;
				},
				escapeMarkup: function(markup) {
					return markup;
				}
			}
		});
	});
</script>

<div class="d-flex align-items-center mb-4">
	<h2 class="m-0"><i class="bi bi-gear"></i><?php $L->p('Settings') ?></h2>
	<div class="ms-auto">
		<button id="btnSave" type="button" class="btn btn-primary btn-sm"><?php $L->p('Save') ?></button>
		<a id="btnCancel" class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'users' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs ps-3 mb-3" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true"><?php $L->p('general') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="advanced-tab" data-bs-toggle="tab" href="#advanced" role="tab" aria-controls="advanced" aria-selected="false"><?php $L->p('advanced') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="seo-tab" data-bs-toggle="tab" href="#seo" role="tab" aria-controls="seo" aria-selected="false"><?php $L->p('seo') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="social-tab" data-bs-toggle="tab" href="#social" role="tab" aria-controls="social" aria-selected="false"><?php $L->p('Social Networks') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="images-tab" data-bs-toggle="tab" href="#images" role="tab" aria-controls="images" aria-selected="false"><?php $L->p('images') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="language-tab" data-bs-toggle="tab" href="#language" role="tab" aria-controls="language" aria-selected="false"><?php $L->p('language') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="custom-fields-tab" data-bs-toggle="tab" href="#custom-fields" role="tab" aria-controls="custom-fields" aria-selected="false"><?php $L->p('custom-fields') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="logo-tab" data-bs-toggle="tab" href="#logo" role="tab" aria-controls="logo" aria-selected="false"><?php $L->p('Site logo') ?></a>
	</li>
</ul>
<!-- End Tabs -->

<!-- Content -->
<div class="tab-content" id="tabContent">

	<!-- General tab -->
	<div class="tab-pane show active" id="general" role="tabpanel">
		<?php
		echo Bootstrap::formTitle(array('title' => $L->g('Site')));

		echo Bootstrap::formInputText(array(
			'name' => 'title',
			'label' => $L->g('Site title'),
			'value' => $site->title(),
			'tip' => $L->g('use-this-field-to-name-your-site'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'slogan',
			'label' => $L->g('Site slogan'),
			'value' => $site->slogan(),
			'tip' => $L->g('use-this-field-to-add-a-catchy-phrase'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'description',
			'label' => $L->g('Site description'),
			'value' => $site->description(),
			'tip' => $L->g('you-can-add-a-site-description-to-provide'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'footer',
			'label' => $L->g('Footer text'),
			'value' => $site->footer(),
			'tip' => $L->g('you-can-add-a-small-text-on-the-bottom'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formTitle(array('title' => $L->g('Dark Mode')));

		echo Bootstrap::formSelect(array(
			'name' => 'darkModeAdmin',
			'label' => $L->g('Admin panel'),
			'options' => array('true' => $L->g('Enabled'), 'false' => $L->g('Disabled')),
			'selected' => ($site->darkModeAdmin() ? 'true' : 'false'),
			'tip' => $L->g('Enable dark mode for the admin panel. The theme has to support this feature.'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formTitle(array('title' => $L->g('Autosave')));

		echo Bootstrap::formInputText(array(
			'name' => 'autosaveInterval',
			'label' => $L->g('Interval'),
			'value' => $site->autosaveInterval(),
			'tip' => $L->g('Number in minutes for every execution of autosave'),
			'data' => array('save' => 'true')
		));


		echo Bootstrap::formTitle(array('title' => $L->g('Content')));

		echo Bootstrap::formSelect(array(
			'name' => 'itemsPerPage',
			'label' => $L->g('Items per page'),
			'options' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '-1' => $L->g('All content')),
			'selected' => $site->itemsPerPage(),
			'tip' => $L->g('Number of items to show per page'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formSelect(array(
			'name' => 'orderBy',
			'label' => $L->g('Order content by'),
			'options' => array('date' => $L->g('Date'), 'position' => $L->g('Position')),
			'selected' => $site->orderBy(),
			'tip' => $L->g('order-the-content-by-date-to-build-a-blog'),
			'data' => array('save' => 'true')
		));
		?>
	</div>
	<!-- End General tab -->

	<!-- Advanced tab -->
	<div class="tab-pane" id="advanced" role="tabpanel">
		<?php
		echo Bootstrap::formTitle(array('title' => $L->g('Page content')));

		echo Bootstrap::formSelect(array(
			'name' => 'markdownParser',
			'label' => $L->g('Markdown parser'),
			'options' => array('true' => $L->g('Enabled'), 'false' => $L->g('Disabled')),
			'selected' => ($site->markdownParser() ? 'true' : 'false'),
			'tip' => $L->g('Enable the markdown parser for the content of the page.'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formTitle(array('title' => $L->g('Predefined pages')));

		try {
			$options = array();
			if (!empty($site->homepage())) {
				$tmp = new Page($site->homepage());
				$options = array($site->homepage()=>$tmp->title());
			}
		} catch (Exception $e) {
			// continue
		}
		echo Bootstrap::formSelect(array(
			'name' => 'homepage',
			'label' => $L->g('Homepage'),
			'options' => $options, // Complete via Ajax
			'selected' => false,
			'tip' => $L->g('Returning page for the main page'),
			'data' => array('save' => 'true')
		));

		try {
			$options = array();
			if (!empty($site->pageNotFound())) {
				$tmp = new Page($site->pageNotFound());
				$options = array($site->pageNotFound()=>$tmp->title());
			}
		} catch (Exception $e) {
			// continue
		}
		echo Bootstrap::formSelect(array(
			'name' => 'pageNotFound',
			'label' => $L->g('Page not found'),
			'options' => $options, // Complete via Ajax
			'selected' => false,
			'tip' => $L->g('Returning page when the page doesnt exist'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formTitle(array('title' => $L->g('Email account settings')));

		echo Bootstrap::formInputText(array(
			'name' => 'emailFrom',
			'label' => $L->g('Sender email'),
			'value' => $site->emailFrom(),
			'tip' => $L->g('Emails will be sent from this address'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formTitle(array('title' => $L->g('Site URL')));

		echo Bootstrap::formInputText(array(
			'name' => 'url',
			'label' => 'URL',
			'value' => $site->url(),
			'tip' => $L->g('full-url-of-your-site'),
			'placeholder' => 'https://',
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formTitle(array('title' => $L->g('URL Filters')));

		echo Bootstrap::formInputText(array(
			'name' => 'uriPage',
			'label' => $L->g('Pages'),
			'value' => $site->uriFilters('page'),
			'tip' => DOMAIN_PAGES,
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'uriTag',
			'label' => $L->g('Tags'),
			'value' => $site->uriFilters('tag'),
			'tip' => DOMAIN_TAGS,
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'uriCategory',
			'label' => $L->g('Category'),
			'value' => $site->uriFilters('category'),
			'tip' => DOMAIN_CATEGORIES,
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'uriBlog',
			'label' => $L->g('Blog'),
			'value' => $site->uriFilters('blog'),
			'tip' => DOMAIN . $site->uriFilters('blog'),
			'disabled' => Text::isEmpty($site->uriFilters('blog')),
			'data' => array('save' => 'true')
		));
		?>
	</div>

	<!-- SEO tab -->
	<div class="tab-pane" id="seo" role="tabpanel" aria-labelledby="seo-tab">
		<?php
		echo Bootstrap::formTitle(array('title' => $L->g('Extreme friendly URL')));

		echo Bootstrap::formSelect(array(
			'name' => 'extremeFriendly',
			'label' => $L->g('Allow Unicode'),
			'options' => array('true' => $L->g('Enabled'), 'false' => $L->g('Disabled')),
			'selected' => ($site->extremeFriendly() ? 'true' : 'false'),
			'tip' => $L->g('Allow unicode characters in the URL and some part of the system.'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formTitle(array('title' => $L->g('Title formats')));

		echo Bootstrap::formInputText(array(
			'name' => 'titleFormatHomepage',
			'label' => $L->g('Homepage'),
			'value' => $site->titleFormatHomepage(),
			'tip' => $L->g('Variables allowed') . ' <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>',
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'titleFormatPages',
			'label' => $L->g('Pages'),
			'value' => $site->titleFormatPages(),
			'tip' => $L->g('Variables allowed') . ' <code>{{page-title}}</code> <code>{{page-description}}</code> <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>',
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'titleFormatCategory',
			'label' => $L->g('Category'),
			'value' => $site->titleFormatCategory(),
			'class' => '',
			'tip' => $L->g('Variables allowed') . ' <code>{{category-name}}</code> <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>',
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'titleFormatTag',
			'label' => $L->g('Tag'),
			'value' => $site->titleFormatTag(),
			'tip' => $L->g('Variables allowed') . ' <code>{{tag-name}}</code> <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>',
			'data' => array('save' => 'true')
		));
		?>
	</div>

	<!-- Social Network tab -->
	<div class="tab-pane" id="social" role="tabpanel" aria-labelledby="social-tab">
		<?php
		echo Bootstrap::formTitle(array('title' => $L->g('Social Networks')));

		echo Bootstrap::formInputText(array(
			'name' => 'youtube',
			'label' => 'YouTube',
			'value' => $site->youtube(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'twitter',
			'label' => 'Twitter',
			'value' => $site->twitter(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'facebook',
			'label' => 'Facebook',
			'value' => $site->facebook(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'discord',
			'label' => 'Discord',
			'value' => $site->discord(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'codepen',
			'label' => 'CodePen',
			'value' => $site->codepen(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'instagram',
			'label' => 'Instagram',
			'value' => $site->instagram(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'gitlab',
			'label' => 'GitLab',
			'value' => $site->gitlab(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'github',
			'label' => 'GitHub',
			'value' => $site->github(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'linkedin',
			'label' => 'LinkedIn',
			'value' => $site->linkedin(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'xing',
			'label' => 'Xing',
			'value' => $site->xing(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'mastodon',
			'label' => 'Mastodon',
			'value' => $site->mastodon(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'dribbble',
			'label' => 'Dribbble',
			'value' => $site->dribbble(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'vk',
			'label' => 'VK',
			'value' => $site->vk(),
			'data' => array('save' => 'true')
		));
		?>
	</div>

	<!-- Images tab -->
	<div class="tab-pane" id="images" role="tabpanel" aria-labelledby="images-tab">
		<?php
		echo Bootstrap::formTitle(array('title' => $L->g('Thumbnail small')));

		echo Bootstrap::formInputText(array(
			'name' => 'thumbnailSmallWidth',
			'label' => $L->g('Width'),
			'value' => $site->thumbnailSmallWidth(),
			'tip' => $L->g('Thumbnail width in pixels'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'thumbnailSmallHeight',
			'label' => $L->g('Height'),
			'value' => $site->thumbnailSmallHeight(),
			'tip' => $L->g('Thumbnail height in pixels'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'thumbnailSmallQuality',
			'label' => $L->g('Quality'),
			'value' => $site->thumbnailSmallQuality(),
			'tip' => $L->g('Thumbnail quality in percentage'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formTitle(array('title' => $L->g('Thumbnail medium')));

		echo Bootstrap::formInputText(array(
			'name' => 'thumbnailMediumWidth',
			'label' => $L->g('Width'),
			'value' => $site->thumbnailMediumWidth(),
			'tip' => $L->g('Thumbnail width in pixels'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'thumbnailMediumHeight',
			'label' => $L->g('Height'),
			'value' => $site->thumbnailMediumHeight(),
			'tip' => $L->g('Thumbnail height in pixels'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'thumbnailMediumQuality',
			'label' => $L->g('Quality'),
			'value' => $site->thumbnailMediumQuality(),
			'tip' => $L->g('Thumbnail quality in percentage'),
			'data' => array('save' => 'true')
		));
		?>
	</div>

	<!-- Timezone and language tab -->
	<div class="tab-pane" id="language" role="tabpanel" aria-labelledby="language-tab">
		<?php
		echo Bootstrap::formTitle(array('title' => $L->g('Language and timezone')));

		echo Bootstrap::formSelect(array(
			'name' => 'language',
			'label' => $L->g('Language'),
			'options' => $L->getLanguageList(),
			'selected' => $site->language(),
			'tip' => $L->g('select-your-sites-language'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formSelect(array(
			'name' => 'timezone',
			'label' => $L->g('Timezone'),
			'options' => Date::timezoneList(),
			'selected' => $site->timezone(),
			'tip' => $L->g('select-a-timezone-for-a-correct'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'locale',
			'label' => $L->g('Locale'),
			'value' => $site->locale(),
			'tip' => $L->g('with-the-locales-you-can-set-the-regional-user-interface'),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formTitle(array('title' => $L->g('Date and time formats')));

		echo Bootstrap::formInputText(array(
			'name' => 'dateFormat',
			'label' => $L->g('Date format'),
			'value' => $site->dateFormat(),
			'tip' => $L->g('Current format') . ': ' . Date::current($site->dateFormat()),
			'data' => array('save' => 'true')
		));
		?>
	</div>

	<!-- Custom fields -->
	<div class="tab-pane" id="custom-fields" role="tabpanel" aria-labelledby="custom-fields-tab">
		<?php
		echo Bootstrap::formTitle(array('title' => $L->g('Custom fields')));

		echo Bootstrap::formTextarea(array(
			'name' => 'customFields',
			'label' => 'JSON Format',
			'value' => json_encode($site->customFields(), JSON_PRETTY_PRINT),
			'tip' => $L->g('define-custom-fields-for-the-content'),
			'rows' => 15,
			'data' => array('save' => 'true'),
            'disable-current-value' => false
		));
		?>
	</div>

	<!-- Site logo tab -->
	<div class="tab-pane" id="logo" role="tabpanel" aria-labelledby="logo-tab">
		<div class="container">
			<div class="row">
				<div class="col-8">
					<img id="siteLogoPreview" class="img-fluid img-thumbnail" alt="Site logo preview" src="<?php echo ($site->logo()?$site->logo():HTML_PATH_CORE_IMG . 'default.svg') ?>" />
				</div>
				<div class="col-4">
					<label id="btnUploadProfilePicture" class="btn btn-primary"><i class="bi bi-upload"></i><?php $L->p('Upload image'); ?><input type="file" id="inputSiteLogo" name="inputSiteLogo" hidden></label>
					<button id="btnRemoveSiteLogo" type="button" class="btn btn-secondary"><i class="bi bi-trash"></i><?php $L->p('Remove image'); ?></button>
				</div>
			</div>
		</div>
	</div>

</div>
<!-- End Content -->
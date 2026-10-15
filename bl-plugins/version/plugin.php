<?php

class pluginVersion extends Plugin
{

	public function init()
	{
		$this->dbFields = array(
			'showCurrentVersion' => true,
			'newVersionAlert' => true
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div>';
		$html .= '<label>' . $L->get('Show current version in the sidebar') . '</label>';
		$html .= '<select name="showCurrentVersion">';
		$html .= '<option value="true" ' . ($this->getValue('showCurrentVersion') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('showCurrentVersion') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Show alert when there is a new version in the sidebar') . '</label>';
		$html .= '<select name="newVersionAlert">';
		$html .= '<option value="true" ' . ($this->getValue('newVersionAlert') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('newVersionAlert') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function adminSidebar()
	{
		global $L;
		$html = '';
		if ($this->getValue('showCurrentVersion')) {
			$html = '<a id="current-version" class="current-version nav-link" href="' . HTML_PATH_ADMIN_ROOT . 'version' . '">' . $L->get('Version') . ' ' . (defined('BLUDIT_PRO') ? '<span class="bi-heart" style="color: #ffc107"></span>' : '') . '<span class="badge bg-warning rounded-pill">' . BLUDIT_VERSION . '</span></a>';
		}
		if ($this->getValue('newVersionAlert')) {
			$html .= '<a id="new-version" class="new-version" style="display: none;" target="_blank" href="https://www.bludit.com">' . $L->get('New version available') . ' <i class="fa fa-bell text-danger"></i></a>';
		}
		return $html;
	}

	public function adminBodyEnd()
	{
		if ($this->getValue('newVersionAlert')) {
			// The follow Javascript get via AJAX the information about new versions
			// The script is on /bl-plugins/version/js/version.js
			$jsPath = $this->phpPath() . 'js' . DS;
			$scripts  = '<script>' . file_get_contents($jsPath . 'version.js') . '</script>';
			return $scripts;
		}
		return false;
	}

	// This plugin is the only thing allowed to reach out to bludit.com, the
	// mechanism to check and install updates lives here instead of core so it
	// doesn't exist at all when the plugin is disabled
	private function loadCoreUpdater()
	{
		if (!defined('CORE_UPDATE_URL')) {
			// Updates of Bludit, core.json describes the last release of Bludit
			// and it's signed, the signature is verified with this public key
			// before trusting it
			define('CORE_UPDATE_URL', 'https://bludit.com/release/core.json');
			define('CORE_UPDATE_PUBLIC_KEY', 'niZhKjSwwQYmedL+v2SvrNROHyxWn6elld63DcEbEPY=');

			// Schema version of core.json supported by this version of Bludit
			define('CORE_UPDATE_SCHEMA', 1);

			// Seconds to keep core.json cached before download it again
			define('CORE_UPDATE_CACHE_TTL', 21600); // 6 hours

			// Maximum size allowed for the zip file of a release of Bludit
			define('CORE_UPDATE_MAX_ZIP_SIZE', 52428800); // 50 MB

			// Maximum size allowed for the content of the zip file of a release, uncompressed
			define('CORE_UPDATE_MAX_UNCOMPRESSED_SIZE', 209715200); // 200 MB

			// Hosts allowed to download the releases of Bludit from
			$GLOBALS['CORE_UPDATE_ALLOWED_HOSTS'] = array(
				'bludit.com'
			);
		}

		require_once($this->phpPath() . 'coreupdater.class.php');
	}

	// Renders the Updates section of the admin Version page
	public function renderUpdates()
	{
		global $L, $security;

		$this->loadCoreUpdater();

		$html = '';
		$release = CoreUpdater::getRelease();
		if ($release === false) {
			$html .= '<p>' . $L->g('Unable to check the updates of Bludit') . '</p>';
		} elseif (!CoreUpdater::available($release)) {
			$html .= '<p>' . $L->g('You have the last version of Bludit') . '</p>';
		} else {
			$newVersion = Sanitize::html($release['version']);
			$download = CoreUpdater::allowedURL($release['download']) ? Sanitize::html($release['download']) : '';
			$notWritable = CoreUpdater::notWritable();

			$html .= '<p>' . sprintf($L->g('Bludit %s is available'), $newVersion) . '</p>';

			if (!CoreUpdater::phpCompatible($release)) {
				$html .= '<div class="alert alert-warning">' . sprintf($L->g('The new version of Bludit requires PHP %s'), Sanitize::html($release['php'])) . '</div>';
			} elseif (!empty($notWritable)) {
				// The update is not possible, the files are replaced by hand
				$html .= '<div class="alert alert-warning">' . $L->g('Bludit can not write in these directories') . ' <code>' . Sanitize::html(implode(', ', $notWritable)) . '</code>. ' . $L->g('Update Bludit manually replacing the files') . '</div>';
				if ($download !== '') {
					$html .= '<a class="btn btn-primary" href="' . $download . '">' . $L->g('Download') . ' Bludit ' . $newVersion . '</a>';
				}
			} else {
				if (defined('BLUDIT_PRO') && CoreUpdater::changesMinorVersion($release)) {
					$html .= '<div class="alert alert-warning">' . sprintf($L->g('The Bludit PRO license is for this version, after the update download the license for Bludit %s from Patreon'), $newVersion) . '</div>';
				}
				$html .= '<p class="text-muted">' . $L->g('The content and the settings are not modified, the previous version is restored if the update fails') . '</p>';
				$confirmation = Sanitize::html(addslashes(sprintf($L->g('Update Bludit to the version %s?'), $release['version'])));
				$updating = Sanitize::html(addslashes($L->g('Updating Bludit, this can take a minute, do not close this page')));
				$html .= '<form method="post" action="' . HTML_PATH_ADMIN_ROOT . 'version" onsubmit="if(!confirm(\'' . $confirmation . '\')){return false;} var b=this.querySelector(\'button\'); b.disabled=true; b.innerHTML=\'<span class=&quot;fa fa-spinner fa-spin&quot;></span> ' . $updating . '\'; return true;">';
				$html .= '<input type="hidden" name="tokenCSRF" value="' . $security->getTokenCSRF() . '">';
				$html .= '<button type="submit" class="btn btn-primary">' . $L->g('Update') . ' Bludit ' . $newVersion . '</button>';
				$html .= '</form>';
			}
		}
		return $html;
	}

	// Handles the POST of the update form on the admin Version page
	public function handleUpdatePost()
	{
		global $L;

		checkRole(array('admin'));

		$this->loadCoreUpdater();

		// The download and the copy can take longer than the default limit
		@set_time_limit(300);

		if (CoreUpdater::update() === false) {
			Alert::set(CoreUpdater::$error, ALERT_STATUS_FAIL);
		} else {
			Alert::set($L->g('Bludit has been updated'));
		}

		Redirect::page('version');
	}
}

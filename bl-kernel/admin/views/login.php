<?php defined('BLUDIT') or die('Bludit CMS.');

// Logo and title
$logoPath = HTML_PATH_CORE_IMG . 'logo.svg';
$logoClass = 'logo-icon';
if (defined('BLUDIT_PRO') && $site->logo(false)) {
	$logoPath = $site->logo(true);
	$logoClass = 'logo-icon custom-logo';
}

echo '
<div class="login-logo">
	<div class="' . $logoClass . '">
		<img src="' . $logoPath . '" alt="Logo">
	</div>
	<h1>' . (defined('BLUDIT_PRO') ? Sanitize::html($site->title()) : 'BLUDIT') . '</h1>
</div>
';

echo Bootstrap::formOpen(array());

echo Bootstrap::formInputHidden(array(
	'name' => 'tokenCSRF',
	'value' => $security->getTokenCSRF()
));

// Username field with icon
echo '
<div class="form-group">
	<label for="jsusername">' . $L->g('Username') . '</label>
	<div class="input-icon-wrapper">
		<input type="text"
			dir="auto"
			value="' . (isset($_POST['username']) ? Sanitize::html($_POST['username']) : '') . '"
			class="form-control"
			id="jsusername"
			name="username"
			placeholder="' . $L->g('Username') . '"
			autocomplete="username"
			autofocus>
		<span class="input-icon">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
				<circle cx="12" cy="7" r="4"></circle>
			</svg>
		</span>
	</div>
</div>
';

// Password field with icon
echo '
<div class="form-group">
	<label for="jspassword">' . $L->g('Password') . '</label>
	<div class="input-icon-wrapper">
		<input type="password"
			class="form-control"
			id="jspassword"
			name="password"
			placeholder="' . $L->g('Password') . '"
			autocomplete="current-password">
		<span class="input-icon">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
				<path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
			</svg>
		</span>
	</div>
</div>
';

// Remember me checkbox
echo '
<div class="form-check">
	<input class="form-check-input" type="checkbox" value="true" id="jsremember" name="remember">
	<label class="form-check-label" for="jsremember">' . $L->g('Remember me') . '</label>
</div>
';

// Submit button
echo '
<button type="submit" class="btn btn-login" name="save">
	<span>' . $L->g('Login') . '</span>
	<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px; vertical-align: middle;">
		<path d="M5 12h14"></path>
		<path d="M12 5l7 7-7 7"></path>
	</svg>
</button>
';

echo '</form>';

// Footer
if (!defined('BLUDIT_PRO')) {
	echo '
	<div class="login-footer">
		<p>Powered by <a href="https://www.bludit.com" target="_blank" rel="noopener">Bludit</a></p>
	</div>
	';
}

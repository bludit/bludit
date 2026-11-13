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
			autofocus
			required>
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
			class="form-control has-password-toggle"
			id="jspassword"
			name="password"
			placeholder="' . $L->g('Password') . '"
			autocomplete="current-password"
			required>
		<span class="input-icon">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
				<path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
			</svg>
		</span>
		<button type="button" class="password-toggle" id="jspasswordToggle" aria-controls="jspassword" aria-pressed="false" aria-label="' . Sanitize::html($L->g('Show password')) . '" title="' . Sanitize::html($L->g('Show password')) . '">
			<svg class="icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
				<circle cx="12" cy="12" r="3"></circle>
			</svg>
			<svg class="icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden>
				<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
				<path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c6.5 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
				<path d="M6.61 6.61A13.53 13.53 0 0 0 2 12s3.5 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
				<line x1="2" y1="2" x2="22" y2="22"></line>
			</svg>
		</button>
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

// Show/hide password
echo '
<script>
(function() {
	var input = document.getElementById("jspassword");
	var toggle = document.getElementById("jspasswordToggle");
	if (!input || !toggle) {
		return;
	}
	var labels = {
		show: ' . json_encode($L->g('Show password')) . ',
		hide: ' . json_encode($L->g('Hide password')) . '
	};

	// SVG elements have no "hidden" property, toggle the attribute
	function setHidden(element, hidden) {
		if (hidden) {
			element.setAttribute("hidden", "");
		} else {
			element.removeAttribute("hidden");
		}
	}

	// Keep the focus (and the phone keyboard) on the password field
	toggle.addEventListener("mousedown", function(event) {
		event.preventDefault();
	});

	toggle.addEventListener("click", function() {
		var show = input.type === "password";
		input.type = show ? "text" : "password";
		toggle.setAttribute("aria-pressed", show ? "true" : "false");
		toggle.setAttribute("aria-label", show ? labels.hide : labels.show);
		toggle.title = show ? labels.hide : labels.show;
		setHidden(toggle.querySelector(".icon-eye"), show);
		setHidden(toggle.querySelector(".icon-eye-off"), !show);
	});

	// Never submit the form with the password visible
	input.form.addEventListener("submit", function() {
		input.type = "password";
	});
})();
</script>
';

// Footer
if (!defined('BLUDIT_PRO')) {
	echo '
	<div class="login-footer">
		<p>Powered by <a href="https://www.bludit.com" target="_blank" rel="noopener">Bludit</a></p>
	</div>
	';
}

<?php

class pluginGDPRCookie extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'enabled'=>true,
			'message'=>'Allow to Google Analytics tracking you?'
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>Show concent to the user</label>';
		$html .= '<select name="enabled">';
		$html .= '<option value="true" '.($this->getValue('enabled')===true?'selected':'').'>'.$Language->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('enabled')===false?'selected':'').'>'.$Language->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '<span class="tip">Show the consent to the user before loading the script of Google Analytics. The user can reject be tracked for Google Analytics.</span>';
		$html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
		$html = '<style>
				#pluginGDPRCookie {
					display: block;
					position: fixed;
					top: 0;
					background: #eee;
					padding: 2px 10px;
					font-size: 1em;
					color: #555;
					z-index: 2000;
					width: 100%;
					padding: 30px;
					text-align: center;
					border-bottom: 1px solid #999;
				}

				#pluginGDPRCookie div.buttons {
					margin-top: 10px;
				}

				#pluginGDPRCookie button {
					margin-right: 10px;
				}
			}
			</style>';

		return $html;
	}

	public function siteBodyBegin()
	{
$script = <<<EOT
<script>
	function pluginGDPRCookie_allowTracking() {
		setCookie("BLUDIT_GDPR_ALLOW_TRACKING", true, 7);
		pluginGDPRCookie_hideModal();
	}

	function pluginGDPRCookie_disableTracking() {
		setCookie("BLUDIT_GDPR_ALLOW_TRACKING", false, 7);
		pluginGDPRCookie_hideModal();
	}

	function pluginGDPRCookie_hideModal() {
		document.getElementById("pluginGDPRCookie").style.display = "none";
	}
</script>
EOT;

		if ($this->getValue('enabled')) {
			$html  = '<div id="pluginGDPRCookie">';
			$html .= '<div>Allow to Google Analytics tracking you?</div>';
			$html .= '<div class="buttons">';
			$html .= '<button id="pluginGDPRCookie_allowButton">Allow</button>';
			$html .= '<button id="pluginGDPRCookie_denyButton">Deny</button>';
			$html .= '</div>';
			$html .= '</div>';
		} else {
			$html = '<script> window.onload=function() { allowTracking(); }</script>';
		}

		$script .= '
			<script>
			window.onload=function() {
				document.getElementById("pluginGDPRCookie_allowButton").addEventListener("click", pluginGDPRCookie_allowTracking);
				document.getElementById("pluginGDPRCookie_denyButton").addEventListener("click", pluginGDPRCookie_disableTracking);
			}
			</script>
		';

		return $script.$html;
	}
}

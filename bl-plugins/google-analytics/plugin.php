<?php

class pluginGoogleAnalytics extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'EUGDPR'=>true,
			'trackingID'=>''
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label for="jstrackingID">'.$Language->get('Google Analytics Tracking ID').'</label>';
		$html .= '<input id="jstrackingID" type="text" name="trackingID" value="'.$this->getValue('trackingID').'">';
		$html .= '<span class="tip">'.$Language->get('complete-this-field-with-the-tracking-id').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>EU General Data Protection</label>';
		$html .= '<select name="EUGDPR">';
		$html .= '<option value="true" '.($this->getValue('EUGDPR')===true?'selected':'').'>'.$Language->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('EUGDPR')===false?'selected':'').'>'.$Language->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '<span class="tip">Show the consent to the user before loading the script of Google Analytics. The user can reject be tracked for Google Analytics.</span>';
		$html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
		$html = '<style>
				#pluginGoogleAnalytics {
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

				#pluginGoogleAnalytics div.buttons {
					margin-top: 10px;
				}

				#pluginGoogleAnalytics button {
					margin-right: 10px;
				}
			}
			</style>';

		return $html;
	}

	public function siteBodyBegin()
	{
		$script = '
			<script>
			function executeGoogleAnalytics() {

				var s = document.createElement("script");
				s.src = "https://www.googletagmanager.com/gtag/js?id='.$this->getValue('trackingID').'";
				document.body.appendChild(s);

				var s = document.createElement("script");
				s.text = \'window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag("js", new Date()); gtag("config", "'.$this->getValue('trackingID').'");\';
				document.body.appendChild(s);

				hideGoogleAnalyticsDialog();
			}

			function hideGoogleAnalyticsDialog() {
				document.getElementById("pluginGoogleAnalytics").style.display = "none";
			}

			</script>
		';

		if ($this->getValue('EUGDPR')) {
			$html  = '<div id="pluginGoogleAnalytics">';
			$html .= '<div>Allow to Google Analytics tracking you?</div>';
			$html .= '<div class="buttons">';
			$html .= '<button id="pluginGoogleAnalytics_allowButton">Allow</button>';
			$html .= '<button id="pluginGoogleAnalytics_denyButton">Deny</button>';
			$html .= '</div>';
			$html .= '</div>';
		} else {
			$html = '<script> window.onload=function() { executeGoogleAnalytics(); }</script>';
		}

		$script .= '
			<script>
			window.onload=function() {
				document.getElementById("pluginGoogleAnalytics_allowButton").addEventListener("click", executeGoogleAnalytics);
				document.getElementById("pluginGoogleAnalytics_denyButton").addEventListener("click", hideGoogleAnalyticsDialog);
			}
			</script>
		';

		return $script.$html;
	}
}

<?php

class pluginAbout extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'About',
			'text'=>'',
			'facebook'=>'',
			'twitter'=>'',
			'instagram'=>'',
			'googleplus'=>''
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Plugin label').'</label>';
		$html .= '<input name="label" id="jslabel" type="text" value="'.$this->getDbField('label').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('About').'</label>';
		$html .= '<textarea name="text" id="jstext">'.$this->getDbField('text').'</textarea>';
		$html .= '</div>';

		$html .= '<legend>Social networks</legend>';

		$html .= '<div>';
		$html .= '<label>Facebook</label>';
		$html .= '<input name="facebook" placeholder="https://www.facebook.com/USERNAME" id="jsfacebook" type="text" value="'.$this->getDbField('facebook').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Twitter</label>';
		$html .= '<input name="twitter" placeholder="https://www.twitter.com/USERNAME" id="jstwitter" type="text" value="'.$this->getDbField('twitter').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>instagram</label>';
		$html .= '<input name="instagram" placeholder="https://www.instagram.com/USERNAME" id="jsinstagram" type="text" value="'.$this->getDbField('instagram').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Google+</label>';
		$html .= '<input name="googleplus" placeholder="https://plus.google.com/+USERNAME" id="jsgoogleplus" type="text" value="'.$this->getDbField('googleplus').'">';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		global $Language;
		global $dbTags;
		global $Url;

		$filter = $Url->filters('tag');

		$html  = '<div class="plugin plugin-about">';
		$html .= '<h2>'.$this->getDbField('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= $this->getDbField('text');
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}
<?php

class pluginCustomFieldsParser extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'Custom fields parser',
			'jsondb'=>json_encode(array())
		);
	}

	public function form()
	{
		global $L;
		global $site;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$jsondb = $this->getValue('jsondb', false);
		$database = json_decode($jsondb, true);

		$customFields = $site->customFields();

		foreach ($customFields as $field=>$options) {
			if ($options['type']=="string") {
				$html .= '<div>';
				$html .= '<label>'.$options['label'].'</label>';
				$html .= '<textarea name="'.$field.'">'.(isset($database[$field])?$database[$field]:'').'</textarea>';
				$html .= '</div>';
			}
		}

		return $html;
	}

	public function post()
	{
		$this->db['jsondb'] = Sanitize::html(json_encode($_POST));
		return $this->save();
	}

	public function parse($page)
	{
		$jsondb = $this->getValue('jsondb', false);
		$database = json_decode($jsondb, true);
		$parsedCode = array();

		foreach ($database as $field=>$code) {
			$value = $page->custom($field);
			$parsedCode['{{ '.$field.' }}'] = str_replace('{{ value }}', $value, $code);
		}

		$content = $page->contentRaw();
		return str_replace(array_keys($parsedCode), array_values($parsedCode), $content);
	}

	public function beforeSiteLoad()
	{
		if ($GLOBALS['WHERE_AM_I']=='page') {
			$GLOBALS['page']->setField('content', $this->parse($GLOBALS['page']));
		} else {
			foreach ($GLOBALS['content'] as $key=>$page)  {
				$GLOBALS['content'][$key]->setField('content', $this->parse($GLOBALS['content'][$key]));
			}
			$GLOBALS['page'] = $GLOBALS['content'][0];
		}
	}
}
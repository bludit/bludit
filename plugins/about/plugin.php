<?php

class pluginAbout extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'title'=>'',
			'description'=>''
			);
	}	

	public function onSiteHead()
	{
		$html = '<title>Blog &ndash; Layout Examples &ndash; Pure</title>';
		return $html;
	}


}

?>

<?php

class pluginTimeMachine extends Plugin {

	public function init()
	{

	}

	public function beforeAdminLoad()
	{
		global $L;

		$notifyText = '';
		if($_SERVER['REQUEST_METHOD']=='POST') {
			if($GLOBALS['ADMIN_CONTROLLER']=='new-page') {
				$notifyText = $L->g('New page created');
			}
		}
	}
}

<?php

class MyHooks extends Plugin {

	public function init()
	{
        $this->customHooks = array(
            'select',
            'insert'
        );
	}

	public function select()
	{
		echo 'Custom hook select';
	}

	public function insert()
	{
		echo 'Custom hook insert';
	}
}
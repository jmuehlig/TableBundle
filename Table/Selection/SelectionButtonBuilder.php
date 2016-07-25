<?php

namespace JGM\TableBundle\Table\Selection;

/**
 * Builder for buttons, used for submitting selected 
 * rows of the table.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class SelectionButtonBuilder
{
	/**
	 * @var array
	 */
	private $buttons;
	
	public function __construct()
	{
		$this->buttons = array();
	}
	
	public function add($name, array $options = array())
	{
		$this->buttons[$name] = new Button\SubmitButton($name, $options);
		
		return $this;
	}
	
	public function getButtons()
	{
		return $this->buttons;
	}
}

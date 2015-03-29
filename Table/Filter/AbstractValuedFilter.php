<?php


namespace JGM\TableBundle\Table\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;

/**
 * The AbstractValuedFilter is an abstract filter,
 * which can hold possible values and render them
 * as select-input for example.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
abstract class AbstractValuedFilter extends AbstractFilter
{	
	/**
	 * @return All values of this valued filter.
	 */
	protected abstract function getValues();
	
	public function needsFormEnviroment()
	{
		return in_array($this->widget, array('select', 'choice'));
	}
	
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'widget' => 'list',
			'reset_label' => '',
			'reset_pos' => 0,
			'li_attr' => array(),
			'li_active_attr' => array()
		));
		
		$optionsResolver->setAllowedValues(array(
			'widget' => $this->getAvailableWidgets(),
		));
		
		$optionsResolver->setAllowedValues(array(
			'li_attr' => 'array',
			'li_active_attr' => 'array',
			'widget' => 'string',
			'reset_pos' => 'integer',
			'reset_label' => 'string'
		));
	}
	
	/**
	 * @return array	Array of all widgets, supported by this filter.
	 */
	protected function getAvailableWidgets()
	{
		return array('select', 'list', 'choice');
	}
	
	public function getWidgetBlockName()
	{
		return $this->widget . '_widget';
	}
}

<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

		$optionsResolver->setAllowedValues('widget', $this->getAvailableWidgets());
		
		$optionsResolver->setAllowedTypes('li_attr', array('array', null));
		$optionsResolver->setAllowedTypes('li_active_attr', array('array', null));
		$optionsResolver->setAllowedTypes('widget', 'string');
		$optionsResolver->setAllowedTypes('reset_pos', array('integer', null));
		$optionsResolver->setAllowedTypes('reset_label', array('string', null));
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

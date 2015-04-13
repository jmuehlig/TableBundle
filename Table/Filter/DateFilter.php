<?php

namespace JGM\TableBundle\Table\Filter;

use JGM\TableBundle\Table\TableException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filter for date columns.
 * 
 * @author	Jan Mühlig
 * @since	1.0
 */
class DateFilter extends AbstractFilter
{
	public function needsFormEnviroment()
	{
		return true;
	}
	
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'format' => 'd.m.Y',
			'widget' => 'text',
			//'years' => range( date('Y', strtotime('-5 years')), date('Y', strtotime('+5 years')) ),
			//'days' => range(1,31)
		));
		
		$optionsResolver->setAllowedValues(array(
			'widget' => array('text')
		));
	}

	public function getWidgetBlockName() 
	{
		if($this->widget === 'text')
		{
			return 'date_text_widget';
		}
		
		TableException::filterWidgetNotFound($this->widget);
	}

}

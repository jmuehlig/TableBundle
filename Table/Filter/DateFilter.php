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
			'type' => 'text'
			//'years' => range( date('Y', strtotime('-5 years')), date('Y', strtotime('+5 years')) ),
			//'days' => range(1,31)
		));
		
		$optionsResolver->setAllowedValues(array(
			'widget' => array('text'),
			'type' => array('text', 'date')
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
	
	public function setValue(array $value)
	{
		$dateAsString = $value[$this->getName()];
		if($dateAsString !== null && $dateAsString !== "")
		{
			$timestampFromString = strtotime($dateAsString);
			
			$date = new \DateTime();
			$date->setTimestamp($timestampFromString);
			$date->setTime(0, 0, 0);
			
			$this->value = $date;
		}
		else
		{
			$this->value = null;
		}
	}
}

<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\Renderer\RenderHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
			'widget' => 'choice',
			'years' => range( date('Y', strtotime('-5 years')), date('Y', strtotime('+5 years')) ),
			'days' => range(1,31)
		));
		
		$optionsResolver->setAllowedValues(array(
			'widget' => array('choice', 'text', 'single_text')
		));
	}

	public function render(ContainerInterface $container)
	{
//		if($this->widget === 'single_text')
//		{
//			return $this->renderSingleText($container);
//		}
//		else if($this->widget === 'text')
//		{
			return $this->renderText($container);
//		}
//		else
//		{
//			return $this->renderChoice($container);
//		}
	}
	
	protected function renderChoice(ContainerInterface $container)
	{
		$content = "";
		
		// 
		
		return $content;
	}
	
	protected function renderText(ContainerInterface $container)
	{
		return sprintf(
			"<input type=\"date\" name=\"%s\"%s%s />",
			$this->getName(),
			$this->defaultValue === null ? '' : sprintf(" value=\"%s\"", date($this->format, strtotime($this->defaultValue))),
			RenderHelper::attrToString($this->getAttributes())
		);
	}
	
	protected function renderSingleText(ContainerInterface $container)
	{
		
	}
}

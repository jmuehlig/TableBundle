<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\Renderer\RenderHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filter, giving some available values for selection.
 * Rendered as <select>-element.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @version	1.0
 */
class SelectionFilter extends AbstractFilter
{
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'values' => array(),
			'reset_label' => ''
		));
	}
	
	public function needsFormEnviroment()
	{
		return true;
	}

	public function render(ContainerInterface $container)
	{
		$content = sprintf("<select name=\"%s\"%s>", $this->getName(), RenderHelper::attrToString($this->getAttributes()));
		
		// Merge reset option with all other values.
		$allOptions = array_merge(
			array($this->defaultValue => $this->getResetLabel()), 
			$this->getValues()
		);
		
		// Render all options.
		foreach($allOptions as $value => $label)
		{
			$selected = $value === $this->getValue() ? " selected=\"selected\"" : "";
			$content .= sprintf("<option value=\"%s\"%s>%s</option>", $value, $selected, $label);
		}
		
		$content .= "<select>";
		
		return $content;
	}
}
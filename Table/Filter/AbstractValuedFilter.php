<?php


namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\Renderer\RenderHelper;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;

/**
 * Description of AbstractValuedFilter
 *
 * @author Jan
 */
abstract class AbstractValuedFilter extends AbstractFilter
{
	protected $RENDER_PREFIX = 'render';
	
	/**
	 * @return All values of this valued filter.
	 */
	protected abstract function getValues();
	
	protected function needsFormEnviroment()
	{
		return in_array($this->widget, array('select', 'choice'));
	}
	
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		$optionsResolver->setAllowedValues(array(
			'widget' => $this->getAvailableWidgets(),
		));
		
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'reset_label' => '',
			'reset_pos' => 0,
			'li_class' => '',
			'li_active_class' => ''
		));
	}
	
	/**
	 * A widget is supported, if a function called 'render<WidgetName>' exists
	 * and does not return false.
	 * 
	 * @return array	Array of all widgets, supported by this filter.
	 */
	protected function getAvailableWidgets()
	{
		$widgets = array();
		
		$prefix = $this->RENDER_PREFIX;
		$prefixLength = strlen($prefix);
		
		$methods = get_class_methods($this);
		foreach($methods as $method)
		{
			if(strrpos($prefix, $methods, -$prefixLength) !== false)
			{
				$widgets[] = strtolower(substr($method, $prefixLength, 1)) . substr($method, $prefixLength+1);
			}
		}
		
		return $widgets;
	}

	protected function getWidgetFuntionName($prefix)
	{
		return $prefix . strtoupper($this->widget{0}) . substr($this->widget, 1);
	}
	
	/**
	 * Renders the filter, depends on the selected widget.
	 * 
	 * @return string HTML code for this filter.
	 */
	public function render()
	{
		return call_user_func($this, $this->getWidgetFuntionName($this->RENDER_PREFIX));
	}
	
	/**
	 * Renders filer as a ul-list.
	 */
	protected function renderList()
	{
		$urlHelper = $this->containeInterface->get('pzad.url_helper');
		
		// Begin <ul>-tag.
		$content = sprintf("<ul%s>", RenderHelper::attrToString($this->getAttributes()));

		// Render values as items and reset item.
		$count = 0;
		$resetItemRendered = $this->resetLabel === null;
		foreach($this->values as $key => $label)
		{
			// Render reset label, if not done.
			if($resetItemRendered === false && $this->resetPos <= $count)
			{
				$content .= $this->renderValue($urlHelper, '', $this->resetLabel, $this->getLiClass(''));
				$resetItemRendered = true;
			}
			
			// Value item.
			$content .= $this->renderValue($urlHelper, (string) $key,  $label, $this->getLiClass((string) $key));
		}
		
		// Render reset label, if not done.
		if($resetItemRendered === false)
		{
			$content .= $this->renderValue($urlHelper, null, $this->resetLabel, $this->getLiClass(null));
		}
		
		// End <ul>-tag.
		$content .= "</ul>";
		
		return $content;
	}
	
	/**
	 * Renders filter as a select-box.
	 */
	protected function renderSelect()
	{
		$content = sprintf("<select name=\"%s\"%s>", $this->getName(), RenderHelper::attrToString($this->getAttributes()));
		
		// Merge reset option with all other values.
		$allOptions = array_merge(
			array($this->defaultValue => $this->resetLabel), 
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
	
	/**
	 * Renders filter as a radio choice.
	 */
	protected function renderChoice()
	{
		$content = "";
		
		// Merge reset option with all other values.
		$allOptions = array_merge(
			array($this->defaultValue => $this->resetLabel), 
			$this->getValues()
		);
		
		// Render all options.
		foreach($allOptions as $value => $label)
		{
			$attr = $this->getAttributes();
			if($value === $this->getValue())
			{
				$attr['checked'] = "checked";
			}
			
			$id = $this->getName() . "_" . str_replace(' ', '_', $value);
			$content .= sprintf(
				"<input id=\"%s\" type=\"radio\" value=\"%s\"%s> <label for=\"%s\">%s</label>",
				$id,
				$value,
				RenderHelper::attrToString($attr),
				$id,
				$label
			);
		}
		
		return $content;
	}
}

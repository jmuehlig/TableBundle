<?php


namespace JGM\TableBundle\Table\Filter;

use JGM\TableBundle\Table\Renderer\RenderHelper;
use JGM\TableBundle\Table\UrlHelper;
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
			if(substr($method, 0, $prefixLength) === $prefix)
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
		return call_user_func(array($this, $this->getWidgetFuntionName($this->RENDER_PREFIX)));
	}
	
	public function getWidgetBlockName()
	{
		return $this->widget . '_widget';
	}
	
	/**
	 * Renders filer as a ul-list.
	 */
	protected function renderList()
	{
		$urlHelper = $this->containeInterface->get('jgm.url_helper');
		
		// Begin <ul>-tag.
		$content = sprintf("<ul%s>", RenderHelper::attrToString($this->getAttributes()));

		// Render values as items and reset item.
		$count = 0;
		$resetItemRendered = $this->resetLabel === null;
		foreach($this->getValues() as $key => $label)
		{
			// Render reset label, if not done.
			if($resetItemRendered === false && $this->resetPos <= $count)
			{
				$content .= $this->createListNodeValue($urlHelper, '', $this->resetLabel, $this->getListItemAttributes(''));
				$resetItemRendered = true;
			}
			
			// Value item.
			$content .= $this->createListNodeValue($urlHelper, (string) $key,  $label, $this->getListItemAttributes((string) $key));
		}
		
		// Render reset label, if not done.
		if($resetItemRendered === false)
		{
			$content .= $this->createListNodeValue($urlHelper, null, $this->resetLabel, $this->getListItemAttributes(null));
		}
		
		// End <ul>-tag.
		$content .= "</ul>";
		
		return $content;
	}
	
	/**
	 * Renders a value item as a list item.
	 * 
	 * @param UrlHelper $urlHelper	UrlHelper for creating urls with filter values.
	 * @param string $value			Value to render.
	 * @param string $label			Label of the value.
	 * @param string $attr			Attributes of the list item.
	 * 
	 * @return string				HTML code of the list item.
	 */
	protected function createListNodeValue(UrlHelper $urlHelper, $value, $label, $attr)
	{
		$content = sprintf("<li%s>", RenderHelper::attrToString($attr));
		$content .= sprintf(
			"<a href=\"%s\">%s</a>",
			$urlHelper->getUrlForParameters(array(
				$this->getName() => $value
			)),
			$label
		);
		$content .= "</li>";
		
		return $content;
	}
	
	/**
	 * Returns the attributes of a list item,
	 * depending on the filters value and the
	 * class options, defined by the filter builder.
	 * 
	 * @param	string	$value	Value, the class is used for.
	 * @return	array			Attributes.
	 */
	protected function getListItemAttributes($value)
	{
		return $this->getValue() === $value ? $this->liActiveAttr : $this->liAttr;
	}
	
	/**
	 * Renders filter as a select-box.
	 */
	protected function renderSelect()
	{
		$content = sprintf(
			"<select name=\"%s\" id=\"%s\"%s>",
			$this->getName(),
			$this->getName(),
			RenderHelper::attrToString($this->getAttributes())
		);
		
		// Merge reset option with all other values.
		$allOptions = array($this->defaultValue => $this->resetLabel) + $this->getValues();
		
		// Render all options.
		foreach($allOptions as $value => $label)
		{
			$selected = (string) $value === $this->getValue() ? " selected=\"selected\"" : "";
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
		$allOptions = array($this->defaultValue => $this->resetLabel) + $this->getValues();
		
		// Render all options.
		foreach($allOptions as $value => $label)
		{
			$value = (string) $value;
			$attr = $this->getAttributes();

			if($value === $this->getValue())
			{
				$attr['checked'] = "checked";
			}
			
			$id = $this->getName() . "_" . str_replace(' ', '_', $value);
			$content .= sprintf(
				"<input id=\"%s\" name=\"%s\" type=\"radio\" value=\"%s\"%s /> <label for=\"%s\">%s</label>",
				$id,
				$this->getName(),
				$value,
				RenderHelper::attrToString($attr),
				$id,
				$label
			);
		}
		
		return $content;
	}
}

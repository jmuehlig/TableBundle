<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\Utils\UrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of ListFilter
 *
 * @author Jan Mühlig
 */
class ListFilter extends AbstractFilter
{
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		$optionsResolver->setDefaults(array(
			'values' => array(),
			'reset_label' => null,
			'reset_pos' => 0,
			'ul_class' => '',
			'li_class' => '',
			'li_active_class' => ''
		));
	}

	public function needsFormEnviroment()
	{
		return false;
	}

	public function render(ContainerInterface $container)
	{
		$urlHelper = $container->get('pzad.url_helper');
		
		// Begin <ul>-tag.
		$content = sprintf("<ul%s>", $this->getUlClass() !== '' ? ' class="' . $this->getUlClass() . '"' : '');

		// Render values as items and reset item.
		$count = 0;
		$resetItemRendered = $this->getResetLabel() === null;
		foreach($this->getValues() as $key => $label)
		{
			// Render reset label, if not done.
			if($resetItemRendered === false && $this->getResetPos() <= $count)
			{
				$content .= $this->renderValue($urlHelper, null, $this->getResetLabel(), $this->getLiClass(null));
			}
			
			// Value item.
			$content .= $this->renderValue($urlHelper, (string) $key,  $label, $this->getLiClass((string) $key));
		}
		
		// Render reset label, if not done.
		if($resetItemRendered === false)
		{
			$content .= $this->renderValue($urlHelper, null, $this->getResetLabel(), $this->getLiClass(null));
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
	 * @param string $class			Class of the list item.
	 * 
	 * @return string				HTML code of the list item.
	 */
	protected function renderValue(UrlHelper $urlHelper, $value, $label, $class)
	{
		$content = sprintf("<li%s>", $class !== '' ? ' class="' . $class . '"' : '');
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
	 * Returns the class of a list item,
	 * depending on the filters value and the
	 * class options, defined by the filter builder.
	 * 
	 * @param string $value	Value, the class is used for.
	 * @return string		Class.
	 */
	protected function getLiClass($value)
	{
		return $this->getValue() === $value ? $this->getLiActiveClass() : $this->getLiClass();
	}
}

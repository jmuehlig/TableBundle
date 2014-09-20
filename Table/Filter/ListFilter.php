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
	/**
	 * Label for filter resetter.
	 * 
	 * @var string
	 */
	protected $resetLabel;
	
	/**
	 * List with values.
	 * 
	 * @var array
	 */
	protected $values;
	
	/**
	 * List with classes.
	 * 
	 * @var array
	 */
	protected $classes;
	
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		$optionsResolver->setDefaults(array(
			'values' => array(),
			'reset_label' => null,
			'ul_class' => '',
			'li_class' => '',
			'li_active_class' => ''
		));
	}
	
	public function setOptions(array $options)
	{
		$options = parent::setOptions($options);
		
		$this->values = $options['values'];
		$this->resetLabel = $options['reset_label'];
		$this->classes = array(
			'ul' => $options['ul_class'],
			'li' => $options['li_class'],
			'li_active' => $options['li_active_class']
		);
	}

	public function needsFormEnviroment()
	{
		return false;
	}

	public function render(ContainerInterface $container)
	{
		$urlHelper = $container->get('pzad.url_helper');
		
		// Begin <ul>-tag.
		$content = sprintf("<ul%s>", $this->classes['ul'] !== '' ? ' class="' . $this->classes['ul'] . '"' : '');
		
		// Reset-Link.
		if($this->resetLabel!== null)
		{
			$this->renderValue($urlHelper, null, $this->resetLabel, $this->getLiClass(null));
		}
		
		// Other values.
		foreach($this->values as $key => $label)
		{
			$content .= $this->renderValue($urlHelper, (string) $key,  $label, $this->getLiClass((string) $key));
		}
		
		// End <ul>-tag.
		$content .= "</ul>";
		
		return $content;
	}
	
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
	}
	
	protected function getLiClass($value)
	{
		return $this->getValue() === $value ? $this->classes['li_active'] : $this->classes['li'];
	}
}

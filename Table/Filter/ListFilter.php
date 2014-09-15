<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\Utils\UrlGenerator;
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
			'ul_class' => '',
			'li_class' => '',
			'li_active_class' => ''
		));
	}
	
	public function setOptions(array $options)
	{
		$options = parent::setOptions($options);
		
		$this->values = $options['values'];
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
		$urlGenerator = new UrlGenerator($container->get('request'), $container->get('router'));
		
		$content = sprintf("<ul%s>", $this->classes['ul'] !== '' ? ' class="' . $this->classes['ul'] . '"' : '');
		
		foreach($this->values as $key => $label)
		{
			$class = $this->getValue() == $key ? $this->classes['li_active'] : $this->classes['li'];
			
			$content .= sprintf("<li%s>", $class !== '' ? ' class="' . $class . '"' : '');
			$content .= sprintf(
				"<a href=\"%s\">%s</a>",
				$urlGenerator->getUrl(array($this->getName() => $key)),
				$label
			);
			$content .= "</li>";
		}
		
		$content .= "</ul>";
		
		return $content;
	}
}

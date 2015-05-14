<?php

namespace JGM\TableBundle\Table\Column;

use JGM\TableBundle\Table\Row\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * 
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class UrlColumn extends AbstractColumn
{
	/**
	 * @var ContainerInterface 
	 */
	protected $container;
	
	protected function setDefaultOptions(OptionsResolverInterface $optionsResolver)
	{
		parent::setDefaultOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'url' => null,
			'text' => '',
			'route_name' => null,
			'route_params' => array(),
			'link_attr' => array(),
		));
	}
	
	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
	public function getContent(Row $row)
	{
		$url = '#';
		if($this->options['url'] !== null)
		{
			$url = $this->options['url'];
		}
		else if($this->options['route_name'] !== null)
		{
			$params = array();
			foreach($this->options['route_params'] as $key => $value)
			{
				$params[$key] = $this->getValue($row, $value);
			}
			$url = $this->container->get('router')->generate($this->options['route_name'], $params);
		}
		
		$attr = array();
		foreach($this->options['link_attr'] as $name => $value)
		{
			$attr[] = sprintf("%s=\"%s\"", $name, $value);
		}
		
		return sprintf("<a href=\"%s\"%s>%s</a>", $url, implode(" ", $attr), $this->options['text']);
	}
}

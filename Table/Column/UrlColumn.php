<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Column;

use JGM\TableBundle\Table\Row\Row;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Column for linking table content or static text
 * to controller routes or static urls.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class UrlColumn extends AbstractColumn implements ContainerAwareInterface
{
	/**
	 * @var ContainerInterface 
	 */
	protected $container;
	
	protected function configureOptions(OptionsResolver $optionsResolver)
	{
		parent::configureOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'url' => null,
			'text' => null,
			'route_name' => null,
			'route_params' => array(),
			'link_attr' => array(),
			'empty_value' => null
		));
	}
	
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
	
	public function getContent(Row $row)
	{
		$url = $this->getUrl($row);
		
		if($url === null)
		{
			return $this->options['empty_value'];
		}
		
		$text = $this->options['text'];
		if($text === null)
		{
			$text = $this->replacePlaceholder($this->getValue($row), $row);
		}
		
		return sprintf("<a href=\"%s\"%s>%s</a>", $url, $this->getLinkAttributesAsString(), $text);
	}
	
	/**
	 * Creates a string based on the link_attr option.
	 * Starts with a blank, if there is at least 
	 * one attribute.
	 * 
	 * @return string
	 */
	protected function getLinkAttributesAsString()
	{
		
		$attr = array();
		foreach($this->options['link_attr'] as $name => $value)
		{
			$attr[] = sprintf("%s=\"%s\"", $name, $value);
		}
		
		if(empty($attr))
		{
			return "";
		}
		
		return " " . implode(" ", $attr);
	}
	
	/**
	 * Creates an url for the cell (row, this column).
	 * Possible urls:
	 *	* given url by the option 'url'
	 *	* url by given route and route parameters
	 *	* content of this cell, interpreted as url, of not empty
	 * 
	 * @param Row $row
	 */
	protected function getUrl(Row $row)
	{
		$url = null;
		if($this->options['url'] !== null)
		{
			$url = $this->options['url'];
		}
		else if($this->options['route_name'] !== null)
		{
			$params = array();
			foreach($this->options['route_params'] as $key => $value)
			{
				$params[$key] = $this->replacePlaceholder($value, $row);
			}
			$url = $this->container->get('router')->generate($this->options['route_name'], $params);
		}
		else 
		{
			$value = $this->getValue($row);
			if($value !== null && strlen($value) > 0)
			{
				$url = $this->replacePlaceholder($value, $row);
			}
		}
		
		return $url;
	}
		
	/**
	 * Replaces all placeholders like {{id}} in a string
	 * by the rows value.
	 * 
	 * @param string $string	String, in which the placeholders will be replaced.
	 * @param Row $row			Current row.
	 * 
	 * @return string
	 */
	private function replacePlaceholder($string, Row $row)
	{
		$matchArray = array();
		if(preg_match_all('/{{\s*(.*)\s*}}/', $string, $matchArray))
		{
			$search = array();
			$replace = array();
			foreach($matchArray[1] as $key => $match)
			{
				$search[] = $matchArray[0][$key];
				$replace[] = $this->getValue($row, $match);
			}
			
			return str_replace($search, $replace, $string);
		}
		
		return $string;
	}
}

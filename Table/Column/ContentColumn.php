<?php

namespace PZAD\TableBundle\Table\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PZAD\TableBundle\Table\Row\Row;
use PZAD\TableBundle\Table\ContentGrabber\ContentGrabberInterface;
use PZAD\TableBundle\Table\TableException;

/**
 * Uses a ContentGrabber or a ContentFunction to generate
 * the output for this column.
 *
 * @author Jan Mühlig
 */
class ContentColumn extends AbstractColumn
{
	/**
	 * @var ContainerInterface 
	 */
	protected $container;
	
	public function setDefaultOptions(OptionsResolverInterface $optionsResolver)
	{
		parent::setDefaultOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'content_grabber' => null,
			'content_function' => null
		));
	}

	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function getContent(Row $row)
	{
		if($this->options['content_function'] !== null && is_callable($this->options['content_function']))
		{
			return $this->options['content_function']($row, $this);
		}
		
		if($this->options['content_grabber'] !== null && $this->options['content_grabber'] instanceof ContentGrabberInterface)
		{
			if(is_callable(array($this->options['content_grabber'], 'setContainer')))
			{
				$this->options['content_grabber']->setContainer($this->container);
			}
			
			return $this->options['content_grabber']->getContent($row, $this);
		}
		
		TableException::noContentDefined($this->getName());
	}

}

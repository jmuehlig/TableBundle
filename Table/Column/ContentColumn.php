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

use JGM\TableBundle\Table\Column\AbstractColumn;
use JGM\TableBundle\Table\Column\ContentGrabber\ContentGrabberInterface;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\TableException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Uses a ContentGrabber or a ContentFunction to generate
 * the output for this column.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class ContentColumn extends AbstractColumn implements ContainerAwareInterface
{
	/**
	 * @var ContainerInterface 
	 */
	protected $container;
	
	/**
	 * @var callable
	 */
	protected $contentCallable;


	public function configureOptions(OptionsResolver $optionsResolver)
	{
		parent::configureOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'content_grabber' => null
		));
	}
	
	public function setOptions(array $options)
	{
		parent::setOptions($options);
		
		$contentGrabber = $this->options['content_grabber'];
		
		if($contentGrabber === null)
		{
			TableException::noContentDefined($this->getName());
		}
		else if($contentGrabber instanceof ContentGrabberInterface)
		{
			$this->contentCallable = array($contentGrabber, 'getContent');
		}
		else if(is_callable($contentGrabber))
		{
			$this->contentCallable = $contentGrabber;
		}
		else
		{
			TableException::noContentDefined($this->getName());
		}
	}

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
		
		$contentGrabber = $this->options['content_grabber'];
		if($contentGrabber instanceof ContainerAwareInterface)
		{
			$contentGrabber->setContainer($container);
		}
	}

	public function getContent(Row $row)
	{
		return call_user_func_array($this->contentCallable, array($row, $this));		
	}
}

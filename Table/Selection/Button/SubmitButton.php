<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Selection\Button;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Submit button options holder for
 * the selection buttons.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class SubmitButton
{
	private $name;
	private $options;
	
	public function __construct($name, array $options = array())
	{
		$this->name = $name;
		$this->options = $this->resolveOptions($name, $options);
	}
	
	private function resolveOptions($name, array $options)
	{
		$optionsResolver = new OptionsResolver();
		$optionsResolver->setDefaults(array(
			'label' => $name,
			'attr' => array()
		));
		
		$optionsResolver->setAllowedTypes('label', 'string');
		$optionsResolver->setAllowedTypes('attr', 'array');
		
		return $optionsResolver->resolve($options);
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getLabel()
	{
		return $this->options['label'];
	}
	
	public function getAttributes()
	{
		return $this->options['attr'];
	}
}

<?php

namespace JGM\TableBundle\Table\Selection\Button;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of SubmitButton
 *
 * @author Jan
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

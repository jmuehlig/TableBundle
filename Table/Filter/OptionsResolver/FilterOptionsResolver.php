<?php

namespace JGM\TableBundle\Table\Filter\OptionsResolver;

use JGM\TableBundle\Table\Filter\Model\Filter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * OptionsResolver for filter options, used to resolve
 * options, set at the filter table type.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class FilterOptionsResolver extends OptionsResolver
{
	/**
	 * @var OptionsResolverInterface
	 */
	protected $submitButtonResolver;
	
	/**
	 * @var OptionsResolverInterface
	 */
	protected $resetLinkResolver;
	
			
	function __construct() 
	{
		parent::__construct();
		
		$this->setDefaults(array(
			'submit' => array(),
			'reset' => array()
		));
		
		$this->submitButtonResolver = new OptionsResolver();
		$this->submitButtonResolver->setDefaults(array(
			'label' => 'Ok',
			'attr' => array()
		));
		
		$this->resetLinkResolver = new OptionsResolver();
		$this->resetLinkResolver->setDefaults(array(
			'label' => 'Reset',
			'attr' => array()
		));
	}
	
	/**
	 * Creating a filter model from resolver.
	 * 
	 * @return Filter
	 */
	public function toFilter()
	{
		$filter = $this->resolve(array());
		$submit = $this->submitButtonResolver->resolve($filter['submit']);
		$reset = $this->submitButtonResolver->resolve($filter['reset']);
		
		return new Filter(
			$submit['label'], 
			$submit['attr'],
			$reset['label'],
			$reset['attr']
		);
	}
}

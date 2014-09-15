<?php

namespace PZAD\TableBundle\Table\Filter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * The AbstractFormFilter gives a extending filter the possibility
 * to indicate a form component as a filter.
 * 
 * @author 	Jan Mühlig
 * @since	1.0.0
 */
abstract class AbstractFormFilter extends AbstractFilter
{	
	/**
	 * Options for the form widget.
	 * 
	 * @var array
	 */
	protected $formOptions;
	
	protected abstract function getType();
	
	public function needsFormEnviroment()
	{
		return true;
	}

	protected function getTemplate()
	{
		return 'PZADTableBundle:Filter:abstractForm.html.twig';
	}
	
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		$optionsResolver->setDefaults(array('form' => array()));
	}
	
	public function setOptions(array $options)
	{
		$options = parent::setOptions($options);
		
		$this->formOptions = $options['form'];
	}

	public function render(ContainerInterface $container)
	{
		$formBuilder = $container->get('form.factory')->createNamedBuilder('', 'form');
		/* @var $formBuilder FormBuilder */
		
		$options = array_merge(
			$this->formOptions,
			array(
				'label' => $this->getLabel(),
				'attr' => $this->getAttributes(),
				'data' => $this->getValue()
			)
		);
		
		$formBuilder->add($this->getName(), $this->getType(), $options);
		
		return $container->get('templating')->render(
			$this->getTemplate(),
			array('filter' => $formBuilder->getForm()->get($this->getName())->createView())
		); 
	}
}
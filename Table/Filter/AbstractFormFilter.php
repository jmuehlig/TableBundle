<?php

namespace PZAD\TableBundle\Table\Filter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilder;

/**
 * The AbstractFormFilter gives a extending filter the possibility
 * to indicate a form component as a filter.
 * 
 * @author 	Jan Mühlig
 * @since	1.0.0
 */
abstract class AbstractFormFilter extends AbstractFilter
{	
	protected abstract function getType();
	
	protected function getTemplate()
	{
		return 'PZADTableBundle:Filter:abstractForm.html.twig';
	}
		
	public function render(ContainerInterface $container)
	{
		$formBuilder = $container->get('form.factory')->createBuilder('form');
		/* @var $formBuilder FormBuilder */
		
//		$options = array_merge(
//			array(
//				$this->getLabel(),
//				$this->getAttributes()
//			),
//			$this->getFurtherOptions()
//		);
//		
		$formBuilder->add($this->getName(), $this->getType(), array('label' => $this->getLabel(), 'attr' => $this->getAttributes()));
		
		return $container->get('templating')->render(
			$this->getTemplate(),
			array('filter' => $formBuilder->getForm()->get($this->getName())->createView())
		); 
	}
}
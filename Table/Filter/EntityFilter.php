<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JGM\TableBundle\Table\Filter;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of EntityFilter
 *
 * @author Jan
 */
class EntityFilter extends AbstractValuedFilter
{
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'operator' => FilterOperator::EQ,
			'widget' => 'select',
			'order_by' => array('id', 'asc'),
			'find_by' => array()
		));
		
		$optionsResolver->setRequired(array(
			'entity'
		));
		
		$optionsResolver->setAllowedTypes(array(
			'order_by' => 'array',
			'find_by' => 'array'
		));
	}

	protected function getValues()
	{
		$repository = $this->containeInterface->get('doctrine')->getRepository($this->entity);
		/* @var $repository EntityRepository */
		
		$values = array();
		//$repository->findBy($this->findBy, $this->orderBy)
		foreach($repository->findAll() as $item)
		{
			$values[$item->getId()] = $item->__toString();
		}
		
		return $values;
	}
}

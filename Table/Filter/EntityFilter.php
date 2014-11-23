<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PZAD\TableBundle\Table\Filter;

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
		$optionsResolver->setRequired(array(
			'entity'
		));
		
		$optionsResolver->setAllowedTypes(array(
			'order_by' => 'array'
		));
		
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'widget' => 'select',
			'order_by' => array('id', 'asc'),
			'find_by' => array()
		));
	}

	protected function getValues()
	{
		$repository = $this->containeInterface->get('doctrine')->getRepository($this->entity);
		/* @var $repository EntityRepository */
		
		$values = array();
		foreach($repository->findBy($this->findBy, $this->orderBy) as $item)
		{
			$values[$item->getId()] = $item->__toString();
		}
		
		return $values;
	}
}

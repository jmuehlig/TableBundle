<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Filter;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This valued filter will contain a set of
 * objects of an entity.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class EntityFilter extends AbstractValuedFilter
{
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'operator' => FilterOperator::EQ,
			'widget' => 'select',
			'order_by' => array('id' => 'asc'),
			'find_by' => array(),
			'entity' => null,
			'entities' => array()
		));
		
		$optionsResolver->setAllowedTypes(array(
			'order_by' => 'array',
			'find_by' => 'array',
		));
	}

	/**
	 * @return array	All objects of the specified entity.
	 */
	public function getValues()
	{
		if($this->entity !== null)
		{
			$repository = $this->container->get('doctrine')->getRepository($this->entity);
			/* @var $repository EntityRepository */

			$values = array();
			foreach($repository->findBy($this->findBy, $this->orderBy) as $item)
			{
				$values[$item->getId()] = (string) $item;
			}

			return $values;
		}
		else if($this->entities !== null)
		{
			return $this->entities;
		}
		
		return array();
	}
//	
//	public function getValue()
//	{
//		$id = parent::getValue();
//		
//		$repository = $this->containeInterface->get('doctrine')->getRepository($this->entity);
//		/* @var $repository EntityRepository */
//		
//		return $repository->findOneBy(array('id' => $id));
//	}
}

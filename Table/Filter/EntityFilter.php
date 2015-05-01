<?php

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

	/**
	 * @return array	All objects of the specified entity.
	 */
	public function getValues()
	{
		$repository = $this->containeInterface->get('doctrine')->getRepository($this->entity);
		/* @var $repository EntityRepository */

		$values = array();
		//$repository->findBy($this->findBy, $this->orderBy)
		foreach($repository->findAll() as $item)
		{
			$values[$item->getId()] = (string) $item;
		}
		
		return $values;
	}
}

<?php

namespace PZAD\TableBundle\Table;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Abstract table type.
 */
abstract class AbstractTableType
{
	/**
	 * Container
	 * 
	 * @var ContainerInterface 
	 */
	private $_container;
	
	/**
	 * EntityManager.
	 * 
	 * @var EntityManager 
	 */
	private $_entityManager;
	
	public final function getContainer()
	{
		return $this->_container;
	}

	public final function getEntityManager()
	{
		return $this->_entityManager;
	}

	public final function setContainer(ContainerInterface $container)
	{
		$this->_container = $container;
	}

	public final function setEntityManager(EntityManager $entityManager)
	{
		$this->_entityManager = $entityManager;
	}
	
	/**
	 * Generates the attributes for a row at
	 * the given index.
	 * 
	 * @param Row\Row $row Row.
	 * @return array Array with attributes (e.g. class)
	 */
	public function getRowAttributes(Row\Row $row)
	{
		return array();
	}
	
	/**
	 * Builds the query for this table type.
	 * 
	 * @param QueryBuilder $queryBuilder
	 * @param array $columns
	 * @param string $entity
	 * 
	 * @return QueryBuilder Builder with builded query.
	 */
	public function buildQuery(QueryBuilder $queryBuilder, array $columns, $entity)
	{
		$queryBuilder
			->select('t')
			->from($entity, 't');
		
		foreach($columns as $column)
		{
			/* @var $column Column\ColumnInterface */
			if($column instanceof Column\EntityColumn)
			{
				$queryBuilder->leftJoin(sprintf('t.%s', $column->getName()), strtolower($column->getName()));
			}
		}
		
		return $queryBuilder;
	}
	
	/**
	 * Here you can refine your builded query, e.g. with where clauses.
	 * 
	 * @param QueryBuilder $queryBuilder	The QueryBuilder, build by
	 *										the method `buildQuery`.
	 * 
	 * @return QueryBuilder					Refined QueryBuilder.
	 */
	public function refineQuery(QueryBuilder $queryBuilder)
	{
		return $queryBuilder;
	}
	
	public abstract function buildTable(TableBuilder $builder);
	
	/**
	 * @return string Name of the table type.
	 */
	public abstract function getName();
	
	/**
	 * Sets the default options for the table type.
	 * 
	 * @param OptionsResolverInterface $resolver
	 */
	public abstract function setDefaultOptions(OptionsResolverInterface $resolver);
}

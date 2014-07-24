<?php

namespace PZAD\TableBundle\Table\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use PZAD\TableBundle\Table\Row\Row;
use PZAD\TableBundle\Table\Column\EntityColumn;
use PZAD\TableBundle\Table\TableBuilder;
use PZAD\TableBundle\Table\Column\ColumnInterface;

/**
 * The abstract table type which user defined table types based on.
 * User defined table types have to implement the abstract methods
 * `buildTable`, `getName` and `setDefaultOptions`.
 * Further they can implement the methos `buildQuery`, `refineQuery` 
 * and `getRowAttributes`.
 * 
 * The table type injects the container and the entity manager.
 * 
 * @author Jan Mühlig <mail@janmuehlig.de>
 * @since 1.0.0
 */
abstract class AbstractTableType
{
	/**
	 * Container
	 * 
	 * @var ContainerInterface 
	 */
	protected $container;
	
	/**
	 * EntityManager.
	 * 
	 * @var EntityManager 
	 */
	protected $entityManager;
	
	public final function getContainer()
	{
		return $this->container;
	}

	public final function getEntityManager()
	{
		return $this->entityManager;
	}

	public final function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public final function setEntityManager(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	/**
	 * Generates the attributes for a row at
	 * the given index.
	 * 
	 * @param Row $row Row.
	 * @return array Array with attributes (e.g. class)
	 */
	public function getRowAttributes(Row $row)
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
			/* @var $column ColumnInterface */
			if($column instanceof EntityColumn)
			{
				$queryBuilder->leftJoin(sprintf('t.%s', $column->getName()), strtolower($column->getName()));
			}
		}
	}
	
	/**
	 * Here you can refine your builded query, e.g. with where clauses.
	 * 
	 * @param QueryBuilder $queryBuilder	The QueryBuilder, build by
	 *										the method `buildQuery`.
	 * 
	 * @return QueryBuilder					Refined QueryBuilder.
	 */
	public function refineQuery(QueryBuilder $queryBuilder) { ; }
	
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

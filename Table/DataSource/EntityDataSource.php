<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PZAD\TableBundle\Table\DataSource;

use Doctrine\ORM\QueryBuilder;
use PZAD\TableBundle\Table\Column\ColumnInterface;
use PZAD\TableBundle\Table\Column\EntityColumn;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of EntityDataSource
 *
 * @author Jan Mühlig
 */
class EntityDataSource extends QueryBuilderDataSource
{
	/**
	 * @var string
	 */
	protected $entity;
	
	/**
	 * @var callable
	 */
	protected $callback;
	
	public function __construct($entity, $callback = null)
	{
		parent::__construct(null);
		
		$this->entity = $entity;
		$this->callback = $callback;
	}
	
	public function getData(ContainerInterface $container, array $columns, array $filters = null, \PZAD\TableBundle\Table\Model\PaginationOptionsContainer $pagination = null, \PZAD\TableBundle\Table\Model\SortableOptionsContainer $sortable = null)
	{
		if($this->queryBuilder === null)
		{
			$this->queryBuilder = $this->createQueryBuilder($container, $columns);
		}
		
		return parent::getData($container, $columns, $filters, $pagination, $sortable);
	}
	
	public function getCountPages(ContainerInterface $container, array $columns, array $filters = null, \PZAD\TableBundle\Table\Model\PaginationOptionsContainer $pagination = null)
	{
		if($this->queryBuilder === null)
		{
			$this->queryBuilder = $this->createQueryBuilder($container, $columns);
		}
		
		return parent::getCountPages($container, $columns, $filters, $pagination);
	}
	
	/**
	 * Creates a simple query builder with joins over all entity columns.
	 * 
	 * @param	ContainerInterface $container	Symfony container.
	 * @param	array $columns					Array with all columns.
	 * 
	 * @return	QueryBuilder					DQL query.
	 */
	protected function createQueryBuilder(ContainerInterface $container, array $columns)
	{
		$queryBuilder = $container->get('doctrine')->getManager()->createQueryBuilder();
		/* @var $queryBuilder QueryBuilder */
		
		$queryBuilder->select('t')->from($this->entity, 't');
		
		foreach($columns as $column)
		{
			/* @var $column ColumnInterface */
			
			if($column instanceof EntityColumn)
			{
				$queryBuilder->leftJoin('t.' . $column->getName(), strtolower($column->getName()));
			}
		}
		
		if($this->callback !== null)
		{
			call_user_func($this->callback, $queryBuilder);
		}
		
		return $queryBuilder;
	}
}

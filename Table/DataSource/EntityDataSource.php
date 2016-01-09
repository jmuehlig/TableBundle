<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\DataSource;

use Doctrine\ORM\QueryBuilder;
use JGM\TableBundle\Table\Column\ColumnInterface;
use JGM\TableBundle\Table\Column\EntityColumn;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Data source for filling the table with objects
 * of an entity.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
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

	/**
	 * @var string
	 */
	protected $alias;

	/**
	 * @var array
	 */
	protected $columnNameMap;
	
	public function __construct($entity, $aliasOrCallback = 't', $callbackOrAlias = null)
	{
		// TODO: Remove deprecated constructor with entity and callback, without alias.
		parent::__construct(null);
		
		$this->entity = $entity;

		if(is_callable($aliasOrCallback))
		{
			 @trigger_error(
				'The signatur ($entity,$callback) of the EntityDataSource Constructor is deprecated sind v1.2 and will be removed in 1.4. Use ($entity,$alias,$callback).',
				E_USER_DEPRECATED
			);
			
			$this->alias = 't';
			$this->callback = $aliasOrCallback;
		}
		else
		{
			$this->alias = $aliasOrCallback;
			$this->callback = $callbackOrAlias;
		}
		
		$this->columnNameMap = [];
	}
	
	public function getData(ContainerInterface $container, array $columns, array $filters = null,
							Pagination $pagination = null, Order $sortable = null)
	{
		if($this->queryBuilder === null)
		{
			$this->queryBuilder = $this->createQueryBuilder($container, $columns);
		}
		
		return parent::getData($container, $columns, $filters, $pagination, $sortable);
	}
	
	public function getCountItems(ContainerInterface $container, array $columns, array $filters = null)
	{
		if($this->queryBuilder === null)
		{
			$this->queryBuilder = $this->createQueryBuilder($container, $columns);
		}
		
		return parent::getCountItems($container, $columns, $filters);
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
		
		$queryBuilder->select($this->alias)->from($this->entity, $this->alias);
		
		foreach($columns as $column)
		{
			/* @var $column ColumnInterface */
			
			$this->processJoinColumn($column->getName(), $queryBuilder, $column instanceof EntityColumn);
		}
		
		if($this->callback !== null)
		{
			call_user_func($this->callback, $queryBuilder);
		}
		
		return $queryBuilder;
	}
}

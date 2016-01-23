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

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JGM\TableBundle\Table\Filter\EntityFilter;
use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Filter\FilterOperator;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use JGM\TableBundle\Table\TableException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DataSource implementation for fetching the data
 * from a database by executing a query builder.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class QueryBuilderDataSource implements DataSourceInterface
{
	/**
	 * @var string
	 */
	protected $entity;
	
	/**
	 * @var QueryBuilder
	 */
	protected $queryBuilder;
	
	/**
	 * @var array
	 */
	protected $joinTable;

	/**
	 * @var array
	 */
	protected $operatorMap;

	public function __construct(QueryBuilder $queryBuilder = null)
	{
		$this->queryBuilder = $queryBuilder;
		$this->joinTable = [];
		
		$this->operatorMap = array(
			FilterOperator::EQ => '=',
			FilterOperator::NOT_EQ => '!=',
			FilterOperator::GT => '>',
			FilterOperator::GEQ => '>=',
			FilterOperator::LT => '<',
			FilterOperator::LEQ => '<=',
			FilterOperator::NOT_LIKE => 'not like',
			FilterOperator::LIKE => 'like'
		);
	}
	
	public function getType()
	{
		return 'doctrine';
	}
	
	public function getData(ContainerInterface $container, array $columns, array $filters = null, Pagination $pagination = null, Order $sortable = null)
	{
		if($this->queryBuilder === null)
		{
			TableException::noQueryBuilder($container->get('jgm.table_context')->getCurrentTableName());
		}
		
		$queryBuilder = clone $this->queryBuilder;
		
		$this->applyFilters($queryBuilder, $filters);
		
		
		if($sortable !== null)
		{
			$rootAliases = $queryBuilder->getRootAliases();
			$rootAlias = $rootAliases[0];
			$column = $this->getQueryColoumnName($sortable->getCurrentColumnName(), $rootAlias);

			$queryBuilder->orderBy($column, $sortable->getCurrentDirection());
		}
		
		if($pagination !== null)
		{			
			$queryBuilder->setFirstResult($pagination->getCurrentPage() * $pagination->getItemsPerRow());
			$queryBuilder->setMaxResults($pagination->getItemsPerRow());
			
			return new Paginator($queryBuilder->getQuery(), false);
		}
		
		return $queryBuilder->getQuery()->getResult();
	}
	
	public function getCountItems(ContainerInterface $container, array $columns, array $filters = null)
	{
		if($this->queryBuilder === null)
		{
			TableException::noQueryBuilder($container->get('jgm.table_context')->getCurrentTableName());
		}
		
		$queryBuilder = clone $this->queryBuilder;
		
		$aliases = $queryBuilder->getRootAliases();
		
		$queryBuilder->select(sprintf('count(%s) a', $aliases[0]));
		
		$this->applyFilters($queryBuilder, $filters);
		
		try
		{
			$result = $queryBuilder->getQuery()->getSingleScalarResult();
		} 
		catch (NoResultException $ex) 
		{
			$result = 0;
		}
		
		return $result;
	}
	
	/**
	 * Applys the filters to the query builder and sets required parameters.
	 * 
	 * @param QueryBuilder $queryBuilder	The query builder.
	 * @param array $filters				Array with filters.
	 */
	protected function applyFilters(QueryBuilder $queryBuilder, array $filters = array())
	{
		if(count($filters) < 1)
		{
			return;
		}

		$this->joinTable = $this->getJoinedColumns($queryBuilder);
		$whereParts = array();
		$haveParts = array();
		
		$rootAliases = $queryBuilder->getRootAliases();
		$rootAlias = $rootAliases[0];
		
		foreach($filters as $filter)
		{
			/* @var $filter FilterInterface */

			// Only apply used filters to the query builder.
			if($filter->isActive() !== true)
			{
				continue;
			}
			
			// Build part for filter with all columns like: 'column1 = x or column2 = x ..'
			$innerWhereParts = array();
			$innerHaveParts = array();
			foreach($filter->getColumns() as $column)
			{
				// Add joins and the alias for the column.
				$this->processJoinColumn($column, $queryBuilder, $filter instanceof EntityFilter);
				
				// Get the query column name: t.a if its property a, for example.
				$columnName = $this->getQueryColoumnName($column, $rootAlias);
				
				// Get the column expression.
				$columnExpression = str_replace($column, $columnName, $filter->getExpressionForColumn($this, $column));
				
				// Make the like column sensitive.
				if($filter->getOperator() === FilterOperator::LIKE || $filter->getOperator() === FilterOperator::NOT_LIKE)
				{
					$columnExpression = sprintf("lower(%s)", $columnExpression);
				}
				
				// Create the where or having term.
				$term = sprintf("%s %s :%s", $columnExpression, $this->operatorMap[$filter->getOperator()], $filter->getName());
				
				if($this->isAggregate($columnExpression))
				{
					$innerHaveParts[] = $term;
				}
				else
				{
					$innerWhereParts[] = $term;
				}
			}
			
			if(count($innerWhereParts) > 0)
			{
				$whereParts[] = sprintf('(%s)', implode(' or ', $innerWhereParts));
				
				// Add the filters value to the query builder parameters map.
				if($filter->getOperator() === FilterOperator::LIKE || $filter->getOperator() === FilterOperator::NOT_LIKE)
				{
					$queryBuilder->setParameter($filter->getName(), '%' . strtolower($filter->getValue()) . '%');
				}
				else
				{
					$queryBuilder->setParameter($filter->getName(), $filter->getValue());
				}
			}
			
			if(count($innerHaveParts) > 0)
			{
				$haveParts[] = sprintf('(%s)', implode(' or ', $innerHaveParts));
				$queryBuilder->setParameter($filter->getName(), $filter->getValue());
			}
		}

		// If there was more than one filter used, add them all to the query builder.
		if(count($whereParts) > 0)
		{
			$whereStatement = implode(' and ', $whereParts);

			if(strpos(strtolower($queryBuilder->getDQL()), 'where') === false)
			{
				$queryBuilder->where($whereStatement);
			}
			else
			{
				$queryBuilder->andWhere($whereStatement);
			}
		}
		
		if(count($haveParts) > 0)
		{
			$haveStatement = implode(' and ', $haveParts);

			if(strpos(strtolower($queryBuilder->getDQL()), 'having') === false)
			{
				$queryBuilder->having($haveStatement);
			}
			else
			{
				$queryBuilder->andHaving($haveStatement);
			}
		}
	}
	
	/**
	 * Adds a join statement to the queryBuilder, if the column needs a join.
	 * 
	 * @param string $columnName			Name of the coloumn.
	 * @param QueryBuilder $queryBuilder	Query builder.
	 * @param boolean $isForSelect			Should we add a select to the query?
	 * 
	 * @return string						New name of the column.
	 */
	protected function processJoinColumn($columnName, QueryBuilder $queryBuilder, $joinLastItem, $isForSelect = false)
	{
		$rootAliases = $queryBuilder->getRootAliases();
		$rootAlias = $rootAliases[0];

		if(substr($columnName, 0 , strlen($rootAlias)) !== $rootAlias)
		{
			$columnName = sprintf("%s.%s", $rootAlias, $columnName);
			// If we have construct like "t.prop_a.prop_b.prob_c[.prob_d]*"
			if(substr_count($columnName, '.') > 1)
			{
				$parts = explode('.', $columnName);
				$max = $joinLastItem ? count($parts)-1 : count($parts)-2;
				for($i = 0; $i < $max; $i++)
				{
					$current = $parts[$i];
					$next = $parts[$i+1];
					
					if(!in_array($next, $this->joinTable))
					{
						$queryBuilder->leftJoin(sprintf("%s.%s", $current, $next), $next);
						$this->joinTable[] = $next;
					}
					
					if($isForSelect)
					{
						$queryBuilder->addSelect($next);
					}
				}
			}
		}
	}
	
	protected function getQueryColoumnName($columnName, $rootAlias)
	{
		if(strpos($columnName, ".") === false)
		{
			return sprintf("%s.%s", $rootAlias, $columnName);
		}
		else
		{
			$parts = explode(".", $columnName);
			return sprintf("%s.%s", $parts[count($parts)-2], $parts[count($parts)-1]);
		}
	}
	
	/**
	 * Returns true, if the given expression is
	 * an aggregate function and needs to be
	 * applied after group by.
	 * 
	 * @param string $expression	Expression like column name or count(columnName).
	 * @return boolean
	 */
	protected function isAggregate($expression)
	{
		$aggregations = array('count(', 'sum(', 'avg(', 'min(', 'max(', 'count (', 'sum (', 'avg (', 'min (', 'max (');
		$expression = strtolower($expression);
		foreach($aggregations as $aggregation)
		{
			if(strpos($expression, $aggregation) !== false)
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Registeres all joins of the query builder,
	 * for skipping these joins on join filter columns.
	 * 
	 * @param	QueryBuilder $queryBuilder	QueryBuilder for data request.
	 * @return	array
	 */
	protected function getJoinedColumns(QueryBuilder $queryBuilder)
	{
		$joins = array();
		foreach($queryBuilder->getDQLPart('join') as $joinParts)
		{
			foreach($joinParts as $join)
			{
				/* @var $join Join */
				$nameParts = explode('.', $join->getJoin());
				$joins[] = $nameParts[count($nameParts)-1];
			}
		}
		
		return $joins;
	}
}

<?php

namespace JGM\TableBundle\Table\DataSource;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Filter\FilterOperator;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use JGM\TableBundle\Table\TableException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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

	public function __construct(QueryBuilder $queryBuilder = null)
	{
		$this->queryBuilder = $queryBuilder;
		$this->joinTable = [];
	}
	
	public function getData(ContainerInterface $container, array $columns, array $filters = null, Pagination $pagination = null, Order $sortable = null)
	{
		if($this->queryBuilder === null)
		{
			TableException::noQueryBuilder();
		}
		
		$queryBuilder = clone $this->queryBuilder;
		
		$this->applyFilters($container->get('request'), $queryBuilder, $filters);
		
		
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
			TableException::noQueryBuilder();
		}
		
		$queryBuilder = clone $this->queryBuilder;
		
		$aliases = $queryBuilder->getRootAliases();
		
		$queryBuilder->select(sprintf('count(%s)', $aliases[0]));
		
		$this->applyFilters($container->get('request'), $queryBuilder, $filters);
				
		return $queryBuilder->getQuery()->getSingleScalarResult();
	}
	
	/**
	 * Applys the filters to the query builder and sets required parameters.
	 * 
	 * @param Request $request				The http request.
	 * @param QueryBuilder $queryBuilder	The query builder.
	 * @param array $filters				Array with filters.
	 */
	protected function applyFilters(Request $request, QueryBuilder $queryBuilder, array $filters = array())
	{
		if(count($filters) < 1)
		{
			return;
		}

		$whereParts = array();
		
		$rootAliases = $queryBuilder->getRootAliases();
		$rootAlias = $rootAliases[0];
		
		foreach($filters as $filter)
		{
			/* @var $filter FilterInterface */

			// Only apply used filters to the query builder.
			if($filter->getValue() === "" || $filter->getValue() === null)
			{
				continue;
			}
			
			// Build part for filter with all columns like: 'column1 = x or column2 = x ..'
			$innerWhereParts = array();
			foreach($filter->getColumns() as $column)
			{			
				// Add joins and the alias for the column.
				$this->processJoinColumn($column, $queryBuilder);
				
				// Get the query column name: t.a if its property a, for example.
				$column = $this->getQueryColoumnName($column, $rootAlias);
				
				if(substr_count($column, '.') > 1)
				{
					$parts = array_merge($queryBuilder->getRootAliases(), explode('.', $column));
					for($i = 0; $i < count($parts)-1; $i++)
					{
						$current = $parts[$i];
						$next = $parts[$i+1];
						$queryBuilder->leftJoin(sprintf("%s.%s", $current, $next), $next);
					}
					
					$column = sprintf("%s.%s", $current, $next);
				}
				
				$innerWhereParts[] = sprintf($this->createWherePart($filter->getOperator()), $column, $filter->getName());
			}
			
			if(count($innerWhereParts) > 0)
			{
				$whereParts[] = sprintf('(%s)', implode(' or ', $innerWhereParts));
				
				$value = $filter->getValue();
				if($filter instanceof \JGM\TableBundle\Table\Filter\DateFilter)
				{
					$value = \DateTime::createFromFormat($filter->format, $value);
					/* @var $value \DateTime */
					$value->setTime(0, 0, 0);
				}
				
				// Add the filters value to the query builder parameters map.
				if($filter->getOperator() === FilterOperator::LIKE || $filter->getOperator() === FilterOperator::NOT_LIKE)
				{
					$queryBuilder->setParameter($filter->getName(), '%' . $value . '%');
				}
				else
				{
					$queryBuilder->setParameter($filter->getName(), $value);
				}
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
	}
	
	/**
	 * Creates a where part with placeholders, like '${column} <= ${parameter}' for operator 'LT'.
	 * 
	 * @param int $filterOperator	Operator of the filter.
	 * 
	 * @return string				Where part.
	 */
	protected function createWherePart($filterOperator)
	{
		if($filterOperator === FilterOperator::EQ)
		{
			return "%s = :%s";
		}
		else if($filterOperator === FilterOperator::NOT_EQ)
		{
			return "%s != :%s";
		}
		else if($filterOperator === FilterOperator::GT)
		{
			return "%s > :%s";
		}
		else if($filterOperator === FilterOperator::GEQ)
		{
			return "%s >= :%s";
		}
		else if($filterOperator === FilterOperator::LT)
		{
			return "%s < :%s";
		}
		else if($filterOperator === FilterOperator::LEQ)
		{
			return "%s <= :%s";
		}
		else if($filterOperator === FilterOperator::NOT_LIKE)
		{
			return "%s not like :%s";
		}
		else
		{
			return "%s like :%s";
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
	protected function processJoinColumn($columnName, QueryBuilder $queryBuilder, $isForSelect = false)
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
				for($i = 0; $i < count($parts)-1; $i++)
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
}

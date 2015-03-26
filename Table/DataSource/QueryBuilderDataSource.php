<?php

namespace JGM\TableBundle\Table\DataSource;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JGM\TableBundle\Table\Column\ColumnInterface;
use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Filter\FilterOperator;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use JGM\TableBundle\Table\Model\SortableOptionsContainer;
use JGM\TableBundle\Table\TableException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * DataSource implementation for fetching the data
 * from a database by executing a query builder.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0.0
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

	public function __construct(QueryBuilder $queryBuilder = null)
	{
		$this->queryBuilder = $queryBuilder;
	}
	
	public function getData(ContainerInterface $container, array $columns, array $filters = null, Pagination $pagination = null, SortableOptionsContainer $sortable = null)
	{
		if($this->queryBuilder === null)
		{
			TableException::noQueryBuilder();
		}
		
		$queryBuilder = clone $this->queryBuilder;
		
		$this->applyFilters($container->get('request'), $queryBuilder, $filters);
		
		$aliases = $queryBuilder->getRootAliases();
		
		if($sortable !== null)
		{
			$queryBuilder->orderBy(sprintf('%s.%s', $aliases[0], $sortable->getColumnName()), $sortable->getDirection());
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
				/* @var $column ColumnInterface */
				
				// Add the table alias, if not used.
				if(strpos($column, '.') === false)
				{
					$aliases = $queryBuilder->getRootAliases();
					$column = sprintf("%s.%s", $aliases[0], $column);
				}
				
				$innerWhereParts[] = sprintf($this->createWherePart($filter->getOperator()), $column, $filter->getName());
			}
			
			if(count($innerWhereParts) > 0)
			{
				$whereParts[] = sprintf('(%s)', implode(' or ', $innerWhereParts));
				
				// Add the filters value to the query builder parameters map.
				if($filter->getOperator() === FilterOperator::LIKE || $filter->getOperator() === FilterOperator::NOT_LIKE)
				{
					$queryBuilder->setParameter($filter->getName(), '%' . $filter->getValue() . '%');
				}
				else
				{
					$queryBuilder->setParameter($filter->getName(), $filter->getValue());
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
}

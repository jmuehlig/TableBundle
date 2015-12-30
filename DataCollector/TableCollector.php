<?php

namespace JGM\TableBundle\DataCollector;

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Exception;
use JGM\TableBundle\DependencyInjection\Service\TableContext;
use JGM\TableBundle\DependencyInjection\Service\TableStopwatchService;
use JGM\TableBundle\Table\Column\ColumnInterface;
use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Table;
use JGM\TableBundle\Table\TableException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Collector for collecting information of the table bundle
 * and the builded tables.
 * Information will be displayed at the debug toolbar and web profiler.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.2
 */
class TableCollector extends DataCollector
{
	/**
	 * @var TableContext
	 */
	private $tableContext;
	
	/**
	 * @var TableStopwatchService
	 */
	private $stopwatchService;
	
	public function __construct(TableContext $tableContext, TableStopwatchService $stopwatchService)
	{
		$this->tableContext = $tableContext;
		$this->stopwatchService = $stopwatchService;
		$this->data = array();
	}
	
	public function collect(Request $request, Response $response, Exception $exception = null)
	{
		
		$this->data['count'] = count($this->tableContext->getAllRegisteredTables());
		$this->data['duration'] = $this->stopwatchService->getSumDuration();
		$this->data['stopwatches'] = $this->stopwatchService->getStopwatchesData();
		
		if($exception instanceof TableException)
		{
			$this->data['exception'] = $exception;
		}
		else
		{
			$this->data['exception'] = null;
		}
		
		$tables = array();
		foreach($this->tableContext->getAllRegisteredTables() as $tableName => $table)
		{
			/* @var $table Table */
			
			$view = array();
			
			// Collect Table options.
			$view['table_options'] = array(
				'attributes' => $this->formatAttributes($table->getTableView()->getAttributes()),
				'head_attributes' => $this->formatAttributes($table->getTableView()->getHeadAttributes()),
				'empty_value' => $table->getTableView()->getEmptyValue(),
				'template_name' => $table->getTableView()->getTemplateName()
			);
			
			if($table->getTableView()->getPagination() !== null)
			{
				$pagination = $table->getTableView()->getPagination();
				$classes = $pagination->getClasses();
				$view['pagination_options'] = array(
					'css_classes' => $this->formatAttributes(array(
						'ul' => $classes['ul'],
						'li:default' => implode(' ', $classes['li']['default']),
						'li:active' => implode(' ', $classes['li']['active']),
						'li:disabled' => implode(' ', $classes['li']['disabled']),
					)),
					'current_page' => $pagination->getCurrentPage(),
					'items_per_row' => $pagination->getItemsPerRow(),
					'max_pages' => $pagination->getMaxPages(),
					'next_label' => $pagination->getNextLabel(),
					'prev_label' => $pagination->getPreviousLabel(),
					'parameter_name' => $pagination->getParameterName(),
					'show_empty' => $pagination->getShowEmpty()
				);
			}
			
			if($table->getTableView()->getOrder() !== null)
			{
				$order = $table->getTableView()->getOrder();
				$classes = $order->getClasses();
				$view['order_options'] = array(
					'param_column' => $order->getParamColumnName(),
					'empty_column' => $order->getEmptyColumnName(),
					'current_column' => $order->getCurrentColumnName(),
					'param_direction' => $order->getParamDirectionName(),
					'empty_direction' => $order->getEmptyDirection(),
					'current_direction' => $order->getCurrentDirection(),
					'css_classes' => $this->formatAttributes(array(
						'asc' => $classes[Order::DIRECTION_ASC],
						'desc' => $classes[Order::DIRECTION_DESC]
					))
				);
			}
			
			if($table->getTableView()->getFilter() !== null)
			{
				$filter = $table->getTableView()->getFilter();
				/* @var $filter \JGM\TableBundle\Table\Filter\Model\Filter */
				$view['filter_options'] = array(
					'submit_label' => $filter->getSubmitLabel(),
					'submit_attributes' => $this->formatAttributes($filter->getSubmitAttributes()),
					'reset_label' => $filter->getResetLabel(),
					'reset_attributes' => $this->formatAttributes($filter->getResetAttributes())
				);
			}
			
			// Collect columns.
			$columns = array();
			foreach($table->getTableView()->getColumns() as $column)
			{
				/* @var $column ColumnInterface */
				
				$columnData = array();
				$columnData['name'] = $column->getName();
				$columnData['label'] = $column->getLabel();
				$columnData['attributes'] = $this->formatAttributes($column->getAttributes());
				$columnData['head_attributes'] = $this->formatAttributes($column->getHeadAttributes());
				$columnData['sortable'] = $column->isSortable();
				$columnData['class'] = get_class($column);
						
				$columns[] = $columnData;
			}
			$view['columns'] = $columns;
			
			// Collect filters.
			$filters = array();
			foreach($table->getTableView()->getFilters() as $filter)
			{
				/* @var $filter FilterInterface */
				
				$filterData = array();
				$filterData['name'] = $filter->getName();
				$filterData['label'] = $filter->getLabel();
				$filterData['attributes'] = $this->formatAttributes($filter->getAttributes());
				$filterData['columns'] = $filter->getColumns();
				$filterData['active'] = $filter->isActive();
				$filterData['value'] = $filter->getValue(FilterInterface::FOR_FILTERING);
				$filterData['class'] = get_class($filter);
						
				$filters[] = $filterData;
			}
			$view['filters'] = $filters;
			
			$tables[$tableName] = $view;
		}
		
		$this->data['tables'] = $tables;
	}
	
	public function getCount()
	{
		return $this->data['count'];
	}
	
	public function getDuration()
	{
		return $this->data['duration'];
	}
	
	public function getStopwatches()
	{
		return $this->data['stopwatches'];
	}
	
	public function getTables()
	{
		return $this->data['tables'];
	}
	
	public function getException()
	{
		return $this->data['exception'];
	}

	public function getName()
	{
		return 'jgm.table_collector';
	}
	
	protected function formatAttributes(array $attributes)
	{
		$formatedAttributes = array();
		foreach($attributes as $key => $value)
		{
			$formatedAttributes[] = sprintf("%s='%s'", $key, $value);
		}
		
		return $formatedAttributes;
	}
}

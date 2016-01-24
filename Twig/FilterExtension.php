<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Twig;

use JGM\TableBundle\DependencyInjection\Service\TableStopwatchService;
use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\TableException;
use JGM\TableBundle\Table\TableView;
use JGM\TableBundle\Table\Utils\UrlHelper;
use Twig_Environment;
use Twig_SimpleFunction;

/**
 * Twig extension for render the table filter views.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class FilterExtension extends AbstractTwigExtension
{
	/**
	 * Not every filter_* method, called from 
	 * twig tempalte, needs the view. There are
	 * some methods like filter_label, which accepts
	 * the filter interface only. For these methods,
	 * we'll save the tableView, which were passed on
	 * beginning (filter_begin).
	 * 
	 * @var TableView 
	 */
	protected $tableView;
	
	/**
	 * Array for mapping tables and their
	 * need for form environments.
	 * The key of the map is the table name,
	 * the value is a boolean.
	 * 
	 * @var array
	 */
	protected $filterNeedsFormEnvironment;
	
	public function __construct(UrlHelper $urlHelper, TableStopwatchService $stopwatchService)
	{
		parent::__construct($urlHelper, $stopwatchService);
		
		$this->filterNeedsFormEnvironment = array();
	}
	
	public function getName()
	{
		return 'filter';
	}

	public function getFunctions()
	{
		return array(
			new Twig_SimpleFunction ('filter', array($this, 'getFilterContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_label', array($this, 'getFilterLabelContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_widget', array($this, 'getFilterWidgetContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_row', array($this, 'getFilterRowContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_rows', array($this, 'getFilterRowsContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_begin', array($this, 'getFilterBeginContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_submit_button', array($this, 'getFilterSubmitButtonContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_reset_link', array($this, 'getFilterResetLinkContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_end', array($this, 'getFilterEndContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_url', array($this, 'getFilterUrl'))
		);
	}
	public function getFilterContent(Twig_Environment $environment, TableView $tableView)
	{
		$template = $this->loadTemplate($environment, $tableView->getFilter()->getTemplate());
		
		return $template->renderBlock('filter', array(
				'view' => $tableView
		));
	}
	
	public function getFilterBeginContent(Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$this->tableView = $tableView;
		
		$template = $this->loadTemplate($environment, $tableView->getFilter()->getTemplate());
		
		$content = $template->renderBlock('filter_begin', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView),
			'tableName' => $tableView->getName()
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
	}
	
	public function getFilterWidgetContent(\Twig_Environment $environment, FilterInterface $filter)
	{
		if($this->tableView === null)
		{
			TableException::filterRenderingNotStarted($filter->getName());
		}
		
		$this->stopwatchService->start($this->tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$template = $this->loadTemplate($environment, $this->tableView->getFilter()->getTemplate());
		$content = $template->renderBlock('filter_widget', array(
			'filter' => $filter
		));
		
		$this->stopwatchService->stop($this->tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
	}
	
	public function getFilterLabelContent(\Twig_Environment $environment, FilterInterface $filter)
	{
		if($this->tableView === null)
		{
			TableException::filterRenderingNotStarted($filter->getName());
		}
		
		$this->stopwatchService->start($this->tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$template = $this->loadTemplate($environment, $this->tableView->getFilter()->getTemplate());
		$content = $template->renderBlock('filter_label', array(
			'filter' => $filter
		));
		
		$this->stopwatchService->stop($this->tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
	}
	
	public function getFilterRowContent(\Twig_Environment $environment, FilterInterface $filter)
	{
		if($this->tableView === null)
		{
			TableException::filterRenderingNotStarted();
		}
		
		$template = $this->loadTemplate($environment, $this->tableView->getFilter()->getTemplate());
		
		return $template->renderBlock('filter_row', array(
			'filter' => $filter
		));
	}
	
	public function getFilterRowsContent(\Twig_Environment $environment, $tableViewOrFilterArray)
	{
		if($this->tableView === null)
		{
			TableException::filterRenderingNotStarted();
		}
		
		$template = $this->loadTemplate($environment, $this->tableView->getFilter()->getTemplate());
		
		$filters = array();
		if($tableViewOrFilterArray instanceof TableView)
		{
			$filters = $tableViewOrFilterArray->getFilters();
		}
		else if(is_array($tableViewOrFilterArray))
		{
			$filters = $tableViewOrFilterArray;
		}
		else
		{
			TableException::canNotRenderFilter();
		}
		
		return $template->renderBlock('filter_rows', array(
			'filters' => $filters
		));
	}
	
	public function getFilterSubmitButtonContent(\Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$filterOptions = $tableView->getFilter();
		if($filterOptions === null)
		{
			return;
		}
		
		$template = $this->loadTemplate($environment, $tableView->getFilter()->getTemplate());
		$content = $template->renderBlock('filter_submit_button', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView),
			'submitLabel' => $filterOptions->getSubmitLabel(),
			'attributes' => $filterOptions->getSubmitAttributes()
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
	}
	
	public function getFilterResetLinkContent(\Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$filterOptions = $tableView->getFilter();
		if($filterOptions === null)
		{
			return;
		}
		
		$filterParams = array();
		foreach($tableView->getFilters() as $filter)
		{
			$filterParams[$filter->getName()] = null;
		}
		
		$template = $this->loadTemplate($environment, $tableView->getFilter()->getTemplate());
		$content = $template->renderBlock('filter_reset_link', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView),
			'resetLabel' => $filterOptions->getResetLabel(),
			'attributes' => $filterOptions->getResetAttributes(),
			'resetUrl' => $this->urlHelper->getUrlForParameters($filterParams)
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
	}
	
	public function getFilterEndContent(\Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$this->tableView = null;
		
		$template = $this->loadTemplate($environment, $tableView->getFilter()->getTemplate());
		$content = $template->renderBlock('filter_end', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView)
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
	}
	
	public function getFilterUrl(array $params)
	{		
		return $this->urlHelper->getUrlForParameters($params);
	}

	protected function getFilterNeedsFormEnviroment(TableView $tableView)
	{
		$tableName = $tableView->getName();
		if(array_key_exists($tableName, $this->filterNeedsFormEnvironment) === false)
		{
			$needsForEnvironment = false;
			foreach($tableView->getFilters() as $filter)
			{
				/* @var $filter FilterInterface */
				if($filter->needsFormEnviroment())
				{
					$needsForEnvironment = true;
					break;
				}
			}
			
			$this->filterNeedsFormEnvironment[$tableName] = $needsForEnvironment;
			return $needsForEnvironment;
		}
		
		return $this->filterNeedsFormEnvironment[$tableName];
	}
}

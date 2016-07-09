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
use JGM\TableBundle\Table\Pagination\Strategy\StrategyFactory;
use JGM\TableBundle\Table\Pagination\Strategy\StrategyInterface;
use JGM\TableBundle\Table\TableView;
use JGM\TableBundle\Table\Utils\UrlHelper;
use Twig_SimpleFunction;

/**
 * Twig extension for render the table pagination
 * at twig templates.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class PaginationExtension extends AbstractTwigExtension
{
	public function __construct(UrlHelper $urlHelper, TableStopwatchService $stopwatchService)
	{
		parent::__construct($urlHelper, $stopwatchService);
	}
	
	public function getName()
	{
		return 'pagination_';
	}
	
	public function getFunctions()
	{
		return array(
			new Twig_SimpleFunction (
				'table_pagination', 
				array($this, 'getTablePaginationContent'), 
				array('is_safe' => array('html'), 'needs_environment' => true)
			),
			new Twig_SimpleFunction (
				'table_pagination_option', 
				array($this, 'getTablePaginationOptionContent'),
				array('is_safe' => array('html'), 'needs_environment' => true)
			),
			new Twig_SimpleFunction (
				'page_url', 
				array($this, 'getPageUrl')
			),
		);
	}
	
	public function getTablePaginationContent(\Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		$pagination = $tableView->getPagination();
		
		if($pagination === null || ($tableView->getTotalPages() < 2 && $pagination->getShowEmpty() === false))
		{
			return;
		}
		
		// Get the page strategy.
		$strategy = StrategyFactory::getStrategy($tableView->getTotalPages(), $pagination->getMaxPages());
		/* @var $strategy StrategyInterface */ 

		$template = $this->loadTemplate($environment, $pagination->getTemplate());
		$content = $template->renderBlock('table_pagination', array(
			'currentPage' => $pagination->getCurrentPage(),
			'prevLabel' => $pagination->getPreviousLabel(),
			'nextLabel' => $pagination->getNextLabel(),
			'totalPages' => $tableView->getTotalPages(),
			'classes' => $pagination->getClasses(),
			'parameterName' => $pagination->getParameterName(),
			'pages' => $strategy->getPages($pagination->getCurrentPage(), $tableView->getTotalPages(), $pagination->getMaxPages())
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
	
	public function getTablePaginationOptionContent(\Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		$pagination = $tableView->getPagination();
		
		$optionValues = $pagination->getOptionValues();
		if($pagination === null || $optionValues == null || count($optionValues) < 2)
		{
			return;
		}
		
		if(!in_array($pagination->getItemsPerRow(), $optionValues))
		{
			$optionValues[] = $pagination->getItemsPerRow();
		}
		sort($optionValues);
		
		$label = $pagination->getOptionLabel();
		if(empty($label))
		{
			$label = null;
		}
		
		$template = $this->loadTemplate($environment, $pagination->getTemplate());
		$content = $template->renderBlock('table_pagination_option', array(
			'tableName' => $tableView->getName(),
			'values' => $optionValues,
			'attributes' => $pagination->getOptionAttributes(),
			'label' => $label,
			'labelAttributes' => $pagination->getOptionLabelAttributes(),
			'submitLabel' => $pagination->getOptionSubmitLabel(),
			'submitAttributes' => $pagination->getOptionSubmitAttributes(),
			'currentValue' => $pagination->getItemsPerRow()
		));
 		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
	
	public function getPageUrl($parameterName, $page)
	{
		return $this->urlHelper->getUrlForParameters(array(
			$parameterName => $page
		));
	}
}

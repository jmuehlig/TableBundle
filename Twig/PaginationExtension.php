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
use JGM\TableBundle\Table\OptionsResolver\TableOptions;
use JGM\TableBundle\Table\Pagination\OptionsResolver\PaginationOptions;
use JGM\TableBundle\Table\Pagination\Strategy\StrategyFactory;
use JGM\TableBundle\Table\Pagination\Strategy\StrategyInterface;
use JGM\TableBundle\Table\TableView;
use JGM\TableBundle\Table\Utils\UrlHelper;
use Twig_Environment;
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
				'table_pagination_option_begin', 
				array($this, 'getTablePaginationOptionBeginContent'),
				array('is_safe' => array('html'), 'needs_environment' => true)
			),
			new Twig_SimpleFunction (
				'table_pagination_option_label', 
				array($this, 'getTablePaginationOptionLabelContent'),
				array('is_safe' => array('html'), 'needs_environment' => true)
			),
			new Twig_SimpleFunction (
				'table_pagination_option_input', 
				array($this, 'getTablePaginationOptionInputContent'),
				array('is_safe' => array('html'), 'needs_environment' => true)
			),
			new Twig_SimpleFunction (
				'table_pagination_option_button', 
				array($this, 'getTablePaginationOptionButtonContent'),
				array('is_safe' => array('html'), 'needs_environment' => true)
			),
			new Twig_SimpleFunction (
				'table_pagination_option_end', 
				array($this, 'getTablePaginationOptionEndContent'),
				array('is_safe' => array('html'), 'needs_environment' => true)
			),
			new Twig_SimpleFunction (
				'page_url', 
				array($this, 'getPageUrl')
			),
		);
	}
	
	public function getTablePaginationContent(Twig_Environment $environment, TableView $tableView)
	{
		if(	$tableView->hasPagination() === false
			|| (
				$tableView->getPaginationOption(PaginationOptions::TOTAL_PAGES) < 2 
				&& $tableView->getPaginationOption(PaginationOptions::SHOW_EMPTY) === false
			)
		)
		{
			return;
		}
		
		// Get the page strategy.
		$strategy = StrategyFactory::getStrategy(
			$tableView->getTableOption(TableOptions::TOTAL_ITEMS),
			$tableView->getPaginationOption(PaginationOptions::MAX_PAGES)
		);
		/* @var $strategy StrategyInterface */ 

		$template = $this->loadTemplate($environment, $tableView->getPaginationOption(PaginationOptions::TEMPLATE));
		$content = $template->renderBlock('table_pagination', array(
			'currentPage' => $tableView->getPaginationOption(PaginationOptions::CURRENT_PAGE),
			'prevLabel' => $tableView->getPaginationOption(PaginationOptions::PREV_LABEL),
			'nextLabel' => $tableView->getPaginationOption(PaginationOptions::NEXT_LABEL),
			'totalPages' => $tableView->getPaginationOption(PaginationOptions::TOTAL_PAGES),
			'classes' => array(
				'ul' => $tableView->getPaginationOption(PaginationOptions::UL_CLASS),
				'li' => array(
					'default' => $tableView->getPaginationOption(PaginationOptions::LI_CLASS),
					'active' => $tableView->getPaginationOption(PaginationOptions::LI_CLASS_ACTIVE),
					'disabled' => $tableView->getPaginationOption(PaginationOptions::LI_CLASS_DISABLED)
				)
			),
			'parameterName' => $tableView->getPaginationOption(PaginationOptions::PARAM),
			'pages' => $strategy->getPages(
				$tableView->getPaginationOption(PaginationOptions::CURRENT_PAGE), 
				$tableView->getPaginationOption(PaginationOptions::TOTAL_PAGES), 
				$tableView->getPaginationOption(PaginationOptions::MAX_PAGES)
			)
		));
		
		return $content;
	}
	
	public function getTablePaginationOptionContent(Twig_Environment $environment, TableView $tableView)
	{
		$optionValues = $tableView->getPaginationOption(PaginationOptions::OPTION_VALUES);
		if(	$tableView->hasPagination() === false || $optionValues  === null || count($optionValues) < 2)
		{
			return;
		}
		
		$template = $this->loadTemplate($environment, $tableView->getPaginationOption(PaginationOptions::TEMPLATE));
		$content = $template->renderBlock('table_pagination_option', array(
			'tableView' => $tableView
		));
 		
		return $content;
	}
	
	public function getTablePaginationOptionBeginContent(Twig_Environment $environment, TableView $tableView)
	{
		$optionValues = $tableView->getPaginationOption(PaginationOptions::OPTION_VALUES);
		if(	$tableView->hasPagination() === false || $optionValues  === null || count($optionValues) < 2)
		{
			return;
		}
		
		$template = $this->loadTemplate($environment, $tableView->getPaginationOption(PaginationOptions::TEMPLATE));
		$content = $template->renderBlock('table_pagination_option_begin', array(
			'tableName' => $tableView->getName()
		));
 		
		return $content;
	}
	
	public function getTablePaginationOptionLabelContent(Twig_Environment $environment, TableView $tableView)
	{
		$optionValues = $tableView->getPaginationOption(PaginationOptions::OPTION_VALUES);
		if(	$tableView->hasPagination() === false || $optionValues  === null || count($optionValues) < 2)
		{
			return;
		}
		
		$label = $tableView->getPaginationOption(PaginationOptions::OPTION_LABEL);
		if(empty($label))
		{
			$label = null;
		}
		
		$template = $this->loadTemplate($environment, $tableView->getPaginationOption(PaginationOptions::TEMPLATE));
		$content = $template->renderBlock('table_pagination_option_label', array(
			'tableName' => $tableView->getName(),
			'label' => $label,
			'labelAttributes' => $tableView->getPaginationOption(PaginationOptions::OPTION_LABEL_ATTRIBUTES)
		));
 		
		return $content;
	}
	
	public function getTablePaginationOptionInputContent(Twig_Environment $environment, TableView $tableView)
	{
		$optionValues = $tableView->getPaginationOption(PaginationOptions::OPTION_VALUES);
		if(	$tableView->hasPagination() === false || $optionValues  === null || count($optionValues) < 2)
		{
			return;
		}
		
		if(!in_array($tableView->getPaginationOption(PaginationOptions::ROWS_PER_PAGE), $optionValues))
		{
			$optionValues[] = $tableView->getPaginationOption(PaginationOptions::ROWS_PER_PAGE);
			sort($optionValues);
		}
		
		$template = $this->loadTemplate($environment, $tableView->getPaginationOption(PaginationOptions::TEMPLATE));
		$content = $template->renderBlock('table_pagination_option_input', array(
			'tableName' => $tableView->getName(),
			'values' => $optionValues,
			'attributes' => $tableView->getPaginationOption(PaginationOptions::OPTION_ATTRIBUTES),
			'currentValue' => $tableView->getPaginationOption(PaginationOptions::ROWS_PER_PAGE)
		));
 		
		return $content;
	}
	
	public function getTablePaginationOptionButtonContent(Twig_Environment $environment, TableView $tableView)
	{
		$optionValues = $tableView->getPaginationOption(PaginationOptions::OPTION_VALUES);
		if(	$tableView->hasPagination() === false || $optionValues  === null || count($optionValues) < 2)
		{
			return;
		}
		
		$template = $this->loadTemplate($environment, $tableView->getPaginationOption(PaginationOptions::TEMPLATE));
		$content = $template->renderBlock('table_pagination_option_button', array(
			'tableName' => $tableView->getName(),
			'submitLabel' => $tableView->getPaginationOption(PaginationOptions::OPTION_SUBMIT_LABEL),
			'submitAttributes' => $tableView->getPaginationOption(PaginationOptions::OPTION_SUBMIT_ATTRIBUTES),
		));
 		
		return $content;
	}
	
	public function getTablePaginationOptionEndContent(Twig_Environment $environment, TableView $tableView)
	{
		$optionValues = $tableView->getPaginationOption(PaginationOptions::OPTION_VALUES);
		if(	$tableView->hasPagination() === false || $optionValues  === null || count($optionValues) < 2)
		{
			return;
		}
		
		$template = $this->loadTemplate($environment, $tableView->getPaginationOption(PaginationOptions::TEMPLATE));
		$content = $template->renderBlock('table_pagination_option_end', array());
 		
		return $content;
	}
	
	public function getPageUrl($parameterName, $page)
	{
		return $this->urlHelper->getUrlForParameters(array(
			$parameterName => $page
		));
	}
}

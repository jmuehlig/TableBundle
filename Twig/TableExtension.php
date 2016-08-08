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
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Order\OptionsResolver\OrderOptions;
use JGM\TableBundle\Table\Pagination\OptionsResolver\PaginationOptions;
use JGM\TableBundle\Table\TableView;
use JGM\TableBundle\Table\Utils\UrlHelper;
use Twig_Environment;
use Twig_SimpleFunction;

/**
 * Twig extension for render the table view
 * at twig templates.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class TableExtension extends AbstractTwigExtension
{
	public function __construct(UrlHelper $urlHelper, TableStopwatchService $stopwatchService)
	{
		parent::__construct($urlHelper, $stopwatchService);
	}
	
	public function getName()
	{
		return 'table';
	}
	
	public function getFunctions()
	{
		return array(
			new Twig_SimpleFunction('table', array($this, 'getTableContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction('table_begin', array($this, 'getTableBeginContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction('table_head', array($this, 'getTableHeadContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction('table_body', array($this, 'getTableBodyContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction('table_end', array($this, 'getTableEndContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
		);
	}
	
	public function getTableContent(Twig_Environment $environment, TableView $tableView)
	{
		$template = $this->loadTemplate($environment, $tableView->getTableOption(TableOptions::TEMPLATE));
		
		return $template->renderBlock('table', array(
			'view' => $tableView,
			'isPaginatable' => $tableView->hasPagination()
		));
	}
	
	public function getTableBeginContent(Twig_Environment $environment, TableView $tableView)
	{
		$template = $this->loadTemplate($environment, $tableView->getTableOption(TableOptions::TEMPLATE));
		
		$content = $template->renderBlock('table_begin', array(
			'name' => $tableView->getName(),
			'attributes' => $tableView->getTableOption(TableOptions::ATTRIBUTES),
			'isSelectable' => count($tableView->getSelectionButtons()) > 0
		));
		
		return $content;
	}
	
	public function getTableHeadContent(\Twig_Environment $environment, TableView $tableView)
	{
		$templateName = $tableView->getTableOption(TableOptions::TEMPLATE);
		$viewParameters = array('columns' => $tableView->getColumns());
		if($tableView->hasOrder())
		{
			$templateName = $tableView->getOrderOption(OrderOptions::TEMPLATE);
			$parameters = array(
				'columnName' => $tableView->getOrderOption(OrderOptions::PARAM_COLUMN), 
				'direction' => $tableView->getOrderOption(OrderOptions::PARAM_DIRECTION),
			);
			if($tableView->hasPagination())
			{
				$parameters['pagination'] = $tableView->getPaginationOption(PaginationOptions::PARAM);
			}
			else
			{
				$parameters['pagination'] = null;
			}
			$viewParameters['parameters'] = $parameters;
			$viewParameters['currentDirection'] = $tableView->getOrderOption(OrderOptions::CURRENT_DIRECTION);
			$viewParameters['orderHtml'] =  array(
				Order::DIRECTION_ASC => $tableView->getOrderOption(OrderOptions::HTML_ASC),
				Order::DIRECTION_DESC => $tableView->getOrderOption(OrderOptions::HTML_DESC)
			);
			$viewParameters['currentColumnName'] = $tableView->getOrderOption(OrderOptions::CURRENT_COLUMN);
		}
		
		$template = $this->loadTemplate($environment, $templateName);
		$content = $template->renderBlock('table_head', $viewParameters);
		
		return $content;
	}
	
	public function getTableBodyContent(\Twig_Environment $environment, TableView $tableView)
	{
		$template = $this->loadTemplate($environment, $tableView->getTableOption(TableOptions::TEMPLATE));
		$content = $template->renderBlock('table_body', array(
			'columns' => $tableView->getColumns(),
			'rows' => $tableView->getRows(),
			'emptyValue' => $tableView->getTableOption(TableOptions::EMPTY_VALUE),
			'tableView' => $tableView
		));
		
		return $content;
	}
	
	public function getTableEndContent(\Twig_Environment $environment, TableView $tableView, $renderSelectionButtons = true)
	{
		$template = $this->loadTemplate($environment, $tableView->getTableOption(TableOptions::TEMPLATE));
		$content = $template->renderBlock('table_end', array(
			'tableView' => $tableView,
			'isSelectable' => count($tableView->getSelectionButtons()) > 0,
			'columnsLength' => count($tableView->getColumns()),
			'renderSelectionButtons' => $renderSelectionButtons
		));
		
		return $content;
	}
}

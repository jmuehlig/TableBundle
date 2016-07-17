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
use JGM\TableBundle\Table\Order\Model\Order;
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
		$template = $this->loadTemplate($environment, $tableView->getTemplateName());
		
		return $template->renderBlock('table', array(
			'view' => $tableView,
			'isPaginatable' => $tableView->getPagination() !== null
		));
	}
	
	public function getTableBeginContent(Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		$template = $this->loadTemplate($environment, $tableView->getTemplateName());
		
		$content = $template->renderBlock('table_begin', array(
			'name' => $tableView->getName(),
			'attributes' => $tableView->getAttributes()
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
	
	public function getTableHeadContent(\Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		$templateName = $tableView->getTemplateName();
		$viewParameters = array('columns' => $tableView->getColumns());
		if($tableView->getOrder() !== null)
		{
			$order = $tableView->getOrder();
			/* @var $order Order */
			$templateName = $tableView->getOrder()->getTemplate();
			$parameters = array(
				'columnName' => $order->getParamColumnName(), 
				'direction' => $order->getParamDirectionName(),
			);
			if($tableView->getPagination() !== null)
			{
				$parameters['pagination'] = $tableView->getPagination()->getParameterName();
			}
			else
			{
				$parameters['pagination'] = null;
			}
			$viewParameters['parameters'] = $parameters;
			$viewParameters['classes'] = $order->getClasses();
			$viewParameters['orderHtml'] = $order->getHtml();
			$viewParameters['currentDirection'] = $order->getCurrentDirection();
			$viewParameters['currentColumnName'] = $order->getCurrentColumnName();
		}
		
		$template = $this->loadTemplate($environment, $templateName);
		$content = $template->renderBlock('table_head', $viewParameters);
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
	
	public function getTableBodyContent(\Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		$template = $this->loadTemplate($environment, $tableView->getTemplateName());
		$content = $template->renderBlock('table_body', array(
			'columns' => $tableView->getColumns(),
			'rows' => $tableView->getRows(),
			'emptyValue' => $tableView->getEmptyValue()
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
	
	public function getTableEndContent(\Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		$template = $this->loadTemplate($environment, $tableView->getTemplateName());
		$content = $template->renderBlock('table_end', array());
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
}

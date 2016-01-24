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
use JGM\TableBundle\Table\Utils\UrlHelper;
use Twig_SimpleFunction;

/**
 * Twig extension implementing helper methods
 * for the order component. The rendering
 * is part of the TableExtension
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class OrderExtension extends AbstractTwigExtension
{
	public function __construct(UrlHelper $urlHelper, TableStopwatchService $stopwatchService)
	{
		parent::__construct($urlHelper, $stopwatchService);
	}
	
	public function getName()
	{
		return 'order';
	}
	
	public function getFunctions()
	{
		return array(
			new Twig_SimpleFunction ('order_url', array($this, 'getOrderUrl')),
		);
	}
	
	public function getOrderUrl($parameterColumnName, $columnName, $parameterDirection, $currentDirection, $currentColumnName, $parameterPagination = null)
	{
		$parameters = array($parameterColumnName => $columnName);
		if($currentColumnName == $columnName && $currentDirection == Order::DIRECTION_ASC)
		{
			$parameters[$parameterDirection] = Order::DIRECTION_DESC;
		}
		else
		{
			$parameters[$parameterDirection] = Order::DIRECTION_ASC;
		}
		
		// Start at first page.
		if($parameterPagination !== null && empty($parameterPagination) === false)
		{
			$parameters[$parameterPagination] = 1;
		}
		
		return $this->urlHelper->getUrlForParameters($parameters);
	}
}

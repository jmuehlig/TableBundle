<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Order\OptionsResolver;

use JGM\TableBundle\Table\Order\Model\Order;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Default options resolver for the order component.
 * Setting the default order options and transforms
 * the options to the Order model.
 *
 * @author	Jan Mühlig
 * @since	1.0
 */
class OrderOptionsResolver extends OptionsResolver
{
	function __construct(ContainerInterface $container) 
	{
		$globalDefaults = $container->getParameter('jgm_table.order_default_options');
		
		$this->setDefaults(array(
			'param_direction' => $globalDefaults['param_direction'],
			'param_column' => $globalDefaults['param_column'],
			'empty_direction' => $globalDefaults['empty_direction'],
			'empty_column' => $globalDefaults['empty_column'],
			'class_asc' => $globalDefaults['class_asc'],
			'class_desc' => $globalDefaults['class_desc']
		));
		
		$this->setAllowedTypes('param_direction', 'string');
		$this->setAllowedTypes('param_column', 'string');
		$this->setAllowedTypes('empty_direction', 'string');
		$this->setAllowedTypes('empty_column', array('string', 'null'));
		$this->setAllowedTypes('class_asc', array('string', 'null'));
		$this->setAllowedTypes('class_desc', array('string', 'null'));
	}
	
	/**
	 * Creating an order model from
	 * resolver.
	 * 
	 * @return Order
	 */
	public function toOrder()
	{
		$order = $this->resolve(array());
		
		return new Order(
			$order['param_direction'],
			$order['param_column'],
			$order['empty_direction'],
			$order['empty_column'],
			array(
				Order::DIRECTION_ASC => $order['class_asc'], 
				Order::DIRECTION_DESC => $order['class_desc']
			)
		);
	}
}

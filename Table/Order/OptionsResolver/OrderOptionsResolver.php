<?php
namespace JGM\TableBundle\Table\Order\OptionsResolver;

use JGM\TableBundle\Table\Order\Model\Order;
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
	function __construct() 
	{
		parent::__construct();
		
		$this->setDefaults(array(
			'param_direction' => 'direction',
			'param_column' => 'column',
			'empty_direction' => 'desc',
			'empty_column' => null,
			'class_asc' => '',
			'class_desc' => ''
		));
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
			array(Order::DIRECTION_ASC => $order['class_asc'], Order::DIRECTION_DESC => $order['class_desc'])
		);
	}
}

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
	public function __construct(ContainerInterface $container) 
	{
		$globalDefaults = $container->getParameter('jgm_table.order_default_options');
		
		$this->setDefaults(array(
			OrderOptions::TEMPLATE => $globalDefaults[OrderOptions::TEMPLATE],
			OrderOptions::PARAM_DIRECTION => $globalDefaults[OrderOptions::PARAM_DIRECTION],
			OrderOptions::PARAM_COLUMN => $globalDefaults[OrderOptions::PARAM_COLUMN],
			OrderOptions::EMPTY_DIRECTION => $globalDefaults[OrderOptions::EMPTY_DIRECTION],
			OrderOptions::EMPTY_COLUMN => $globalDefaults[OrderOptions::EMPTY_COLUMN],
			OrderOptions::HTML_ASC => $globalDefaults[OrderOptions::HTML_ASC],
			OrderOptions::HTML_DESC => $globalDefaults[OrderOptions::HTML_DESC]
		));
		
		$this->setAllowedTypes(OrderOptions::TEMPLATE, 'string');
		$this->setAllowedTypes(OrderOptions::PARAM_DIRECTION, 'string');
		$this->setAllowedTypes(OrderOptions::PARAM_COLUMN, 'string');
		$this->setAllowedTypes(OrderOptions::PARAM_DIRECTION, 'string');
		$this->setAllowedTypes(OrderOptions::EMPTY_COLUMN, array('string', 'null'));
		$this->setAllowedTypes(OrderOptions::HTML_ASC, array('string', 'null'));
		$this->setAllowedTypes(OrderOptions::HTML_DESC, array('string', 'null'));
	}
}

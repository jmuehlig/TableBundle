<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Order\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * If a table type implements this interface, it marks the table
 * as ordered.
 * The interface provides a method for setting the default options
 * for beeing sortable.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
interface OrderTypeInterface
{
	/**
	 * Configures the default options for the order table type.
	 * 
	 * @param OptionsResolver $resolver
	 */
	public function configureOrderOptions(OptionsResolver $resolver);
}

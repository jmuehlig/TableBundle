<?php
namespace JGM\TableBundle\Table\Order\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
	 * Sets the default options for the order table type.
	 * 
	 * @param OptionsResolverInterface $resolver
	 */
	public function setOrderDefaultOptions(OptionsResolverInterface $resolver);
}
<?php
namespace JGM\TableBundle\Table\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * If a table type implements this interface, it marks the table
 * as sortable.
 * The interface provides a method for setting the default options
 * for beeing sortable.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
interface SortableInterface
{
	/**
	 * Sets the default options for the sortable table type.
	 * 
	 * @param OptionsResolverInterface $resolver
	 */
	public function setSortableDefaultOptions(OptionsResolverInterface $resolver);
}
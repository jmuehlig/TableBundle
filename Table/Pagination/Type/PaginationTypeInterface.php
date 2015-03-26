<?php

namespace JGM\TableBundle\Table\Pagination\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * If a table type implements this interface, it marks the table
 * as using pagination.
 * The interface provides a method for setting the default options
 * of the pagination.
 * 
 * @author Jan Mühlig <mail@janmuehlig.de>
 * @since 1.0.0 
 */
interface PaginationTypeInterface
{
	/**
	 * Sets the default options for the pagination of the table type.
	 * 
	 * @param OptionsResolverInterface $resolver
	 */
	public function setPaginationDefaultOptions(OptionsResolverInterface $resolver);
}
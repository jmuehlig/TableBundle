<?php

namespace JGM\TableBundle\Table\Pagination\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * If a table type implements this interface, it marks the table
 * as using pagination.
 * The interface provides a method for setting the default options
 * of the pagination.
 * 
 * @author Jan Mühlig <mail@janmuehlig.de>
 * @since 1.0
 */
interface PaginationTypeInterface
{
	/**
	 * Configures the options for the pagination of the table type.
	 * 
	 * @since 1.1
	 * @param OptionsResolver $resolver
	 */
	public function configurePaginationOptions(OptionsResolver $resolver);
}
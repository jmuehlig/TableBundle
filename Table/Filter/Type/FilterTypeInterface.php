<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Filter\Type;

use JGM\TableBundle\Table\Filter\FilterBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * If a table type implements this interface, it marks the table
 * as using filters.
 * The interface provides a method for building the filters, used
 * by the table.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
interface FilterTypeInterface
{
	/**
	 * Builds the filters, by adding some filters to the builder.
	 * 
	 * @param FilterBuilder $filterBuilder	Instance of the filter builder.
	 */
	public function buildFilter(FilterBuilder $filterBuilder);
	
	/**
	 * Sets the options for the filter buttons.
	 * 
	 * @param OptionsResolverInterface $resolver
	 * @deprecated since version	1.1, to be removed in 1.2.
	 *								Use the method `configureFilterButtonOptions` instead.
	 */
	public function setFilterButtonOptions(OptionsResolverInterface $resolver);
	
	/**
	 * Configures the options for the filter buttons.
	 * 
	 * @param OptionsResolver $resolver
	 */
	public function configureFilterButtonOptions(OptionsResolver $resolver);
}

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
	public function buildFilter(FilterBuilder $filterBuilder);
	
	public function setFilterButtonOptions(OptionsResolverInterface $resolver);
}

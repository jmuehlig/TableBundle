<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Pagination\OptionsResolver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Default options resolver for the pagination component.
 * Setting the default pagination options and transforms
 * the options to the Pagination model.
 *
 * @author	Jan Mühlig
 * @since	1.0
 */
class PaginationOptionsResolver extends OptionsResolver
{
	function __construct(ContainerInterface $container) 
	{
		$globalDefaults = $container->getParameter('jgm_table.pagination_default_options');

		$this->setDefaults(array(
			PaginationOptions::TEMPLATE => $globalDefaults[PaginationOptions::TEMPLATE],
			PaginationOptions::PARAM => $globalDefaults[PaginationOptions::PARAM],
			PaginationOptions::ROWS_PER_PAGE => $globalDefaults[PaginationOptions::ROWS_PER_PAGE],
			PaginationOptions::SHOW_EMPTY => $globalDefaults[	PaginationOptions::SHOW_EMPTY],
			PaginationOptions::UL_CLASS => $globalDefaults[PaginationOptions::UL_CLASS],
			PaginationOptions::LI_CLASS => $globalDefaults[PaginationOptions::LI_CLASS],
			PaginationOptions::LI_CLASS_ACTIVE => $globalDefaults[PaginationOptions::LI_CLASS_ACTIVE],
			PaginationOptions::LI_CLASS_DISABLED => $globalDefaults[PaginationOptions::LI_CLASS_DISABLED],
			PaginationOptions::PREV_LABEL => $globalDefaults[PaginationOptions::PREV_LABEL],
			PaginationOptions::NEXT_LABEL => $globalDefaults[PaginationOptions::NEXT_LABEL],
			PaginationOptions::MAX_PAGES => $globalDefaults[PaginationOptions::MAX_PAGES],
			
			PaginationOptions::OPTION_VALUES => $globalDefaults[PaginationOptions::OPTION_VALUES],
			PaginationOptions::OPTION_ATTRIBUTES => $globalDefaults[PaginationOptions::OPTION_ATTRIBUTES],
			PaginationOptions::OPTION_LABEL => $globalDefaults[PaginationOptions::OPTION_LABEL],
			PaginationOptions::OPTION_LABEL_ATTRIBUTES => $globalDefaults[PaginationOptions::OPTION_LABEL_ATTRIBUTES],
			PaginationOptions::OPTION_SUBMIT_LABEL => $globalDefaults[PaginationOptions::OPTION_SUBMIT_LABEL],
			PaginationOptions::OPTION_SUBMIT_ATTRIBUTES => $globalDefaults[PaginationOptions::OPTION_SUBMIT_ATTRIBUTES]
		));
		
		$this->setAllowedTypes(PaginationOptions::TEMPLATE, 'string');
		$this->setAllowedTypes(PaginationOptions::PARAM, 'string');
		$this->setAllowedTypes(PaginationOptions::ROWS_PER_PAGE, 'integer');
		$this->setAllowedTypes(PaginationOptions::SHOW_EMPTY, 'boolean');
		$this->setAllowedTypes(PaginationOptions::UL_CLASS, array('string', 'null'));
		$this->setAllowedTypes(PaginationOptions::LI_CLASS, array('string', 'null'));
		$this->setAllowedTypes(PaginationOptions::LI_CLASS_ACTIVE, array('string', 'null'));
		$this->setAllowedTypes(PaginationOptions::LI_CLASS_DISABLED, array('string', 'null'));
		$this->setAllowedTypes(PaginationOptions::PREV_LABEL, 'string');
		$this->setAllowedTypes(PaginationOptions::NEXT_LABEL, 'string');
		$this->setAllowedTypes(PaginationOptions::MAX_PAGES, array('integer', 'null'));
		
		$this->setAllowedTypes(PaginationOptions::OPTION_VALUES, 'array');
		$this->setAllowedTypes(PaginationOptions::OPTION_ATTRIBUTES, 'array');
		$this->setAllowedTypes(PaginationOptions::OPTION_LABEL, array('string', 'null'));
		$this->setAllowedTypes(PaginationOptions::OPTION_LABEL_ATTRIBUTES, 'array');
		$this->setAllowedTypes(PaginationOptions::OPTION_SUBMIT_LABEL, array('string', 'null'));
		$this->setAllowedTypes(PaginationOptions::OPTION_SUBMIT_ATTRIBUTES, 'array');
	}
}

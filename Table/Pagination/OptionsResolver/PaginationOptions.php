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

/**
 * Holder class for pagination options.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class PaginationOptions
{
	const TEMPLATE					= 'template';
	const PARAM						= 'param';
	const ROWS_PER_PAGE				= 'rows_per_page';
	const SHOW_EMPTY				= 'show_empty';
	const UL_CLASS					= 'ul_class';
	const LI_CLASS					= 'li_class';
	const LI_CLASS_ACTIVE			= 'li_class_active';
	const LI_CLASS_DISABLED			= 'li_class_disabled';
	const PREV_LABEL				= 'prev_label';
	const NEXT_LABEL				= 'next_label';
	const MAX_PAGES					= 'max_pages';
	const OPTION_VALUES				= 'option_values';
	const OPTION_ATTRIBUTES			= 'option_attr';
	const OPTION_LABEL				= 'option_label';
	const OPTION_LABEL_ATTRIBUTES	= 'option_label_attr';
	const OPTION_SUBMIT_LABEL		= 'option_submit_label';
	const OPTION_SUBMIT_ATTRIBUTES	= 'option_submit_attr';
	const CURRENT_PAGE				= 'current_page';
	const TOTAL_PAGES				= 'total_pages';
}

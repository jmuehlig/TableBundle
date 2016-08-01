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

/**
 * Holder class for order options.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class OrderOptions
{
	const TEMPLATE			= 'template';
	const PARAM_DIRECTION	= 'param_direction';
	const PARAM_COLUMN		= 'param_column';
	const EMPTY_DIRECTION	= 'empty_direction';
	const EMPTY_COLUMN		= 'empty_column';
	const HTML_ASC			= 'html_asc';
	const HTML_DESC			= 'html_desc';
	
	const CURRENT_COLUMN	= 'current_column';
	const CURRENT_DIRECTION	= 'current_direction';
}

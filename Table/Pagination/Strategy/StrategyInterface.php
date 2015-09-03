<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Pagination\Strategy;

/**
 * Strategy for pages -drawed at pagination- if number of maximal pages
 * is lower than total pages.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
interface StrategyInterface 
{
	/**
	 * Creates an array of pages, which will be clickable at the pagination,
	 * if there are too much pages.
	 * 
	 * @param int $currentPage		Number of current page.
	 * @param int $totalPages		Number of total pages.
	 * @param int $maxPages			Number of maxmimal pages.
	 * 
	 * @return array				List with pages for the pagination, could be unsorted. 
	 */
	public function getPages($currentPage, $totalPages, $maxPages);
}

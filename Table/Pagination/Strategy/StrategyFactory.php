<?php

namespace JGM\TableBundle\Table\Pagination\Strategy;

/**
 * The StrategyFactory will choose the right pagination
 * strategy in dependency of total pages and maximal pages.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class StrategyFactory 
{
	/**
	 * Creates the proper strategy considered by total and maximal pages.
	 * 
	 * @param int $totalPages		Number of total pages.
	 * @param int $maxPages			Number of maximal pages.
	 * 
	 * @return StrategyInterface	Proper strategy.
	 */
	public static function getStrategy($totalPages, $maxPages)
	{
		if($totalPages > $maxPages)
		{
			return new SimpleLimitStrategy();
		}
		else
		{
			return new AllPagesStrategy();
		}
	}
}

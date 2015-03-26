<?php

namespace JGM\TableBundle\Table\Pagination\Strategy;

/**
 * Description of StrategyFactory
 *
 * @author Jan
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

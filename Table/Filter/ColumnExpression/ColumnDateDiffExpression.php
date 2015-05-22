<?php

namespace JGM\TableBundle\Table\Filter\ColumnExpression;

use DateTime;

/**
 * Description of ColumnDateDiffExpression
 *
 * @author Jan
 */
class ColumnDateDiffExpression extends ColumnNameExpression
{
	const DAY = 'day';
	const MONTH = 'month';
	const YEAR = 'year';
	
	protected $diffDate;
	protected $quotient = 1;
	
	public function __construct($columnName, $unit = self::DAY, DateTime $diffDate = null)
	{
		parent::__construct($columnName);
		
		if($diffDate === null)
		{
			$diffDate = new DateTime();
		}
		
		$unit = strtolower($unit);
		if($unit === self::MONTH)
		{
			$this->quotient = 30.4;
		}
		else if($unit === self::YEAR)
		{
			$this->quotient = 365.25;
		}
		
		$this->diffDate = $diffDate;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getDiffDate()
	{
		return $this->diffDate;
	}
	
	public function getQuotient()
	{
		return $this->quotient;
	}
}

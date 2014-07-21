<?php

namespace PZAD\TableBundle\Table\Filter;

/**
 * Filter exception.
 */
class FilterException extends \Exception
{
	public function __construct($message)
	{
		parent::__construct($message);
	}
	
	public static function FilterTypeNotLegal($filterType)
	{
		$message = sprintf('FilterType "%s" is not a valid Type. Use FilterType::ENTITY or FilterType::PROPERTY.', $filterType);
		throw new FilterException($message);
	}
	
	public static function ValuesMustBeArray()
	{
		$message = 'The option "values" must be an array or null';
		throw new FilterException($message);
	}
	
	public static function NoClassFound()
	{
		$message = 'Please mention an entity, if you use a entity based filter.';
		throw new FilterException($message);
	}
	
	public static function NoRepositoryFound($class)
	{
		$message = sprintf('There is no repository for the entity "%s".', $class);
		throw new FilterException($message);
	}
	
	public static function FilterNotFound($filterName)
	{
		$message = sprintf('There is no filter called "%s".', $filterName);
		throw new FilterException($message);
	}
	
	public static function operatorNotValid($operator, $validOperators)
	{
		$message = sprintf('The operator "%s" is not a valid filter oprator. Use one of the following: %s', $operator, implode(',', $validOperators));
		throw new FilterException($message);
	}
}

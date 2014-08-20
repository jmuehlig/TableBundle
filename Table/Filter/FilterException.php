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
	
	public static function typeNotAllowed($filterType, array $legalFilterTypes)
	{
		$message = sprintf('The filter type "%s" is not valid. Valid types are "%s"', $filterType, implode(",", $legalFilterTypes));
		throw new FilterException($message);
	}
	
	public static function duplicatedFilterName($name)
	{
		$message = sprintf('Duplicated name for the filter "%s".', $name);
		throw new FilterException($message);
	}
	
	public static function filterClassNotImplementingInterface($class)
	{
		$message = sprintf('The filter type class "%s" does not implement the FilterInterface.', $class);
		throw new FilterException($message);
	}
	
	public static function isNoValidFilter($filter)
	{
		$message = sprintf('The given filter "%s" is no valid filter. This can not be rendered.', $filter);
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

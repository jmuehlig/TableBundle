<?php

namespace PZAD\TableBundle\Table;

/**
 * Table exception.
 */
class TableException extends \Exception
{	
	function __construct($message)
	{
		parent::__construct($message);
	}
	
	public static function classNotFound($class)
	{
		$message = "Entity %s not found.";
		throw new TableException(sprintf($message, $class));
	}
	
	public static function typeNotAllowed($type)
	{
		$message = "Type %s is not allowed.";
		throw new TableException(sprintf($message, $type));
	}
	
	public static function duplicatedColumnName($name)
	{
		$message = "Duplicated name for the column %s.";
		throw new TableException(sprintf($message, $name));
	}
	
	public static function fieldNotFound($field)
	{
		$message = "Field %s was not found.";
		throw new TableException(sprintf($message, $field));
	}
	
	public static function propertyNotFound($object, $property)
	{
		$message = "The entity %s has no public property named %s or a getter like 'has', 'get', 'is'.";
		throw new TableException(sprintf($message, get_class($object), $property));
	}
	
	public static function notInstanceOfContentGrabberInterface($object)
	{
		$message = "The ContentGrabber %s does not implements the ContentGrabberInterface";
		throw new TableException(sprintf($message, get_class($object)));
	}
	
	public static function paginationIncomplete($missingInformation)
	{
		$message = "We need more information to paginate your table: %s.";
		throw new TableException(sprintf($message, $missingInformation));
	}
	
	public static function sortIncomplete($missingInformation)
	{
		$message = "We need more information to sort your table: %s.";
		throw new TableException(sprintf($message, $missingInformation));
	}
	
	public static function sortParamNoArray($param)
	{
		$message = "The parameter %s has to be an array.";
		throw new TableException(sprintf($message, $param));
	}
	
	public static function noSortableColumn()
	{
		$message = "There is no sortable column. Pleas add a sortable column or remove the sort-attributes.";
		throw new TableException(sprintf($message));
	}
	
	public static function noSuchColumn($columnName)
	{
		$message = "There is no column named: %s.";
		throw new TableException(sprintf($message, $columnName));
	}
	
	public static function noSuchPorpertyOnEntity($property, $entity)
	{
		$message = "There is no property named: %s on entity class: %s";
		throw new TableException(sprintf($message, $property, get_class($entity)));
	}
	
	public static function noContentDefined($columnName)
	{
		$message = "You have to define a content-function or a content-grabber for the column: %s";
		throw new TableException(sprintf($message, $columnName));
	}
	
	public static function columnTypeAlreadyRegistered($columnType)
	{
		$message = "The column type: %s is already registered.";
		throw new TableException(sprintf($message, $columnType));
	}
	
	public static function columnClassNotFound($class)
	{
		$message = "The column type class: %s does not exist.";
		throw new TableException(sprintf($message, $class));
	}
	
	public static function columnClassNotImplementingInterface($class)
	{
		$message = 'The column type class "%s" does not implement the ColumnInterface.';
		throw new TableException(sprintf($message, $class));
	}
	
	public static function noQueryBuilder()
	{
		$message = 'You have to pass a QueryBuilder into the DataSource.';
		throw new TableException($message);
	}
	
	public static function filterNoView()
	{
		$message = "You have to use filter_begin() before you can render a single filter.";
		throw new TableException($message);
	}
	
	public static function canNotRenderFilter()
	{
		$message = "Please pass a TablieView, a FilterInterface or an array of FilterInterface objects to the filter()-method.";
		throw new TableException($message);
	}
}

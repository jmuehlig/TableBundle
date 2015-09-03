<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table;

/**
 * Exception which will be used for all exceptions
 * thrown by the table bundle itself.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class TableException extends \Exception
{	
	function __construct($message)
	{
		parent::__construct($message);
	}
	
	public static function classNotFound($class)
	{
		$message = "Entity '%s' not found.";
		throw new TableException(sprintf($message, $class));
	}
	
	public static function columnTypeNotAllowed($type)
	{
		$message = "Type '%s' is not allowed.";
		throw new TableException(sprintf($message, $type));
	}
	
	public static function duplicatedColumnName($name)
	{
		$message = "Duplicated name for the column: '%s'. Please use a column name only once.";
		throw new TableException(sprintf($message, $name));
	}
	
	public static function fieldNotFound($field)
	{
		$message = "Field '%s' was not found.";
		throw new TableException(sprintf($message, $field));
	}
	
	public static function propertyNotFound($object, $property)
	{
		$message = "The entity '%s' has no public property named '%s' or a getter like 'has', 'get', 'is'.";
		throw new TableException(sprintf($message, get_class($object), $property));
	}
	
	public static function notInstanceOfContentGrabberInterface($object)
	{
		$message = "The ContentGrabber '%s' does not implements the interface 'ContentGrabberInterface'.";
		throw new TableException(sprintf($message, get_class($object)));
	}
	
	public static function paginationIncomplete($missingInformation)
	{
		$message = "We need more information to paginate your table: '%s'.";
		throw new TableException(sprintf($message, $missingInformation));
	}
	
	public static function sortIncomplete($missingInformation)
	{
		$message = "We need more information to sort your table: '%s'.";
		throw new TableException(sprintf($message, $missingInformation));
	}
	
	public static function sortParamNoArray($param)
	{
		$message = "The parameter '%s' has to be an array.";
		throw new TableException(sprintf($message, $param));
	}
	
	public static function noSortableColumn()
	{
		$message = "There is no sortable column. Pleas add a sortable column or remove the sort-attributes.";
		throw new TableException($message);
	}
	
	public static function noSuchColumn($columnName)
	{
		$message = "There is no column named '%s'.";
		throw new TableException(sprintf($message, $columnName));
	}
	
	public static function noSuchFilter($filterName)
	{
		$message = "There is no filter named '%s'.";
		throw new TableException(sprintf($message, $filterName));
	}
	
	public static function noSuchPorpertyOnEntity($property, $entity)
	{
		$message = "There is no property named '%s' on entity class '%s'.";
		throw new TableException(sprintf($message, $property, get_class($entity)));
	}
	
	public static function noContentDefined($columnName)
	{
		$message = "You have to define a content-function or a content-grabber for the column '%s'.";
		throw new TableException(sprintf($message, $columnName));
	}
	
	public static function columnTypeAlreadyRegistered($columnType)
	{
		$message = "The column type '%s' is already registered.";
		throw new TableException(sprintf($message, $columnType));
	}
	
	public static function columnClassNotFound($class)
	{
		$message = "The column type class '%s' does not exist.";
		throw new TableException(sprintf($message, $class));
	}
	
	public static function columnClassNotImplementingInterface($class)
	{
		$message = "The column type class '%s' does not implement the 'ColumnInterface'.";
		throw new TableException(sprintf($message, $class));
	}
	
	public static function noQueryBuilder()
	{
		$message = 'You have to pass a QueryBuilder into the DataSource.';
		throw new TableException($message);
	}
	
	public static function isNoCallback()
	{
		$message = "Please pass a callback.";
		throw new TableException($message);
	}
	
	public static function filterNoView()
	{
		$message = "You have to use filter_begin() before you can render a single filter.";
		throw new TableException($message);
	}
	
	public static function canNotRenderFilter()
	{
		$message = "Please pass a TableView or an array of 'FilterInterface' implementing objects to the filter_rows()-method.";
		throw new TableException($message);
	}
	
	public static function filterTypeNotAllowed($filterType, array $legalFilterTypes)
	{
		$message = sprintf("The filter type '%s' is not valid. Valid types are: '%s'.", $filterType, implode("','", $legalFilterTypes));
		throw new TableException($message);
	}
	
	public static function duplicatedFilterName($name)
	{
		$message = sprintf("Duplicated name for the filter '%s'.", $name);
		throw new TableException($message);
	}
	
	public static function filterClassNotImplementingInterface($class)
	{
		$message = sprintf("The filter type class '%s' does not implement the 'FilterInterface'.", get_class($class));
		throw new TableException($message);
	}
	
	public static function isNoValidFilter($filter)
	{
		$message = sprintf("The given filter '%s' is not a valid filter. This can not be rendered.", $filter);
		throw new TableException($message);
	}
	
	public static function filterWidgetNotFound($widget)
	{
		$message = sprintf("The given filter widget '%s' is not valid.", $widget);
		throw new TableException($message);
	}
	
	public static function valuesMustBeArray()
	{
		$message = "The option 'values' must be an array or null.";
		throw new TableException($message);
	}
	
	public static function noClassFound()
	{
		$message = "Please mention an entity, if you use a entity based filter.";
		throw new TableException($message);
	}
	
	public static function noRepositoryFound($class)
	{
		$message = sprintf("There is no repository for the entity '%s'.", $class);
		throw new TableException($message);
	}
	
	public static function filterNotFound($filterName)
	{
		$message = sprintf("There is no filter called '%s'.", $filterName);
		throw new TableException($message);
	}
	
	public static function operatorNotValid($operator, $validOperators)
	{
		$message = sprintf("The operator '%s' is not a valid filter oprator. Use one of the following: '%s'", $operator, implode(',', $validOperators));
		throw new TableException($message);
	}
	
	public static function tableViewNotSet()
	{
		throw new TableException("You can not use the url helper without setting the table view.");
	}
	
	public static function paginationNotProvided()
	{
		throw new TableException("Pagination is not provided. Please use the PaginationTypeInterface to add these feature");
	}
	
	public static function orderNotProvided()
	{
		throw new TableException("Order is not provided. Please use the OrderTypeInterface to add these feature");
	}
}

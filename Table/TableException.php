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
	public function __construct($tableName, $message)
	{
		parent::__construct(sprintf(
			"%s%s",
			$tableName !== null ? sprintf("Error on table '%s': ", $tableName) : "",
			$message
		));
	}
	
	public static function columnTypeNotAllowed($tableName, $type)
	{
		$message = "Column type '%s' is not allowed.";
		throw new TableException($tableName, sprintf($message, $type));
	}
	
	public static function duplicatedColumnName($tableName, $name)
	{
		$message = "Duplicated name for column '%s'. Please use a column name only once.";
		throw new TableException($tableName, sprintf($message, $name));
	}
	
	public static function noSortableColumn($tableName)
	{
		$message = "There is no sortable column. Please add a sortable column or remove any sort-attributes.";
		throw new TableException($tableName, $message);
	}
	
	public static function noSuchColumn($tableName, $columnName)
	{
		$message = "There is no column named '%s'.";
		throw new TableException($tableName, sprintf($message, $columnName));
	}
	
	public static function noSuchFilter($tableName, $filterName)
	{
		$message = "There is no filter named '%s'.";
		throw new TableException($tableName, sprintf($message, $filterName));
	}
	
	public static function noSuchPropertyOnEntity($tableName, $property, $entity)
	{
		$message = "There is no property named '%s' on entity class '%s'.";
		throw new TableException($tableName, sprintf($message, $property, get_class($entity)));
	}
	
	public static function noContentDefined($tableName, $columnName)
	{
		$message = "You have to define a content-function or a content-grabber for the column '%s'.";
		throw new TableException($tableName, sprintf($message, $columnName));
	}
	
	public static function noQueryBuilder($tableName)
	{
		$message = 'You have to pass a QueryBuilder into the DataSource.';
		throw new TableException($tableName, $message);
	}
	
	public static function canNotRenderFilter()
	{
		$message = "Please pass a TableView or an array of 'FilterInterface' implementing objects to the filter_rows()-method.";
		throw new TableException(null, $message);
	}
	
	public static function filterTypeNotAllowed($tableName, $filterType, array $legalFilterTypes)
	{
		$message = sprintf("The filter type '%s' is not valid. Valid types are: '%s'.", $filterType, implode("','", $legalFilterTypes));
		throw new TableException($tableName, $message);
	}
	
	public static function duplicatedFilterName($tableName, $filterName)
	{
		$message = sprintf("Duplicated name for the filter '%s'.", $filterName);
		throw new TableException($tableName, $message);
	}
	
	public static function filterClassNotImplementingInterface($tableName, $class)
	{
		$message = sprintf("The filter type class '%s' does not implement the 'FilterInterface'.", get_class($class));
		throw new TableException($tableName, $message);
	}
	
	public static function filterWidgetNotFound($tableName, $widget)
	{
		$message = sprintf("The given filter widget '%s' is not valid.", $widget);
		throw new TableException($tableName, $message);
	}
	
	public static function operatorNotValid($operator, $validOperators)
	{
		$message = sprintf("The operator '%s' is not a valid filter operator. Use one of the following: '%s'.", $operator, implode(',', $validOperators));
		throw new TableException(null, $message);
	}
	
	public static function tableViewNotSet()
	{
		throw new TableException(null, "You can not use the url helper without setting the table view.");
	}
	
	public static function paginationNotProvided()
	{
		throw new TableException(null, "Pagination is not provided. Please use the PaginationTypeInterface to add these feature.");
	}
	
	public static function orderNotProvided()
	{
		throw new TableException(null, "Order is not provided. Please use the OrderTypeInterface to add these feature.");
	}
	
	public static function canNotHandleRequestAfterBild($tableName)
	{
		throw new TableException($tableName, "Can not handle request after build the table.");
	}
}

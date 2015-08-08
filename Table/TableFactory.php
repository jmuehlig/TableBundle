<?php

namespace JGM\TableBundle\Table;

use Doctrine\ORM\EntityManager;
use JGM\TableBundle\Table\Type\AbstractTableType;
use JGM\TableBundle\Table\Type\AnonymousTableType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;


/**
 * TableFactory for creating tables from controller.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class TableFactory
{	
	/**
	 * Container.
	 * 
	 * @var ContainerInterface
	 */
	private $container;
	
	/**
	 * Current request.
	 * 
	 * @var Request 
	 */
	private $request;
	
	/**
	 * EntityManager.
	 * 
	 * @var EntityManager 
	 */
	private $entityManager;
	
	/**
	 * Router.
	 * 
	 * @var RouterInterface 
	 */
	private $router;
	
	/**
	 * Are there multiple instances of tables
	 * on this view?
	 * 
	 * @var boolean
	 */
	private $isMulti = false;
	
	function __construct(ContainerInterface $container, EntityManager $entityManager, Request $request, RouterInterface $router)
	{
		$this->container = $container;
		$this->entityManager = $entityManager;
		$this->request = $request;
		$this->router = $router;
	}
	
	/**
	 * Builds a table by a table type.
	 * 
	 * @param AbstractTableType $tableType	TableType.
	 * @return	Table						Table.
	 */
	public function createTable(AbstractTableType $tableType)
	{
		$table = new Table($this->container, $this->entityManager, $this->request, $this->router, $this->isMulti);
		
		$this->isMulti = true;
		
		return $table->create($tableType);
	}
	
	/**
	 * Builds a table based on a anonymous builder function.
	 * 
	 * @param string		$entity	Name of the entity.
	 * @param callable		$build	Function for building the table.
	 * @param string|null	$name	Name of the table.
	 * 
	 * @return Table			Table.
	 */
	public function createAnonymousTable($entity, $build, $name = 'table')
	{
		return $this->createTable(new AnonymousTableType($entity, $build, $name));
	}
}

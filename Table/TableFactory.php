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

use Doctrine\ORM\EntityManager;
use JGM\TableBundle\Table\Type\AbstractTableType;
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
	 * @param array $options	Options of the table.
	 * @return	Table
	 */
	public function createTable(AbstractTableType $tableType, array $options = array())
	{
		$table = new Table($this->container, $this->entityManager, $this->request, $this->router, $this->isMulti);
		
		$this->isMulti = true;
		
		return $table->create($tableType, $options);
	}
	
	/**
	 * Creats a table builder, which is used to create
	 * tables without implementing a table type.
	 * 
	 * @param string $name	Name of the table.
	 * @param array $options	Options of the table.
	 * 
	 * @return AnonymousTableBuilder
	 */
	public function getTableBuilder($name, array $options = array())
	{
		$table = new Table($this->container, $this->entityManager, $this->request, $this->router, $this->isMulti);
		
		$this->isMulti = true;
		
		return new AnonymousTableBuilder($name, $options, $table);
	}
}

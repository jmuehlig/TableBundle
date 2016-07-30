<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\DependencyInjection\Service;

use Doctrine\ORM\EntityManager;
use JGM\TableBundle\DependencyInjection\Service\TableStopwatchService;
use JGM\TableBundle\Table\Table;
use JGM\TableBundle\Table\TableTypeBuilder;
use JGM\TableBundle\Table\Type\AbstractTableType;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;


/**
 * Service TableFactory for creating tables from controller.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class TableFactoryService
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
	 * @var TableStopwatchService
	 */
	private $stopwatchService;
	
	/**
	 * @var TableHintService
	 */
	private $hintService;
	
	/**
	 * Are there multiple instances of tables
	 * on this view?
	 * 
	 * @var boolean
	 */
	private $isMulti = false;
	
	function __construct(ContainerInterface $container, EntityManager $entityManager, RequestStack $requestStack, RouterInterface $router, TableStopwatchService $stopwatchService, TableHintService $hintService)
	{
		$this->container = $container;
		$this->entityManager = $entityManager;
		$this->request = $requestStack->getCurrentRequest();
		$this->router = $router;
		$this->stopwatchService = $stopwatchService;
		$this->hintService = $hintService;
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
		$table = new Table($this->container, $this->entityManager, $this->request, $this->router, $this->isMulti, $this->stopwatchService, $this->hintService);
		
		$this->isMulti = true;
		
		return $table->create($tableType, $options);
	}
	
	/**
	 * Creats a table builder, which is used to create
	 * tables without implementing a table type.
	 * 
	 * @param string $name		Name of the table.
	 * @param array $options	Options of the table.
	 * 
	 * @return TableTypeBuilder
	 */
	public function createTableTypeBuilder($name, array $options = array())
	{
		$table = new Table($this->container, $this->entityManager, $this->request, $this->router, $this->logger, $this->isMulti, $this->stopwatchService);
		
		$this->isMulti = true;
		
		return new TableTypeBuilder($name, $options, $table);
	}
}

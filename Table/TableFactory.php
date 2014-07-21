<?php
namespace PZAD\TableBundle\Table;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;

/**
 * TableFactory.
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
	
	function __construct(ContainerInterface $container, EntityManager $entityManager, Request $request, RouterInterface $router)
	{
		$this->container = $container;
		$this->entityManager = $entityManager;
		$this->request = $request;
		$this->router = $router;
	}
	
	public function createTable(AbstractTableType $tableType)
	{
		$table = new Table($this->container, $this->entityManager, $this->request, $this->router);
		
		return $table->create($tableType);
	}
}

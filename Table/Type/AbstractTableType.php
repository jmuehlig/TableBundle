<?php

namespace JGM\TableBundle\Table\Type;

use Doctrine\ORM\EntityManager;
use JGM\TableBundle\Table\DataSource\DataSourceInterface;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\TableBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * The abstract table type which user defined table types based on.
 * User defined table types have to implement the abstract methods
 * `buildTable`, `getName` and `setDefaultOptions`.
 * Further they can implement the methos `buildQuery`, `refineQuery` 
 * and `getRowAttributes`.
 * 
 * The table type injects the container and the entity manager.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
abstract class AbstractTableType
{
	/**
	 * Container
	 * 
	 * @var ContainerInterface 
	 */
	protected $container;
	
	/**
	 * EntityManager.
	 * 
	 * @var EntityManager 
	 */
	protected $entityManager;
	
	public final function getContainer()
	{
		return $this->container;
	}

	public final function getEntityManager()
	{
		return $this->entityManager;
	}

	public final function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public final function setEntityManager(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	/**
	 * Generates the attributes for a row at
	 * the given index.
	 * 
	 * @param Row $row Row.
	 * @return array Array with attributes (e.g. class)
	 */
	public function getRowAttributes(Row $row)
	{
		return array();
	}
	
	/**
	 * Returns the data source, the table gets the data from.
	 * 
	 * @return DataSourceInterface
	 */
	public abstract function getDataSource(ContainerInterface $container);

	public abstract function buildTable(TableBuilder $builder);
	
	/**
	 * @return string Name of the table type.
	 */
	public abstract function getName();
	
	/**
	 * Sets the default options for the table type.
	 * 
	 * @param OptionsResolverInterface $resolver
	 */
	public abstract function setDefaultOptions(OptionsResolverInterface $resolver);
}

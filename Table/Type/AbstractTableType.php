<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Type;

use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\TableBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * The abstract table type which user defined table types based on.
 * User defined table types have to implement the abstract methods
 * `buildTable`, `getName` and `configureOptions`.
 * Further they can implement optionally the method `getRowAttributes`.
 * 
 * The table type injects the container and the entity manager.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
abstract class AbstractTableType implements TableTypeInterface
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
	
	/**
	 * {@inheritdoc}
	 */
	public final function getContainer()
	{
		return $this->container;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function getEntityManager()
	{
		return $this->entityManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function setEntityManager(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getRowAttributes(Row $row)
	{
		return array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildTable(TableBuilder $builder)
	{
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		if($resolver instanceof OptionsResolver === false)
		{
			throw new InvalidArgumentException(
				"The resolver has to be an instance of 'Symfony\Component\OptionsResolver\OptionsResolver'."
			);
		}
		
		$this->configureOptions($resolver);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
	}
}

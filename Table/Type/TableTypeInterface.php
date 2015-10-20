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
use JGM\TableBundle\Table\DataSource\DataSourceInterface;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\TableBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Interface of the table type.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.1
 */
interface TableTypeInterface extends ContainerAwareInterface
{
	/**
	 * @return ContainerInterface
	 */
	public function getContainer();
	
	/**
	 * @return EntityManager
	 */
	public function getEntityManager();
	
	/**
	 * Set the entity manager.
	 * 
	 * @param EntityManager	$entityManager	Doctrines entity manager.
	 */
	public function setEntityManager(EntityManager $entityManager);
	
	/**
	 * Returns an array of attributes for the <tr> tag
	 * of a specific row, identified by the given $row object.
	 * 
	 * @param Row $row	Instance of a row.
	 * @return array
	 */
	public function getRowAttributes(Row $row);
	
	/**
	 * Returns an instance of data source, 
	 * the table will get the data from.
	 * 
	 * @param ContainerInterface $container	Instance of the container.
	 * @return DataSourceInterface
	 */
	public function getDataSource(ContainerInterface $container);
	
	/**
	 * Builds the table, by adding some columns to the builder.
	 * 
	 * @param TableBuilder $builder	Instance of the table builder.
	 */
	public function buildTable(TableBuilder $builder);
	
	/**
	 * Returns the name of the table (type).
	 * 
	 * @return string
	 */
	public function getName();
	
	/**
	 * Sets the default options for the table type.
	 * 
	 * @param OptionsResolverInterface $resolver
	 * @deprecated since version	1.1, to be removed in 1.2.
	 *								Use the method `configureOptions` instead.
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver);
	
	/**
	 * Configure the default options for the table type.
	 * 
	 * @since	1.1
	 * @param	OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver);
}

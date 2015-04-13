<?php

namespace JGM\TableBundle\Table\Type;

use JGM\TableBundle\Table\DataSource\EntityDataSource;
use JGM\TableBundle\Table\TableBuilder;
use JGM\TableBundle\Table\TableException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Table type for creating anonymous tables at the
 * controller.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class AnonymousTableType extends AbstractTableType
{
	/**
	 * @var string
	 */
	protected $entity;
	
	/**
	 * @var callable
	 */
	protected $buildCallback;
	
	/**
	 * @var string
	 */
	protected $name;
	
	public function __construct($entity, $buildCallback, $name = 'table')
	{
		if(!is_callable($buildCallback))
		{
			TableException::isNoCallback();
		}
		
		$this->entity = $entity;
		$this->buildCallback = $buildCallback;
		$this->name = $name;
	}
	
	public function buildTable(TableBuilder $builder)
	{
		call_user_func($this->buildCallback, $builder);
	}

	public function getDataSource(ContainerInterface $container)
	{
		return new EntityDataSource($this->entity);
	}

	public function getName()
	{
		return $this->name;
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		
	}
}

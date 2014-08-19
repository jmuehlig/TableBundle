<?php

namespace PZAD\TableBundle\Table\Column;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Abstract column, helpfull for the most options.
 * 
 * If you want to implement your own column,
 * you can - but don't have to - extend these
 * abstract column.
 *
 * @author Jan Mühlig
 * @since 1.0.0
 */
abstract class AbstractColumn implements ColumnInterface
{
	/**
	 * Resolver for the options.
	 * 
	 * @var OptionsResolver
	 */
	protected $optionsResolver;
	
	/**
	 * Resolved options.
	 * 
	 * @var array
	 */
	protected $options;
	
	/**
	 * @var string
	 */
	protected $name;
	
	function __construct()
	{
		$this->optionsResolver = new OptionsResolver();
		$this->setDefaultOptions($this->optionsResolver);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getAttributes()
	{
		return $this->options['attr'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getHeadAttributes()
	{
		return $this->options['head_attr'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLabel()
	{
		return $this->options['label'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSortable()
	{
		return $this->options['sortable'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setOptions(array $options)
	{
		$this->options = $this->optionsResolver->resolve($options);
	}
	
	protected function setDefaultOptions(OptionsResolverInterface $optionsResolver)
	{
		$optionsResolver->setDefaults(array(
			'attr' => array(),
			'head_attr' => array(),
			'sortable' => false,
			'label' => ''
		));
	}
}

# Create a simple table
Creating tables with this bundle is very easy.

## 1) Create the type
First you have to define a type, comparable to the form type definition.
You have to build the table, set the name and data source.
Optional you can define options and attribute setter for rows, depending on the row itself.

Here is a simple table type:
```php
<?php
// src/YourBundle/Table/Type/SimpleTableType.php

use PZAD\TableBundle\Table\DataSource\DataSourceInterface;
use PZAD\TableBundle\Table\Row\Row;
use PZAD\TableBundle\Table\TableBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SimpleTableType extends AbstractType
{
	public function buildTable(TableBuilder $builder)
	{
		// ... (see chapter 'Build the table')
	}
	
	public function getName()
	{
		// return 'simple_table';
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		// ... (see chapter 'Table type options')
	}
	
	public function getDataSource(ContainerInterface $container)
	{
		// (see chapter 'Data of your table')
		return new EntityDataSource('YourBundle:Entity');
	}
	
	public function getRowAttributes(Row $row)
	{
		// (see chapter 'Row attributes')
		if($row->getCount() % 2 === 0)
		{
			return array('class' => 'row-gray');
		}
		else
		{
			return array('class' => 'row-white');
		}
	}
}

## Build the table

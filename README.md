TableBundle
===========
The [JGM TableBundle](http://tablebundle.org) is a bundle for the Symfony Framework (Symfony2 or higher), which is used to build data tables in PHP and render them easily with twig.

## Why should I use this Bundle?
Creating tables for data is a boring and cumbersome work.
You have to care about rendering, paginating, order and filter the given data.
Sometimes, you want to reuse your tables, too.

Using the TableBundle will be the answer to your problems.
It provides building tables including dynamic columns for displaying data from different sources (like entities from database or arrays).
Additional the TableBundle supports mechanisms for dynamic filters, pagination and order.
You don't have to care about the implementation, it's all automatic.

A unique feature is the automatic join, which allows you to access and also filter for joined columns of the given data (and their joined columns, and their...).

## Installation

### Using composer
1. Add the bundle to your `composer.json` by executing the command `composer require jgm/tablebundle` or adding the line `"jgm/tablebundle": "1.3.*"` to the `required`-section.

2. Add the Bundle to your Kernel (`app/AppKernel.php`):

```
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...,
            new JGM\TableBundle\JGMTableBundle()
        );
        // ...
    }
    // ...
}
```

### Dependencies
* php: >=5.3.9
* symfony/symfony: >=2.5
* symfony/config: >=2.5
* symfony/yaml: >=2.5
* symfony/security: >=2.5
* symfony/templating: >=2.5
* symfony/http-foundation: >=2.5
* symfony/http-kernel: >=2.5
* symfony/dependency-injection: >=2.5
* doctrine/common: >=2.3

## Basic usage
### Step 1: Create a table type
```
// src/YourBundle/Table/Type/StudentTableType.php
class StudentTableType extends JGM\TableBundle\Table\Type\AbstractTableType
{
    public function buildTable(TableBuilder $builder) 
    {
		$builder
			->add('text', 'name', ['label' => 'Name'])
			->add('number', 'term', ['label' => 'Term'])
			->add('date', 'birthday', ['label' => 'Day of birth']);
    }

    public function getDataSource(ContainerInterface $container)
    {
      return new EntityDataSource('YourBundle:Student');
    }

    public function getName()
    {
        return 'student_table';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
		$optionsResolver->setDefaults(array(
			'attr' => array('width' => '600px', 'class' => 'table-css'),
			'empty_value' => 'There is no student...'
		));
    }
}
```

### Step 2: Instantiate the table
```
// src/YourBundle/Controller/StudentController.php
<?
class StudentController extends Symfony\Bundle\FrameworkBundle\Controller\Controller
{
    public function showAction()
    {
        $table = $this->get('jgm.table')->createTable(new StudentTableType());
		return array('studentTable' => $table->createView());
    }
}
```

### Step 3: Render the table at twig template
```
<h1>Students</h1>
{{ table(studentTable) }}
```

## Documentation
For more information take a look at the [Documentation Website](http://tablebundle.org).

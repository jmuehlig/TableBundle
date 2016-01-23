<?php

namespace JGM\TableBundle\DependencyInjection;

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Extension for the TableBundle.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class JGMTableExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
		$this->loadServices($container);
		$this->loadConfig($configs, $container);
    }
	
	private function loadServices(ContainerBuilder $container)
	{
		$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
	}
	
	private function loadConfig(array $configs, ContainerBuilder $container)
	{		
		$configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
		
		// Columns.
		$container->setParameter('jgm_table.columns', array_merge(
			$config['columns'], 
			$configuration->getDefaultColumns()
		));
		
		// Filters.
		$container->setParameter('jgm_table.filters', array_merge(
				$config['filters'], 
				$configuration->getDefaultFilters()
		));
		
		// Filter expression.
		$container->setParameter('jgm_table.filter_expressions', array_merge_recursive(
			$config['filter_expressions'], 
			$configuration->getDefaultFilterExpressionManipulators()
		));
		
		// Default table options.
		$container->setParameter('jgm_table.default_options', $config['default_options']);
		
		// Default filter options.
		$container->setParameter('jgm_table.filter_default_options', $config['filter_default_options']);
		
		// Default pagination options.
		$container->setParameter('jgm_table.pagination_default_options', $config['pagination_default_options']);
		
		// Default order options.
		$container->setParameter('jgm_table.order_default_options', $config['order_default_options']);
	}
	
	public function getAlias()
	{
		return 'jgm_table';
	}
}

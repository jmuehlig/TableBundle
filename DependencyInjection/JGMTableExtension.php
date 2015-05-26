<?php

namespace JGM\TableBundle\DependencyInjection;

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
		
		$container->setParameter('jgm_table.columns', array_merge($config['columns'], $configuration->getDefaultColumns()));
		$container->setParameter('jgm_table.filters', array_merge($config['filters'], $configuration->getDefaultFilters()));
		$container->setParameter('jgm_table.filter_expressions', array_merge_recursive(
			$config['filter_expressions'], 
			$configuration->getDefaultFilterExpressionManipulators()
		));
	}
	
	public function getAlias()
	{
		return 'jgm_table';
	}
}

<?php

namespace JGM\TableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for the TableBundle.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(); 
		$rootNode = $treeBuilder->root('jgm_table');

		$rootNode
			->children()
				->arrayNode('columns')->prototype('scalar')->end()->end()
				->arrayNode('filters')->prototype('scalar')->end()->end()
			->end();

        return $treeBuilder;
    }
	
	public function getDefaultColumns() 
	{
		return array(
			'array'		=> 'JGM\TableBundle\Table\Column\ArrayColumn',
			'boolean'	=> 'JGM\TableBundle\Table\Column\BooleanColumn',
			'content'	=> 'JGM\TableBundle\Table\Column\ContentColumn',
			'counter'	=> 'JGM\TableBundle\Table\Column\CounterColumn',
			'date'		=> 'JGM\TableBundle\Table\Column\DateColumn',
			'entity'	=> 'JGM\TableBundle\Table\Column\EntityColumn',
			'number'	=> 'JGM\TableBundle\Table\Column\NumberColumn',
			'text'		=> 'JGM\TableBundle\Table\Column\TextColumn',
			'url'		=> 'JGM\TableBundle\Table\Column\UrlColumn'
		);
	}
	
	public function getDefaultFilters()
	{
		return array(
			'text'		=> 'JGM\TableBundle\Table\Filter\TextFilter',
			'entity'	=> 'JGM\TableBundle\Table\Filter\EntityFilter',
			'boolean'	=> 'JGM\TableBundle\Table\Filter\BooleanFilter',
			'valued'	=> 'JGM\TableBundle\Table\Filter\ValuedFilter',
			'date'		=> 'JGM\TableBundle\Table\Filter\DateFilter'
		);
	}
			
}

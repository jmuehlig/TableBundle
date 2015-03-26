<?php

namespace JGM\TableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
/**
 * Configuration for the TableBundle.
 * 
 * @author Jan Mühlig <mail@janmuehlig.de>
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
			'content'	=> 'JGM\TableBundle\Table\Column\ContentColumn',
			'entity'	=> 'JGM\TableBundle\Table\Column\EntityColumn',
			'date'		=> 'JGM\TableBundle\Table\Column\DateColumn',
			'text'		=> 'JGM\TableBundle\Table\Column\TextColumn',
			'number'	=> 'JGM\TableBundle\Table\Column\NumberColumn',
			'counter'	=> 'JGM\TableBundle\Table\Column\CounterColumn',
			'boolean'	=> 'JGM\TableBundle\Table\Column\BooleanColumn'
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

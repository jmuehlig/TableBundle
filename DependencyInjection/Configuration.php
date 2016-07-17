<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
				
				->arrayNode('columns')
					->prototype('scalar')->end()
				->end()
				
				->arrayNode('filters')
					->prototype('scalar')->end()
				->end()
				
				->arrayNode('filter_expressions')
					->prototype('array')
						->prototype('scalar')->end()
					->end()
				->end()
				
				->arrayNode('default_options')
					->children()
						->scalarNode('template')
							->defaultValue('JGMTableBundle:Blocks:table.html.twig')
						->end()
						->scalarNode('empty_value')
							->defaultValue('No data found.')
						->end()
						->arrayNode('attr')
							->prototype('scalar')->end()
						->end()
						->arrayNode('head_attr')
							->prototype('scalar')->end()
						->end()
						->booleanNode('hide_empty_columns')
							->defaultFalse()
						->end()
						->booleanNode('use_filter')
							->defaultTrue()
						->end()
						->booleanNode('use_pagination')
							->defaultTrue()
						->end()
						->booleanNode('use_order')
							->defaultTrue()
						->end()
						->booleanNode('load_data')
							->defaultTrue()
						->end()
					->end()
					->addDefaultsIfNotSet()
				->end()
				
				->arrayNode('filter_default_options')
					->children()
						->scalarNode('template')
							->defaultValue('JGMTableBundle:Blocks:filter.html.twig')
						->end()
						->scalarNode('submit_label')
							->defaultValue('Ok')
						->end()
						->arrayNode('submit_attr')
							->prototype('scalar')->end()
						->end()
						->scalarNode('reset_label')
							->defaultValue('Reset')
						->end()
						->arrayNode('reset_attr')
							->prototype('scalar')->end()
						->end()
					->end()
					->addDefaultsIfNotSet()
				->end()
				
				->arrayNode('pagination_default_options')
					->children()
						->scalarNode('template')
							->defaultValue('JGMTableBundle:Blocks:pagination.html.twig')
						->end()
						->scalarNode('param')
							->defaultValue('page')
						->end()
						->integerNode('rows_per_page')
							->defaultValue(20)
						->end()
						->booleanNode('show_empty')
							->defaultTrue()
						->end()
						->scalarNode('ul_class')
							->defaultValue('pagination')
						->end()
						->scalarNode('li_class')
							->defaultValue(null)
						->end()
						->scalarNode('li_class_active')
							->defaultValue('active')
						->end()
						->scalarNode('li_class_disabled')
							->defaultValue('disabled')
						->end()
						->scalarNode('prev_label')
							->defaultValue('&laquo')
						->end()
						->scalarNode('next_label')
							->defaultValue('&raquo')
						->end()
						->integerNode('max_pages')
							->defaultValue(null)
						->end()
				
						->arrayNode('option_values')
							->prototype('integer')->end()
						->end()
						->arrayNode('option_attr')
							->prototype('scalar')->end()
						->end()
						->scalarNode('option_label')
							->defaultValue('Entries per Page')
						->end()
						->arrayNode('option_label_attr')
							->prototype('scalar')->end()
						->end()
						->scalarNode('option_submit_label')
							->defaultValue('Submit')
						->end()
						->arrayNode('option_submit_attr')
							->prototype('scalar')->end()
						->end()
					->end()
					->addDefaultsIfNotSet()
				->end()
				
				->arrayNode('order_default_options')
					->children()
						->scalarNode('template')
							->defaultValue('JGMTableBundle:Blocks:order.html.twig')
						->end()
						->scalarNode('param_direction')
							->defaultValue('direction')
						->end()
						->scalarNode('param_column')
							->defaultValue('column')
						->end()
						->scalarNode('empty_direction')
							->defaultValue('desc')
						->end()
						->scalarNode('empty_column')
							->defaultValue(null)
						->end()
						->scalarNode('class_asc')
							->defaultNull()
						->end()
						->scalarNode('class_desc')
							->defaultNull()
						->end()
						->scalarNode('html_asc')
							->defaultValue('&uarr;')
						->end()
						->scalarNode('html_desc')
							->defaultValue('&darr;')
						->end()
					->end()
					->addDefaultsIfNotSet()
				->end()
				
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
			'twig'		=> 'JGM\TableBundle\Table\Column\TwigColumn',
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
	
	public function getDefaultFilterExpressionManipulators()
	{
		return array(
			'doctrine'	=> array(
				'JGM\TableBundle\Table\Filter\ExpressionManipulator\DoctrineCountExpressionManipulator',
				'JGM\TableBundle\Table\Filter\ExpressionManipulator\DoctrineSumExpressionManipulator',
				'JGM\TableBundle\Table\Filter\ExpressionManipulator\DoctrineMinExpressionManipulator',
				'JGM\TableBundle\Table\Filter\ExpressionManipulator\DoctrineMaxExpressionManipulator',
				'JGM\TableBundle\Table\Filter\ExpressionManipulator\DoctrineAvgExpressionManipulator'
			)
		);
	}
}

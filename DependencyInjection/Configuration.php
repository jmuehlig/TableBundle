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

use JGM\TableBundle\Table\Filter\OptionsResolver\FilterOptions;
use JGM\TableBundle\Table\OptionsResolver\TableOptions;
use JGM\TableBundle\Table\Order\OptionsResolver\OrderOptions;
use JGM\TableBundle\Table\Pagination\OptionsResolver\PaginationOptions;
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
						->scalarNode(TableOptions::TEMPLATE)
							->defaultValue('JGMTableBundle:Blocks:table.html.twig')
						->end()
						->scalarNode(TableOptions::EMPTY_VALUE)
							->defaultValue('No data found.')
						->end()
						->arrayNode(TableOptions::ATTRIBUTES)
							->prototype('scalar')->end()
						->end()
						->arrayNode(TableOptions::HEAD_ATTRIBUTES)
							->prototype('scalar')->end()
						->end()
						->booleanNode(TableOptions::HIDE_EMPTY_COLUMNS)
							->defaultFalse()
						->end()
						->booleanNode(TableOptions::USE_FILTER)
							->defaultTrue()
						->end()
						->booleanNode(TableOptions::USE_PAGINATION)
							->defaultTrue()
						->end()
						->booleanNode(TableOptions::USE_ORDER)
							->defaultTrue()
						->end()
						->booleanNode(TableOptions::USE_SELECTION)
							->defaultTrue()
						->end()
						->booleanNode(TableOptions::LOAD_DATA)
							->defaultTrue()
						->end()
					->end()
					->addDefaultsIfNotSet()
				->end()
				
				->arrayNode('filter_default_options')
					->children()
						->scalarNode(FilterOptions::TEMPLATE)
							->defaultValue('JGMTableBundle:Blocks:filter.html.twig')
						->end()
						->scalarNode(FilterOptions::SUBMIT_LABEL)
							->defaultValue('submit')
						->end()
						->arrayNode(FilterOptions::SUBMIT_ATTRIBUTES)
							->prototype('scalar')->end()
						->end()
						->scalarNode(FilterOptions::RESET_LABEL)
							->defaultValue('reset')
						->end()
						->arrayNode(FilterOptions::RESET_ATTRIBUTES)
							->prototype('scalar')->end()
						->end()
					->end()
					->addDefaultsIfNotSet()
				->end()
				
				->arrayNode('pagination_default_options')
					->children()
						->scalarNode(PaginationOptions::TEMPLATE)
							->defaultValue('JGMTableBundle:Blocks:pagination.html.twig')
						->end()
						->scalarNode(PaginationOptions::PARAM)
							->defaultValue('page')
						->end()
						->integerNode(PaginationOptions::ROWS_PER_PAGE)
							->defaultValue(20)
						->end()
						->booleanNode(PaginationOptions::SHOW_EMPTY)
							->defaultTrue()
						->end()
						->scalarNode(PaginationOptions::UL_CLASS)
							->defaultValue('pagination')
						->end()
						->scalarNode(PaginationOptions::LI_CLASS)
							->defaultValue(null)
						->end()
						->scalarNode(PaginationOptions::LI_CLASS_ACTIVE)
							->defaultValue('active')
						->end()
						->scalarNode(PaginationOptions::LI_CLASS_DISABLED)
							->defaultValue('disabled')
						->end()
						->scalarNode(PaginationOptions::PREV_LABEL)
							->defaultValue('&laquo')
						->end()
						->scalarNode(PaginationOptions::NEXT_LABEL)
							->defaultValue('&raquo')
						->end()
						->integerNode(PaginationOptions::MAX_PAGES)
							->defaultValue(null)
						->end()
				
						->arrayNode(PaginationOptions::OPTION_VALUES)
							->prototype('integer')->end()
						->end()
						->arrayNode(PaginationOptions::OPTION_ATTRIBUTES)
							->prototype('scalar')->end()
						->end()
						->scalarNode(PaginationOptions::OPTION_LABEL)
							->defaultValue('Entries per Page')
						->end()
						->arrayNode(PaginationOptions::OPTION_LABEL_ATTRIBUTES)
							->prototype('scalar')->end()
						->end()
						->scalarNode(PaginationOptions::OPTION_SUBMIT_LABEL)
							->defaultValue('Submit')
						->end()
						->arrayNode(PaginationOptions::OPTION_SUBMIT_ATTRIBUTES)
							->prototype('scalar')->end()
						->end()
					->end()
					->addDefaultsIfNotSet()
				->end()
				
				->arrayNode('order_default_options')
					->children()
						->scalarNode(OrderOptions::TEMPLATE)
							->defaultValue('JGMTableBundle:Blocks:order.html.twig')
						->end()
						->scalarNode(OrderOptions::PARAM_DIRECTION)
							->defaultValue('direction')
						->end()
						->scalarNode(OrderOptions::PARAM_COLUMN)
							->defaultValue('column')
						->end()
						->scalarNode(OrderOptions::EMPTY_DIRECTION)
							->defaultValue('desc')
						->end()
						->scalarNode(OrderOptions::EMPTY_COLUMN)
							->defaultValue(null)
						->end()
						->scalarNode(OrderOptions::HTML_ASC)
							->defaultValue('&uarr;')
						->end()
						->scalarNode(OrderOptions::HTML_DESC)
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
			'url'		=> 'JGM\TableBundle\Table\Column\UrlColumn',
			'selection'	=> 'JGM\TableBundle\Table\Selection\Column\SelectionColumn'
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

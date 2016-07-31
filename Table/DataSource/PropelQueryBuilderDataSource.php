<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\DataSource;

use JGM\TableBundle\Table\DataSource\ContainerInterace;
use JGM\TableBundle\Table\DataSource\DataSourceInterface;
use JGM\TableBundle\Table\Model\SortableOptionsContainer;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use JGM\TableBundle\Table\PropelQueryBuilder\PropelQueryBuilder;
use JGM\TableBundle\Table\TableException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DataSource for the Propel ORM.
 * 
 * @author	Daniel Purrucker <daniel.purrucker@nordakademie.de>
 * @since	1.3
 */
class PropelQueryBuilderDataSource implements DataSourceInterface
{
    /** @var \ModelCriteria */
    private $query;

    public function __construct(\ModelCriteria $query)
    {
        $this->query = $query;
    }

    /**
     * Creates an array with data for the table.
     *
     * @param ContainerInterace $container Symfonys container.
     * @param array $columns Array with all columns of the table.
     * @param array|null $filters Array with all filters of the table, null if filters are not supported.
     * @param Pagination|null $pagination Container with all pagination options, null if pagination is not supported.
     * @param SortableOptionsContainer|null $order Container with all sorting options, null if sorting is not supported.
     *
     * @return array
     */
    public function getData(
        ContainerInterface $container,
        array $columns,
        array $filters = null,
        Pagination $pagination = null,
        Order $order = null
    )
    {
        if($this->query === null)
        {
            TableException::noQuery($container->get('jgm.table_context')->getCurrentTableName());
        }
        $query = clone $this->query;

        $query = $this->applyFilters($query, $filters);

        $query = $this->applyOrder($order, $query);

        if($pagination !== null)
        {
            /*
             * PropelModelPager starts with index = 1. So we have to add 1 to the current page to get the right page.
             */
            $propelPage = $query->paginate($pagination->getCurrentPage()+1,$pagination->getItemsPerRow());
            return $propelPage->getIterator();
        }

        return $query->find();
    }

    /**
     * Returns the number of items.
     *
     * @param ContainerInterace $container Symfonys container.
     * @param array $columns Array with all columns of the table.
     * @param array|null $filters Array with all filters of the table, null if filters are not supported.
     *
     * @return int
     */
    public function getCountItems(
        ContainerInterface $container,
        array $columns,
        array $filters = null
    )
    {
        $query = clone $this->query;
        $query = $this->applyFilters($query,$filters);
        $result = $query->find();
        return $result->count();
    }

    /**
     * Returns the name of the type.
     *
     * @return string
     */
    public function getType()
    {
        return 'propel';
    }

    /**
     * Applys the filters to the query
     *
     * @param \ModelCriteria $query	        The query object.
     * @param array $filters				Array with filters.
     * @return \ModelCriteria $query
     */
    protected function applyFilters(\ModelCriteria $query, array $filters = array())
    {
        $query = $query->distinct();
        $tableFilters = new PropelQueryBuilder($query->getModelName());

        foreach ($filters as $filter) {
            if(!$filter->isActive())
            {
                continue;
            }
            foreach ($filter->getColumns() as $column) {
                $tableFilters->addUsage($column,$filter->getValue(),$filter->getOperator());
            }
        }
        $query = $tableFilters->applyFilterOnQuery($query);
        return $query;
    }

    /**
     * @param Order $order
     * @param \ModelCriteria $query
     * @return mixed
     */
    protected function applyOrder(Order $order, $query)
    {
        if ($order !== null) {
            $useCount = 0;
            $column = $order->getCurrentColumnName();
            if(strpos($order->getCurrentColumnName(),'.') !== false) {
                $explodedOrder = explode('.',$order->getCurrentColumnName());
                for($i = 0; $i < sizeof($explodedOrder)-1;$i++) {
                    $use = 'use'.ucfirst($explodedOrder[$i]).'Query';
                    $query = $query->$use();
                    $useCount++;
                }
                $column = end($explodedOrder);
            }
            $query = $query->orderBy($column, strtoupper($order->getCurrentDirection()));
            for($i = 0; $i < $useCount; $i++) {
                $query = $query->endUse();
            }
        }
        return $query;
    }
}

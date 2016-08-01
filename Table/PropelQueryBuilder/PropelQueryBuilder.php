<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\PropelQueryBuilder;

/**
 * @author	Daniel Purrucker <daniel.purrucker@nordakademie.de>
 * @since	1.3
 */
class PropelQueryBuilder implements PropelQueryBuilderInterface
{
    private $table = '';
    private $filters = array();
    private $usages = array();

    public function __construct($tableName)
    {
        $this->table = $tableName;
    }

    /**
     * @return PropelQueryBuilderInterface[]
     */
    public function getUsages()
    {
        return $this->usages;
    }

    /**
     * Follows the catenation and creates usages (use..Query) for every step. Last column
     * in chain is a filter (filterBy..)
     * 
     * @param string|array $column
     * @param mixed $value
     * @param mixed $operator
     */
    public function addUsage($column, $value, $operator = null)
    {
        $explodedColumn = $this->checkForCatenations($column);
        if(count($explodedColumn) > 1) {
            $this->handleNewUsage($explodedColumn, $value, $operator);
        } else if(count($explodedColumn) == 1) {
            $this->addFilter($explodedColumn[0],$value,$operator);
        }
    }

    /**
     * @return PropelQueryFilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param string $string
     * @param mixed $value
     * @param mixed $operator
     */
    public function addFilter($string,$value,$operator = null)
    {

        $filter = new PropelQueryFilter($string,$value,$operator);
        $this->filters[] = $filter;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTable();
    }

    /**
     * @param \ModelCriteria $query
     * @return \ModelCriteria
     */
    public function applyFilterOnQuery(\ModelCriteria $query)
    {
        $query = $this->applyFilters($query);
        $query = $this->applyUsages($query);
        return $query;
    }

    /**
     * @param $column
     * @return array
     */
    private function checkForCatenations($column)
    {
        if (!is_array($column)) {
            $explodedColumn = explode('.', $column);
            return $explodedColumn;
        }
        $explodedColumn = $column;
        return $explodedColumn;
    }

    /**
     * @param $explodedColumn
     * @param $value
     * @param $operator
     */
    private function handleNewUsage($explodedColumn, $value, $operator)
    {
        $table = $explodedColumn[0];
        unset($explodedColumn[0]);
        $explodedColumn = array_values($explodedColumn);
        if (array_key_exists($table, $this->usages)) {
            $this->usages[$table]->addUsage($explodedColumn, $value, $operator);
        } else {
            $dto = new PropelQueryBuilder($table);
            $dto->addUsage($explodedColumn, $value, $operator);
            $this->usages[$table] = $dto;
        }
    }

    /**
     * @param \ModelCriteria $query
     * @return \ModelCriteria
     */
    private function applyFilters(\ModelCriteria $query)
    {
        foreach ($this->getFilters() as $filter) {
            $filterBy = 'filterBy' . ucfirst($filter->getName());
            $query = $query->$filterBy($filter->getValue(), $filter->getCriteria());
        }
        return $query;
    }

    /**
     * @param \ModelCriteria $query
     * @return \ModelCriteria
     */
    private function applyUsages(\ModelCriteria $query)
    {
        foreach ($this->getUsages() as $usage) {
            $useQuery = 'use' . ucfirst($usage->getTable()) . 'Query';
            $query = $query->$useQuery();
            $query = $usage->applyFilterOnQuery($query);
            $query = $query->endUse();
        }
        return $query;
    }
}

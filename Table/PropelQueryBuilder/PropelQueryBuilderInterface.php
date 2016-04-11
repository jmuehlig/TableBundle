<?php
/**
 * Created by PhpStorm.
 * User: Daniel Purrucker <daniel.purrucker@nordakademie.de>
 * Date: 04.04.16
 * Time: 15:28
 */
namespace JGM\TableBundle\Table\PropelQueryBuilder;

interface PropelQueryBuilderInterface
{
    /**
     * @return PropelQueryBuilderInterface[]
     */
    public function getUsages();

    /**
     * @param string|array $column
     * @param mixed $value
     * @param mixed $operator
     */
    public function addUsage($column, $value, $operator = null);

    /**
     * @return PropelQueryFilterInterface[]
     */
    public function getFilters();

    /**
     * @param string $string
     * @param mixed $value
     * @param mixed $operator
     */
    public function addFilter($string, $value, $operator = null);

    /**
     * @return string
     */
    public function getTable();

    /**
     * @param \ModelCriteria $query
     * @return \ModelCriteria
     */
    public function applyFilterOnQuery(\ModelCriteria $query);
}
<?php
/**
 * Created by PhpStorm.
 * User: Daniel Purrucker <daniel.purrucker@nordakademie.de>
 * Date: 31.03.16
 * Time: 15:45
 */

namespace JGM\TableBundle\Table\PropelQueryBuilder;


use JGM\TableBundle\Table\Filter\FilterOperator;

class PropelQueryFilter implements PropelQueryFilterInterface
{
    private $name = '';
    private $value = '';
    private $criteria;

    public function __construct($name, $value, $operator = null)
    {
        $this->name = $name;
        $this->value = $value;
        if($operator == null) {
            $operator = '-1';
        }
        switch ($operator) {
            case FilterOperator::LIKE:
                $this->criteria = \Criteria::LIKE;
                break;
            case FilterOperator::EQ:
                $this->criteria = \Criteria::EQUAL;
                break;
            case FilterOperator::GEQ:
                $this->criteria = \Criteria::GREATER_EQUAL;
                break;
            case FilterOperator::GT:
                $this->criteria = \Criteria::GREATER_THAN;
                break;
            case FilterOperator::LEQ:
                $this->criteria = \Criteria::LESS_EQUAL;
                break;
            case FilterOperator::LT:
                $this->criteria = \Criteria::LESS_THAN;
                break;
            case FilterOperator::NOT_EQ:
                $this->criteria = \Criteria::NOT_EQUAL;
                break;
            default:
                $this->criteria = \Criteria::LIKE;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $value = $this->value;
        if($this->criteria === \Criteria::LIKE || $this->criteria === \Criteria::NOT_LIKE) {
            $value = '%'.$value.'%';
        }
        return $value;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
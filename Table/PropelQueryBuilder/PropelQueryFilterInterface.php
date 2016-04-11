<?php
/**
 * Created by PhpStorm.
 * User: Daniel Purrucker <daniel.purrucker@nordakademie.de>
 * Date: 04.04.16
 * Time: 15:26
 */
namespace JGM\TableBundle\Table\PropelQueryBuilder;

interface PropelQueryFilterInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getCriteria();

    /**
     * @return string
     */
    public function getValue();
}
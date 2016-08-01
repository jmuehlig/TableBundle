<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Test;

use JGM\TableBundle\Table\Filter\FilterOperator;
use JGM\TableBundle\Table\PropelQueryBuilder\PropelQueryBuilder;

/**
 * @author	Daniel Purrucker <daniel.purrucker@nordakademie.de>
 * @since	1.3
 */
class PropelTableFilterDtoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function setJustOneFilter()
    {
        $dto = new PropelQueryBuilder('table');

        $dto->addFilter('vorname','Daniel');

        $filters = $dto->getFilters();
        $this->assertEquals(1,count($filters));
        $this->assertEquals('vorname',$filters[0]->getName());
        $this->assertEquals('%Daniel%',$filters[0]->getValue());
    }

    /**
     * @test
     */
    public function setMultipleFilters()
    {
        $dto = new PropelQueryBuilder('table');

        $dto->addFilter('vorname','Daniel');
        $dto->addFilter('nachname','Purrucker');
        $dto->addFilter('emailadresse','@gmx.de');

        $filters = $dto->getFilters();
        $this->assertEquals(3,count($filters));
        $this->assertEquals('vorname',$filters[0]->getName());
        $this->assertEquals('%Daniel%',$filters[0]->getValue());
        $this->assertEquals('nachname',$filters[1]->getName());
        $this->assertEquals('%Purrucker%',$filters[1]->getValue());
        $this->assertEquals('emailadresse',$filters[2]->getName());
        $this->assertEquals('%@gmx.de%',$filters[2]->getValue());
    }

    /**
     * @test
     */
    public function setOneUsageWithOneFilter()
    {
        $dto = new PropelQueryBuilder('table');

        $dto->addUsage('zenturie.bezeichnung','I09b');

        $usages = $dto->getUsages();
        $this->assertEquals(1,count($usages));
        $this->assertEquals('zenturie',$usages['zenturie']->getTable());
        $filters = $usages['zenturie']->getFilters();
        $this->assertEquals(1,count($filters));
        $this->assertEquals('bezeichnung',$filters[0]->getName());
        $this->assertEquals('%I09b%',$filters[0]->getValue());
    }

    /**
     * @test
     */
    public function setMultipleUsages()
    {
        $dto = new PropelQueryBuilder('table');

        $dto->addUsage('zenturie.bezeichnung','I09b');
        $dto->addUsage('emailadressen.adresse','@gmx.de');
        $dto->addUsage('zenturie.jahrgang','2009');

        $usages = $dto->getUsages();
        $this->assertEquals(2,count($usages));
        $this->assertEquals('zenturie',$usages['zenturie']->getTable());
        $this->assertEquals('emailadressen',$usages['emailadressen']->getTable());
        $filters = $usages['zenturie']->getFilters();
        $this->assertEquals(2,count($filters),'Die Tabelle "zenturie" hat nicht die erwartete Anzahl an Filtern gesetzt.');
        $this->assertEquals('bezeichnung',$filters[0]->getName());
        $this->assertEquals('%I09b%',$filters[0]->getValue());
        $this->assertEquals('jahrgang',$filters[1]->getName());
        $this->assertEquals('%2009%',$filters[1]->getValue());
        $filters = $usages['emailadressen']->getFilters();
        $this->assertEquals(1,count($filters));
        $this->assertEquals('adresse',$filters[0]->getName());
        $this->assertEquals('%@gmx.de%',$filters[0]->getValue());
    }

    /**
     * @test
     */
    public function addUsageCalledButAddFilterShouldCalled()
    {
        $dto = new PropelQueryBuilder('table');

        $dto->addUsage('zenturie','I09b');

        $usages = $dto->getUsages();
        $this->assertEmpty($usages);
        $filters = $dto->getFilters();
        $this->assertEquals(1,count($filters));
        $this->assertEquals('zenturie',$filters[0]->getName());
        $this->assertEquals('%I09b%',$filters[0]->getValue());
    }

    /**
     * @test
     */
    public function addConcatUsages()
    {
        $dto = new PropelQueryBuilder('table');

        $dto->addUsage('student.zenturie.bezeichnung','I09b');

        $usages = $dto->getUsages();
        $this->assertEquals(1,count($usages));
        $filters = $dto->getFilters();
        $this->assertEmpty($filters);
        $usages2 = $usages['student']->getUsages();
        $this->assertEquals(1,count($usages2));
        $filters = $usages['student']->getFilters();
        $this->assertEquals(0,count($filters));

        $usages3 = $usages2['zenturie']->getUsages();
        $this->assertEquals(0,count($usages3));
        $filters = $usages2['zenturie']->getFilters();
        $this->assertEquals(1,count($filters));
        $this->assertEquals('bezeichnung',$filters[0]->getName());
        $this->assertEquals('%I09b%',$filters[0]->getValue());
    }

    /**
     * @test
     */
    public function chooseCorrectCriteria()
    {
        $this->assertFilterOperatorIsCriteria(FilterOperator::EQ,\Criteria::EQUAL);
        $this->assertFilterOperatorIsCriteria(FilterOperator::GEQ,\Criteria::GREATER_EQUAL);
        $this->assertFilterOperatorIsCriteria(FilterOperator::GT,\Criteria::GREATER_THAN);
        $this->assertFilterOperatorIsCriteria(FilterOperator::LEQ,\Criteria::LESS_EQUAL);
        $this->assertFilterOperatorIsCriteria(FilterOperator::LT,\Criteria::LESS_THAN);
        $this->assertFilterOperatorIsCriteria(FilterOperator::NOT_EQ,\Criteria::NOT_EQUAL);
        $this->assertFilterOperatorIsCriteria(FilterOperator::LIKE,\Criteria::LIKE);
        $this->assertFilterOperatorIsCriteria(null,\Criteria::LIKE);
    }

    /**
     * @param $operator
     * @param $criteria
     */
    private function assertFilterOperatorIsCriteria($operator,$criteria)
    {
        $dto = new PropelQueryBuilder('table');

        $dto->addFilter('field','value',$operator);

        $filters = $dto->getFilters();
        $this->assertEquals(1,count($filters));
        $this->assertEquals($criteria,$filters[0]->getCriteria());
    }
}

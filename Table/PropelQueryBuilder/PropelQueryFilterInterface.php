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
<?php

namespace JGM\TableBundle\Table\Selection\Button\Action;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 *
 * @author Jan
 */
interface ButtonActionInterface extends ContainerAwareInterface
{
	public function execute(array $rows);
}

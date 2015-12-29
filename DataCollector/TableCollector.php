<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JGM\TableBundle\DataCollector;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Description of TableCollector
 *
 * @author Jan
 */
class TableCollector extends DataCollector
{
	public function collect(Request $request, Response $response, Exception $exception = null)
	{
	}

	public function getName()
	{
		return 'jgm.table_collector';
	}
}

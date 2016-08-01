<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\DependencyInjection\Listener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Listener for listening response events.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class ResponseListener
{
	public function onKernelResponse(FilterResponseEvent $event)
	{
		if(!$event->isMasterRequest()) 
		{
			return;
		}
		
		$request = $event->getRequest();
		$response = $event->getResponse();
		
		if($request->isMethod('post') && $request->request->has("table_option_values_table_name"))
		{
			$tableName = $request->get("table_option_values_table_name");
			
			$userItemsPerPage = (int) $request->get(sprintf("%s_option_values", $tableName));
			
			$cookie = new Cookie(sprintf("%s_items_per_page", $tableName), $userItemsPerPage);
			
			$response->headers->setCookie($cookie);
		}
	}
}

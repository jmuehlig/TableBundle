<?php

use JGM\TableBundle\Table\Model\SortableOptionsContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

namespace JGM\TableBundle\Table;

/**
 * Helper for urls, used in the table bundle.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class UrlHelper
{
	/**
	 * @var Request
	 */
	private $request;
	
	/**
	 * @var Router
	 */
	private $router;
	
	public function __construct(Request $request, RouterInterface $router)
	{
		$this->request = $request;
		$this->router = $router;
	}
	
	/**
	 * Generates an url.
	 * Replaces the parameters of the given array or adds them,
	 * if they are not used, yet.
	 * 
	 * @param	array|null $replacedParameters	Parameters to replace in the url.
	 * @return	string							New generated url.
	 */
	public function getUrlForParameters(array $replacedParameters = array(), $anchor = null)
	{
		$routeName = $this->request->get('_route');
		$currentRouteParams = array_merge(
			$this->request->attributes->get('_route_params'),
			$this->request->query->all()
		);

		foreach($replacedParameters as $name => $value)
		{
			$currentRouteParams[$name] = $value;
		}
		
		// Cleaning up the parameters.
		foreach($currentRouteParams as $key => $value)
		{
			if($value === null || trim($value) === '')
			{
				unset($currentRouteParams[$key]);
			}
		}
		
		$url = $this->router->generate($routeName, $currentRouteParams);
		
		// Add the anchor, if given.
		if($anchor != null)
		{
			$url .= sprintf("#%s", $anchor);
		}
		
		return $url;
	}
}

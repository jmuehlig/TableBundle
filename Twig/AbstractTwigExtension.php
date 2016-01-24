<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Twig;

use JGM\TableBundle\DependencyInjection\Service\TableStopwatchService;
use JGM\TableBundle\Table\Utils\UrlHelper;
use Twig_Environment;
use Twig_Extension;

/**
 * Base class for any twig extension, provided by this bundle.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
abstract class AbstractTwigExtension extends Twig_Extension
{
	/**
	 * Helper for creating urls.
	 * 
	 * @var UrlHelper
	 */
	protected $urlHelper;
	
	/**
	 * Stopwatch for profiling
	 * the table bundle in debug
	 * mode.
	 * 
	 * @var TableStopwatchService
	 */
	protected $stopwatchService;
	
	/**
	 * Map of loaded templates, with name
	 * of the template as key and the template
	 * as value.
	 * 
	 * @var array
	 */
	protected $templates;

	public function __construct(UrlHelper $urlHelper, TableStopwatchService $stopwatchService)
	{
		$this->urlHelper = $urlHelper;
		$this->stopwatchService = $stopwatchService;
		$this->templates = array();
	}
	
	/**
	 * Loads a template, specified by the given name.
	 * Loaded templates will be cached.
	 * 
	 * @param Twig_Environment $environment	Twig environment.
	 * @param string $templateName			Name of the template.
	 * 
	 * @return \Twig_Template	Loaded template.
	 */
	protected function loadTemplate(Twig_Environment $environment, $templateName) 
	{
		if(array_key_exists($templateName, $this->templates) === false) {
			$this->templates[$templateName] = $environment->loadTemplate($templateName);
		}
		
		return $this->templates[$templateName];
	}
}

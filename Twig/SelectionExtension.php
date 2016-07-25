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
use JGM\TableBundle\Table\TableView;
use JGM\TableBundle\Table\Utils\UrlHelper;
use Twig_Environment;
use Twig_SimpleFunction;

/**
 * Twig extension for render the table filter views.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class SelectionExtension extends AbstractTwigExtension
{
	public function __construct(UrlHelper $urlHelper, TableStopwatchService $stopwatchService)
	{
		parent::__construct($urlHelper, $stopwatchService);
	}
	
	public function getName()
	{
		return 'selection';
	}

	public function getFunctions()
	{
		return array(
			new Twig_SimpleFunction ('selection_buttons', array($this, 'getSelectionButtons'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('selection_button', array($this, 'getSelectionButton'), array('is_safe' => array('html'), 'needs_environment' => true))
		);
	}
	public function getSelectionButtons(Twig_Environment $environment, TableView $tableView)
	{
		$template = $this->loadTemplate($environment, 'JGMTableBundle:Blocks:selection.html.twig');
		
		return $template->renderBlock('selection_buttons', array(
			'tableView' => $tableView,
			'buttons' => $tableView->getSelectionButtons()
		));
	}
	
	public function getSelectionButton(Twig_Environment $environment, TableView $tableView, $buttonName)
	{
		$template = $this->loadTemplate($environment, 'JGMTableBundle:Blocks:selection.html.twig');
		
		$buttons =  $tableView->getSelectionButtons();
		return $template->renderBlock('selection_button', array(
			'button' => $buttons[$buttonName]
		));
	}
}

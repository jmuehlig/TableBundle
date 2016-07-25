<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Selection\Column;

use JGM\TableBundle\Table\Column\AbstractColumn;
use JGM\TableBundle\Table\Row\Row;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Rendering a checkbox for the selection component.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class SelectionColumn extends AbstractColumn
{
	protected function configureOptions(OptionsResolver $optionsResolver)
	{
		parent::configureOptions($optionsResolver);
		
		$optionsResolver->setDefault('label', '');
		$optionsResolver->setDefault('single_selection', false);
		$optionsResolver->setAllowedTypes('single_selection', 'bool');
	}
	
	public function getContent(Row $row)
	{
		$type = $this->options['single_selection'] === true ? "radio" : "checkbox";
		return sprintf(
			"<input type=\"%s\" name=\"selection_%s\" value=\"%s\" />", 
			$type, 
			$this->getName(), 
			$row->get('id')
		);
	}
	
	public function isSortable()
	{
		return false;
	}
}

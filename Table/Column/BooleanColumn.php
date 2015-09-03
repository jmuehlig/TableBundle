<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Column;

use JGM\TableBundle\Table\Row\Row;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Rendering a boolean value.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class BooleanColumn extends AbstractColumn
{
	protected function setDefaultOptions(OptionsResolver $optionsResolver)
	{
		parent::setDefaultOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'true' => '<input type="checkbox" checked="checked" disabled="disabled" />',
			'false' => '<input type="checkbox" disabled="disabled" />'
		));
	}
	
	public function getContent(Row $row)
	{
		$value = $this->getValue($row);
		
		if($value == 1)
		{
			return $this->options['true'];
		}
		
		return $this->options['false'];
	}
}

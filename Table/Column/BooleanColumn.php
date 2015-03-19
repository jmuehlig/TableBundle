<?php

namespace JGM\TableBundle\Table\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JGM\TableBundle\Table\Row\Row;

/**
 * Rendering a boolean value.
 *
 * @author Jan Mühlig
 */
class BooleanColumn extends AbstractColumn
{
	protected function setDefaultOptions(OptionsResolverInterface $optionsResolver)
	{
		parent::setDefaultOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'true' => '<input type="checkbox" checked="checked" disabled="disabled" />',
			'false' => '<input type="checkbox" disabled="disabled" />'
		));
	}
	
	public function getContent(Row $row)
	{
		$value = $row->get($this->getName());
		
		if($value == 1)
		{
			return $this->options['true'];
		}
		
		return $this->options['false'];
	}
}

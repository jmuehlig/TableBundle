<?php

namespace PZAD\TableBundle\Table\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PZAD\TableBundle\Table\Row\Row;

/**
 * This column will only fetch the value of the property,
 * which has the same name like this column.
 *
 * @author Jan Mühlig
 */
class TextColumn extends AbstractColumn
{
	protected function setDefaultOptions(OptionsResolverInterface $optionsResolver)
	{
		parent::setDefaultOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'nl2br' => false,
			'maxlength' => null,
			'after_maxlength' => '...',
			'empty_value' => null
		));
	}
	
	public function getContent(Row $row)
	{
		$value = $row->get($this->getName());
		
		if($value === null || strlen($value) === 0)
		{
			return $this->options['empty_value'];
		}
		
		if($this->options['nl2br'] === true)
		{
			$value = nl2br($value);
		}
		
		if($this->options['maxlength'] !== null && strlen($value) > $this->options['maxlength'])
		{
			$value = substr($value, 0, $this->options['maxlength']) . $this->options['after_maxlength'];
		}
		
		return $value;
	}
}

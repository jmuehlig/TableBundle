<?php

namespace JGM\TableBundle\Table\Column;

use JGM\TableBundle\Table\Row\Row;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This column will render numbers.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class NumberColumn extends AbstractColumn
{
	protected function setDefaultOptions(OptionsResolver $optionsResolver)
	{
		parent::setDefaultOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'empty_value' => null,
			'decimals' => 2,
			'decimal_point' => '.',
			'thousands_sep' => ','
		));
	}
	
	public function getContent(Row $row)
	{
		$value = $this->getValue($row);
		
		if($value === null || strlen($value) === 0)
		{
			return $this->options['empty_value'];
		}
		
		return number_format(
			$value,
			$this->options['decimals'],
			$this->options['decimal_point'],
			$this->options['thousands_sep']
		);
	}
}

<?php

namespace JGM\TableBundle\Table\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JGM\TableBundle\Table\Row\Row;

/**
 * Column for rendering date in format you like.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class DateColumn extends AbstractColumn
{
	public function setDefaultOptions(OptionsResolverInterface $optionsResolver)
	{
		parent::setDefaultOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'format' => 'd.m.Y H:i',
			'empty_value' => null
		));
	}


	public function getContent(Row $row)
	{
		$value = $this->getValue($row);
		
		if($value === null || (is_string($value) && strlen($value) === 0))
		{
			return $this->options['empty_value'];
		}
		
		if($value instanceof \DateTime)
		{
			return $value->format($this->options['format']);
		}
		else
		{
			return date($this->options['format'], strtotime($value));
		}
	}
}

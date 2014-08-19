<?php

namespace PZAD\TableBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of CreateTableCommand
 *
 * @author Jan Mühlig
 */
class CreateTableCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('table:create')
			->setDefinition('Create your table type for the table bundle.')
			->addArgument('entity', InputArgument::REQUIRED, 'The entity you would create a table for.')
			->addOption('paginatable', 'p', InputOption::VALUE_OPTIONAL, 'Should the table be paginatable?', 'no')
			->addOption('sortable', 's', InputOption::VALUE_OPTIONAL, 'Should the table be sortable?', 'yes');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->write('Create Table...');
	}
}

<?php

if(
	!is_file($autoloadFile = __DIR__.'/../vendor/autoload.php') 
	&& !is_file($autoloadFile = __DIR__.'/../../../../../../vendor/autoload.php')
) 
{
    throw new LogicException("File >vendor/autoload.php< not found.");
}

require $autoloadFile;
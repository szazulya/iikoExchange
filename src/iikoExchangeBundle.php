<?php


namespace iikoExchangeBundle;


use iikoExchangeBundle\Service\ExchangeCompilePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class iikoExchangeBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container); // TODO: Change the autogenerated stub
		$container->addCompilerPass(new ExchangeCompilePass());
	}
}
<?php


namespace iikoExchangeBundle\Engine;

use iikoExchangeBundle\Contract\Extensions\WithMultiRestaurantExtensionInterface;
use iikoExchangeBundle\ExtensionTrait\WithMultiRestaurantExtensionTrait;

class ExchangeMultiRestaurantEngine extends AbstractEngineBuilder implements WithMultiRestaurantExtensionInterface
{
	use WithMultiRestaurantExtensionTrait;
}
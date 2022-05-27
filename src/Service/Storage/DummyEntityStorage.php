<?php

namespace iikoExchangeBundle\Service\Storage;

use iikoExchangeBundle\Contract\Exchange\ExchangeInterface;
use iikoExchangeBundle\Contract\Service\EntitiesStorage;

class DummyEntityStorage implements EntitiesStorage
{
	private $storage = [];

	public function saveEntity(ExchangeInterface $exchange, string $entityType, $externalId, array $data)
	{
		$this->storage[$exchange->getId() . "@" . $entityType . "@" . $externalId] = $data;
	}

	public function entityExist(ExchangeInterface $exchange, string $entityType, $externalId)
	{
		return array_key_exists($exchange->getId() . "@" . $entityType . "@" . $externalId, $this->storage);
	}

	public function getEntityData(ExchangeInterface $exchange, string $entityType, $externalId)
	{
		return $this->storage[$exchange->getId() . "@" . $entityType . "@" . $externalId];
	}
}
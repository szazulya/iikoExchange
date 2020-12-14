<?php


namespace iikoExchangeBundle\Exchange;


use iikoExchangeBundle\Contract\ExchangeNodeInterface;
use iikoExchangeBundle\Contract\Extensions\WithMappingExtensionInterface;
use iikoExchangeBundle\Engine\ExchangeEngine;
use iikoExchangeBundle\ExtensionTrait\ExchangeNodeTrait;
use iikoExchangeBundle\Library\Provider\Provider;

abstract class AbstractExchangeBuilder implements ExchangeNodeInterface
{

	protected ?int $id = null;

	protected ?string $uniq = null;

	/**
	 * @return string
	 */
	public function getUniq(): string
	{
		if (!$this->uniq)
		{
			$this->generateUniq();
		}
		return $this->uniq;
	}

	/**
	 * @param string $uniq
	 * @return $this
	 */
	public function setUniq(string $uniq)
	{
		$this->uniq = $uniq;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function generateUniq()
	{
		$this->uniq = strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(16384, 20479),
			mt_rand(32768, 49151),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535)));

		return $this;
	}

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(int $id)
	{
		$this->id = $id;
		return $this;
	}


	const FIELD_EXTRACTOR = 'extractor';
	const FIELD_PROVIDER = 'provider';
	const FIELD_LOADER = 'loader';
	const FIELD_ENGINES = 'engines';
	const FIELD_SCHEDULES = 'schedules';
	const FIELD_MAPPING = 'mapping';

	protected Provider $extractor;
	protected Provider $loader;
	/** @var ExchangeEngine[] */
	protected array $engines;
	protected array $schedules;

	use ExchangeNodeTrait
	{
		ExchangeNodeTrait::jsonSerialize as public nodeJsonSerialize;
	}

	public function __construct(string $code)
	{
		$this->code = $code;
		$this->unique = md5(mt_rand() . $code);
	}


	public function jsonSerialize()
	{
		$requests = $mappings = [];

		array_map(function (ExchangeEngine $engine) use (&$requests)
		{
			foreach ($engine->getRequests() as $request)
			{
				$requests[$request->getCode()] = $request->jsonSerialize();
			}

		}, $this->getEngines());

		$this->serialiseMappingExtension($this, $mappings);

		return $this->nodeJsonSerialize() + [

				static::FIELD_EXTRACTOR => [self::FIELD_PROVIDER => $this->getExtractor()] + [ExchangeEngine::FIELD_REQUEST => array_values($requests)],
				static::FIELD_LOADER => $this->getLoader(),
				static::FIELD_ENGINES => $this->getEngines(),
				static::FIELD_MAPPING => array_values($mappings),
				static::FIELD_SCHEDULES => $this->getSchedules()
			];
	}

	protected function serialiseMappingExtension(ExchangeNodeInterface $exchangeNode, array &$mappings)
	{
		if ($exchangeNode instanceof WithMappingExtensionInterface)
		{
			foreach ($exchangeNode->getMapping() as $mapping)
			{
				$mappings[$mapping->getCode()] = $mapping;
			}
		}
		foreach ($exchangeNode->getChildNodes() as $childNode)
		{
			$this->serialiseMappingExtension($childNode, $mappings);
		}
	}

	/**
	 * @return Provider
	 */
	public function getExtractor(): Provider
	{
		return $this->extractor;
	}

	/**
	 * @param Provider $extractor
	 * @return AbstractExchangeBuilder
	 */
	public function setExtractor(Provider $extractor): AbstractExchangeBuilder
	{
		$this->extractor = $extractor;
		return $this;
	}

	/**
	 * @return Provider
	 */
	public function getLoader(): Provider
	{
		return $this->loader;
	}

	/**
	 * @param Provider $loader
	 * @return AbstractExchangeBuilder
	 */
	public function setLoader(Provider $loader): AbstractExchangeBuilder
	{
		$this->loader = $loader;
		return $this;
	}

	/**
	 * @return ExchangeEngine[]
	 */
	public function getEngines(): array
	{
		return $this->engines;
	}

	/**
	 * @param array $engines
	 * @return AbstractExchangeBuilder
	 */
	public function setEngines(array $engines): AbstractExchangeBuilder
	{
		$this->engines = $engines;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSchedules(): array
	{
		return $this->schedules;
	}

	/**
	 * @param array $schedules
	 * @return AbstractExchangeBuilder
	 */
	public function setSchedules(array $schedules): AbstractExchangeBuilder
	{
		$this->schedules = $schedules;
		return $this;
	}

	public function getChildNodes(): array
	{
		return array_merge($this->getSchedules(), $this->getEngines(), [$this->getLoader(), $this->getExtractor()]);
	}

}
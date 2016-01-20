<?php
namespace Visca\Bundle\DoctrineBundle\Model\Cache;

/**
 * Class CountResultModel
 */
class CountResultModel
{
    const CACHE_NOT_EXISTS = 1;
    const CACHE_EXISTS = 2;
    const CACHE_NEWLY_CREATED = 3;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @var int
     */
    private $cacheExists;

    /**
     * @var int
     */
    private $entityId;

    /**
     * @var mixed
     */
    private $value;

    /**
     * CountResultModel constructor.
     *
     * @param int  $entityId
     * @param string $cacheKey
     */
    public function __construct($entityId, $cacheKey)
    {
        $this->entityId = $entityId;
        $this->cacheKey = $cacheKey;
        $this->cacheExists = self::CACHE_NOT_EXISTS;
    }

    /**
     * @return int
     */
    public function getCacheExists()
    {
        return $this->cacheExists;
    }

    /**
     * @param int $cacheExists
     *
     * @return CountResultModel
     */
    public function setCacheExists($cacheExists)
    {
        $this->cacheExists = $cacheExists;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return CountResultModel
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return \int
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

}

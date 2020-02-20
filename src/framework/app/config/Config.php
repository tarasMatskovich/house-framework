<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 17:23
 */

namespace houseframework\app\config;


/**
 * Class Config
 * @package houseframework\app\config
 */
class Config implements ConfigInterface, \JsonSerializable
{

    const DELIMITER = ':';

    /**
     * @var array
     */
    private $config = [];

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->getInternal($key, $this->config);
    }

    /**
     * @param $key
     * @param $config
     * @return mixed|null
     */
    private function getInternal($key, $config)
    {
        $paths = explode(static::DELIMITER, $key);
        $path = array_shift($paths);
        $config = $config[$path] ?? null;
        if (null === $config) {
            return null;
        }
        if (is_array($config) && !empty($paths)) {
            $key = implode(static::DELIMITER, $paths);
            return $this->getInternal($key, $config);
        } else {
            return $config;
        }
    }

    /**
     * @param array $config
     */
    public function setConfigArray(array $config)
    {
        $this->config = $config;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->config;
    }
}

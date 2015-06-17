<?php
namespace Cascade;

use Monolog\Logger;
use Monolog\Registry;

use Cascade\Config;
use Cascade\Config\ConfigLoader;

/**
 * Module class that manages Monolog Logger object
 * @see Monolog\Logger
 *
 * @todo remove inheritance. There is no need to extend Logger
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class MonoLogger extends Logger
{
    /**
     * Config class that holds options for all registered loggers
     * This is optional, you can set up your loggers programmatically
     * @var Cascade\Config
     */
    protected static $config = null;

    /**
     * Instantiate a new MonoLogger object
     *
     * @see Monolog\Logger::__construct
     *
     * @param string $name The logging channel
     * @param HandlerInterface[] $handlers Optional stack of handlers,
     * the first one in the array is called first, etc.
     * @param callable[] $processors Optional array of processors
     *
     * @throws \InvalidArgumentException: if no name is given
     */
    public function __construct(
        $name,
        array $handlers = array(),
        array $processors = array()
    ) {

        if (empty($name)) {
            throw new \InvalidArgumentException('Logger name is required.');
        }

        parent::__construct($name, $handlers, $processors);
        Registry::addLogger($this);
    }

    /**
     * Get a Logger instance by name. Creates a new one if a Logger with the
     * provided name does not exist
     *
     * @param  string $name Name of the requested Logger instance
     * @return Monolog\Logger Requested instance of Logger or new instance
     */
    public static function getLogger($name)
    {
        return Registry::hasLogger($name) ? Registry::getInstance($name) : new MonoLogger($name);
    }

    /**
     * Return the config options
     *
     * @return array array with configuration options
     */
    public static function getConfig()
    {
        return self::$config;
    }

    /**
     * Load configuration options from a file or a string
     *
     * @param string $resource path to config file or string or array
     */
    public static function fileConfig($resource)
    {
        self::$config = new Config($resource, new ConfigLoader());
        self::$config->load();
        self::$config->configure();
    }
}

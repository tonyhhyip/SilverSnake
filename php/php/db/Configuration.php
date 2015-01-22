<?php
/**
 * @package php.db;
 */

namespace php\db;

use \php\util\cache\Cache;

/**
 * Configuration container for the Doctrine DBAL.
 *
 * @internal When adding a new configuration option just write a getter/setter
 *           pair and add the option to the _attributes array with a proper default value.
 */
class Configuration {
    /**
     * The attributes that are contained in the configuration.
     * Values are default values.
     *
     * @var array
     */
    protected $_attributes = array();

    /**
     * Sets the SQL logger to use. Defaults to NULL which means SQL logging is disabled.
     *
     * @param \php\logging\SQLLogger|null $logger
     *
     * @return void
     */
    public function setSQLLogger(SQLLogger $logger = null) {
        $this->_attributes['sqlLogger'] = $logger;
    }

    /**
     * Gets the SQL logger that is used.
     *
     * @return \php\logging\SQLLogger|null
     */
    public function getSQLLogger() {
        return isset($this->_attributes['sqlLogger']) ? $this->_attributes['sqlLogger'] : null;
    }

    /**
     * Gets the cache driver implementation that is used for query result caching.
     *
     * @return \php\utils\cache\Cache|null
     */
    public function getResultCacheImpl() {
        return isset($this->_attributes['resultCacheImpl']) ?
            $this->_attributes['resultCacheImpl'] : null;
    }

    /**
     * Sets the cache driver implementation that is used for query result caching.
     *
     * @param \php\utils\cache\Cache $cacheImpl
     *
     * @return void
     */
    public function setResultCacheImpl(Cache $cacheImpl) {
        $this->_attributes['resultCacheImpl'] = $cacheImpl;
    }

}
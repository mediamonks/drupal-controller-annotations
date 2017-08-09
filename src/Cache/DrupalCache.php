<?php

namespace Drupal\controller_annotations\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use Drupal\Core\Cache\CacheBackendInterface;

class DrupalCache extends CacheProvider
{
    /**
     * @var CacheBackendInterface
     */
    private $cache;

    /**
     * @param CacheBackendInterface $cache
     */
    public function __construct(CacheBackendInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    protected function doFetch($id)
    {
        return $this->cache->get($id);
    }

    /**
     * @inheritdoc
     */
    protected function doContains($id)
    {
        return $this->fetch($id) !== false;
    }

    /**
     * @inheritdoc
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $this->cache->set($id, $data, time() + $lifeTime);
    }

    /**
     * @inheritdoc
     */
    protected function doDelete($id)
    {
        $this->cache->delete($id);
    }

    /**
     * @inheritdoc
     */
    protected function doFlush()
    {
        $this->cache->deleteAll();
    }

    /**
     * @inheritdoc
     */
    protected function doGetStats()
    {
        return array(
            Cache::STATS_HITS               => null,
            Cache::STATS_MISSES             => null,
            Cache::STATS_UPTIME             => null,
            Cache::STATS_MEMORY_USAGE       => null,
            Cache::STATS_MEMORY_AVAILABLE   => null,
        );
    }
}

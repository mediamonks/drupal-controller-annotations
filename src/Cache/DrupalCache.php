<?php

namespace Drupal\controller_annotations\Cache;

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
        if ($cache = $this->cache->get($id)) {
            return $cache->data;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    protected function doContains($id)
    {
        return $this->doFetch($id) !== false;
    }

    /**
     * @inheritdoc
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        if ($lifeTime === 0) {
            $this->cache->set($id, $data);
        } else {
            $this->cache->set($id, $data, time() + $lifeTime);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doDelete($id)
    {
        $this->cache->delete($id);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doFlush()
    {
        $this->cache->deleteAll();

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doGetStats()
    {
        return;
    }
}

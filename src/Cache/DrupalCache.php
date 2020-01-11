<?php

namespace Drupal\controller_annotations\Cache;

use Doctrine\Common\Cache\CacheProvider;
use Drupal\Core\Cache\CacheBackendInterface;

class DrupalCache extends CacheProvider {

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  private $cache;

  /**
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   */
  public function __construct(CacheBackendInterface $cache) {
    $this->cache = $cache;
  }

  /**
   * @inheritdoc
   */
  protected function doFetch($id) {
    if ($cache = $this->cache->get($id)) {
      return $cache->data;
    }

    return FALSE;
  }

  /**
   * @inheritdoc
   */
  protected function doContains($id) {
    return $this->doFetch($id) !== FALSE;
  }

  /**
   * @inheritdoc
   */
  protected function doSave($id, $data, $lifeTime = 0) {
    if ($lifeTime === 0) {
      $this->cache->set($id, $data);
    }
    else {
      $this->cache->set($id, $data, time() + $lifeTime);
    }

    return TRUE;
  }

  /**
   * @inheritdoc
   */
  protected function doDelete($id) {
    $this->cache->delete($id);

    return TRUE;
  }

  /**
   * @inheritdoc
   */
  protected function doFlush() {
    $this->cache->deleteAll();

    return TRUE;
  }

  /**
   * @inheritdoc
   */
  protected function doGetStats() {
    return;
  }

}

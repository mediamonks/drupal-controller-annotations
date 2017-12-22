<?php

namespace Drupal\Tests\controller_annotations\Unit\Cache;

use Drupal\controller_annotations\Cache\DrupalCache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use StdClass;

class DrupalCacheTest extends UnitTestCase
{
    public function testDoFetch()
    {
        $drupalCache = $this->getDrupalCacheMock();
        $drupalCache->shouldReceive('get')->once()->withArgs(['[foo][1]'])->andReturn($this->getCacheData('bar'));

        $cache = new DrupalCache($drupalCache);

        $this->assertEquals('bar', $cache->fetch('foo'));
    }

    public function testDoContains()
    {
        $drupalCache = $this->getDrupalCacheMock();
        $drupalCache->shouldReceive('get')->once()->withArgs(['[foo][1]'])->andReturn($this->getCacheData('bar'));
        $drupalCache->shouldReceive('get')->once()->withArgs(['[bar][1]'])->andReturn(false);

        $cache = new DrupalCache($drupalCache);
        $this->assertTrue($cache->contains('foo'));
        $this->assertFalse($cache->contains('bar'));
    }

    public function testSave()
    {
        $drupalCache = $this->getDrupalCacheMock();
        $drupalCache->shouldReceive('set')->once()->withArgs(['[foo][1]', 'bar'])->andReturnNull();
        $drupalCache->shouldReceive('set')->once()->withArgs(['[foo][1]', 'bar', m::any()])->andReturnNull();

        $cache = new DrupalCache($drupalCache);
        $this->assertTrue($cache->save('foo', 'bar'));
        $this->assertTrue($cache->save('foo', 'bar', 1));

        m::close();
    }

    public function testDelete()
    {
        $drupalCache = $this->getDrupalCacheMock();
        $drupalCache->shouldReceive('delete')->once()->withArgs(['[foo][1]'])->andReturnNull();

        $cache = new DrupalCache($drupalCache);

        $this->assertTrue($cache->delete('foo'));
    }

    public function testFlushAll()
    {
        $drupalCache = $this->getDrupalCacheMock();
        $drupalCache->shouldReceive('deleteAll')->once()->withNoArgs()->andReturnNull();

        $cache = new DrupalCache($drupalCache);

        $this->assertTrue($cache->flushAll());
    }

    public function testGetStats()
    {
        $drupalCache = $this->getDrupalCacheMock();
        $cache = new DrupalCache($drupalCache);

        $this->assertNull($cache->getStats());
    }

    /**
     * @return CacheBackendInterface
     */
    protected function getDrupalCacheMock()
    {
        $drupalCache = m::mock(CacheBackendInterface::class);
        $drupalCache->shouldReceive('get')->withArgs(['DoctrineNamespaceCacheKey[]'])->andReturnNull();

        return $drupalCache;
    }

    /**
     * @param $data
     *
     * @return StdClass
     */
    protected function getCacheData($data)
    {
      $cacheData = new StdClass();
      $cacheData->data = $data;

      return $cacheData;
    }

    protected function tearDown()
    {
        m::close();

        parent::tearDown();
    }
}

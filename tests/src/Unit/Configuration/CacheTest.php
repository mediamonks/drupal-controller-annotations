<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\controller_annotations\Configuration\Cache;
use Drupal\Tests\UnitTestCase;

class CacheTest extends UnitTestCase
{
    public function testProperties()
    {
        $cache = new Cache([]);

        $cache->setExpires('tomorrow');
        $this->assertEquals('tomorrow', $cache->getExpires());

        $cache->setMaxAge(60);
        $this->assertEquals(60, $cache->getMaxAge());

        $cache->setSMaxAge(120);
        $this->assertEquals(120, $cache->getSMaxAge());

        $this->assertFalse($cache->isPublic());
        $this->assertFalse($cache->isPrivate());

        $cache->setPublic(true);
        $this->assertTrue($cache->isPublic());
        $this->assertFalse($cache->isPrivate());

        $cache->setPublic(false);
        $this->assertFalse($cache->isPublic());
        $this->assertTrue($cache->isPrivate());

        $cache->setVary('vary');
        $this->assertEquals('vary', $cache->getVary());

        $cache->setETag('foobar');
        $this->assertEquals('foobar', $cache->getETag());

        $cache->setLastModified('yesterday');
        $this->assertEquals('yesterday', $cache->getLastModified());
    }

    public function testGetAliasName()
    {
        $cache = new Cache([]);

        $this->assertEquals('cache', $cache->getAliasName());
    }

    public function testAllowArray()
    {
        $cache = new Cache([]);

        $this->assertFalse($cache->allowArray());
    }
}

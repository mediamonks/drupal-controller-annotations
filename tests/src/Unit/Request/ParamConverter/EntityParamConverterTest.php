<?php

namespace Drupal\Tests\controller_annotations\Unit\Request\ParamConverter;

use Drupal\controller_annotations\Configuration\ParamConverter;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\controller_annotations\Request\ParamConverter\EntityParamConverter;
use Drupal\node\Entity\Node;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @group controller_annotations
 */
class EntityParamConverterTest extends UnitTestCase
{
    private function getEntityParamConverter()
    {
        $node = m::mock(Node::class);

        $entityInterface = m::mock(EntityInterface::class);
        $entityInterface->shouldReceive('load')->andReturn($node);

        $entityTypeManager = m::mock(EntityTypeManager::class);
        $entityTypeManager->shouldReceive('getStorage')->andReturn($entityInterface);

        return new EntityParamConverter($entityTypeManager);
    }

    public function testSupports()
    {
        $paramConverter = m::mock(ParamConverter::class);
        $paramConverter->shouldReceive('getClass')->once()->andReturn(Node::class);
        $this->assertTrue($this->getEntityParamConverter()->supports($paramConverter));
    }

    public function testNotSupports()
    {
        $paramConverter = m::mock(ParamConverter::class);
        $paramConverter->shouldReceive('getClass')->once()->andReturn(self::class);
        $this->assertFalse($this->getEntityParamConverter()->supports($paramConverter));
    }

    public function testApply()
    {
        $name = 'test';
        $request = new Request();
        $request->attributes->set($name, 1);

        $node = m::mock(Node::class);

        $paramConverter = m::mock(ParamConverter::class);
        $paramConverter->shouldReceive('getClass')->once()->andReturn(Node::class);
        $paramConverter->shouldReceive('getName')->once()->andReturn($name);
        $paramConverter->shouldReceive('getOptions')->once()->andReturn([]);

        $this->assertTrue($this->getEntityParamConverter()->supports($paramConverter));
        $this->getEntityParamConverter()->apply($request, $paramConverter);

        $this->assertTrue($request->attributes->has($name));
        $this->assertEquals($node, $request->attributes->get($name));
    }

    public function testApplyNonExistingEntity()
    {
        $this->setExpectedException(NotFoundHttpException::class);

        $entityInterface = m::mock(EntityInterface::class);
        $entityInterface->shouldReceive('load')->andReturnNull();

        $entityTypeManager = m::mock(EntityTypeManager::class);
        $entityTypeManager->shouldReceive('getStorage')->andReturn($entityInterface);

        $name = 'test';
        $request = new Request();
        $request->attributes->set($name, 1);

        $paramConverter = m::mock(ParamConverter::class);
        $paramConverter->shouldReceive('getClass')->once()->andReturn(Node::class);
        $paramConverter->shouldReceive('getName')->once()->andReturn($name);
        $paramConverter->shouldReceive('isOptional')->once()->andReturn(false);

        $entityParamConverter = new EntityParamConverter($entityTypeManager);

        $this->assertTrue($entityParamConverter->supports($paramConverter));
        $entityParamConverter->apply($request, $paramConverter);
    }

    public function testApplyWithBundle()
    {
        $id = 1;
        $bundle = 'article';

        $node = m::mock(Node::class);
        $node->shouldReceive('bundle')->once()->andReturn($bundle);

        $entityInterface = m::mock(EntityInterface::class);
        $entityInterface->shouldReceive('load')->withArgs([$id])->andReturn($node);

        $entityTypeManager = m::mock(EntityTypeManager::class);
        $entityTypeManager->shouldReceive('getStorage')->withArgs(['node'])->andReturn($entityInterface);

        $nodeParamConverter = new EntityParamConverter($entityTypeManager);

        $name = 'test';
        $request = new Request();
        $request->attributes->set($name, $id);

        $paramConverter = m::mock(ParamConverter::class);
        $paramConverter->shouldReceive('getClass')->once()->andReturn(Node::class);
        $paramConverter->shouldReceive('getName')->once()->andReturn($name);
        $paramConverter->shouldReceive('getOptions')->once()->andReturn(['bundle' => $bundle]);

        $this->assertTrue($nodeParamConverter->supports($paramConverter));
        $nodeParamConverter->apply($request, $paramConverter);

        $this->assertTrue($request->attributes->has($name));
        $this->assertEquals($node, $request->attributes->get($name));
    }

    public function testApplyWithWrongBundle()
    {
        $this->setExpectedException(NotFoundHttpException::class);

        $id = 1;
        $bundle = 'article';

        $node = m::mock(Node::class);
        $node->shouldReceive('bundle')->once()->andReturn('not_an_article');

        $entityInterface = m::mock(EntityInterface::class);
        $entityInterface->shouldReceive('load')->withArgs([$id])->andReturn($node);

        $entityTypeManager = m::mock(EntityTypeManager::class);
        $entityTypeManager->shouldReceive('getStorage')->withArgs(['node'])->andReturn($entityInterface);

        $nodeParamConverter = new EntityParamConverter($entityTypeManager);

        $name = 'test';
        $request = new Request();
        $request->attributes->set($name, $id);

        $paramConverter = m::mock(ParamConverter::class);
        $paramConverter->shouldReceive('getClass')->once()->andReturn(Node::class);
        $paramConverter->shouldReceive('getName')->once()->andReturn($name);
        $paramConverter->shouldReceive('getOptions')->once()->andReturn(['bundle' => $bundle]);

        $this->assertTrue($nodeParamConverter->supports($paramConverter));
        $nodeParamConverter->apply($request, $paramConverter);
    }

    public function testApplyOptionalWhenEmpty()
    {
        $id = 1;

        $entityInterface = m::mock(EntityInterface::class);
        $entityInterface->shouldReceive('load')->withArgs([$id])->andReturn(null);

        $entityTypeManager = m::mock(EntityTypeManager::class);
        $entityTypeManager->shouldReceive('getStorage')->withArgs(['node'])->andReturn($entityInterface);

        $nodeParamConverter = new EntityParamConverter($entityTypeManager);

        $name = 'test';
        $request = new Request();
        $request->attributes->set($name, $id);

        $paramConverter = m::mock(ParamConverter::class);
        $paramConverter->shouldReceive('getClass')->once()->andReturn(Node::class);
        $paramConverter->shouldReceive('getName')->once()->andReturn($name);
        $paramConverter->shouldReceive('isOptional')->once()->andReturn(true);

        $this->assertTrue($nodeParamConverter->supports($paramConverter));
        $nodeParamConverter->apply($request, $paramConverter);

        $this->assertTrue($request->attributes->has($name));
        $this->assertEquals(null, $request->attributes->get($name));
    }

    public function testApplyWithoutAttribute()
    {
        $id = 1;
        $bundle = 'article';

        $entityInterface = m::mock(EntityInterface::class);
        $entityInterface->shouldReceive('load')->withArgs([$id])->andReturn(null);

        $entityTypeManager = m::mock(EntityTypeManager::class);
        $entityTypeManager->shouldReceive('getStorage')->withArgs(['node'])->andReturn($entityInterface);

        $nodeParamConverter = new EntityParamConverter($entityTypeManager);

        $name = 'test';
        $request = new Request();

        $paramConverter = m::mock(ParamConverter::class);
        $paramConverter->shouldReceive('getClass')->once()->andReturn(Node::class);
        $paramConverter->shouldReceive('getName')->once()->andReturn($name);
        $paramConverter->shouldReceive('getOptions')->never()->andReturn(['bundle' => $bundle]);

        $this->assertTrue($nodeParamConverter->supports($paramConverter));
        $this->assertFalse($nodeParamConverter->apply($request, $paramConverter));
    }

    public function testOptional()
    {
        $id = 1;
        $bundle = 'article';

        $entityInterface = m::mock(EntityInterface::class);
        $entityInterface->shouldReceive('load')->withArgs([$id])->andReturn(null);

        $entityTypeManager = m::mock(EntityTypeManager::class);
        $entityTypeManager->shouldReceive('getStorage')->withArgs(['node'])->andReturn($entityInterface);

        $nodeParamConverter = new EntityParamConverter($entityTypeManager);

        $name = 'test';
        $request = new Request();

        $paramConverter = m::mock(ParamConverter::class);
        $paramConverter->shouldReceive('getName')->once()->andReturn($name);
        $paramConverter->shouldReceive('getOptions')->never()->andReturn(['bundle' => $bundle]);

        $this->assertFalse($nodeParamConverter->apply($request, $paramConverter));
    }

    public function testOptionalEmptyAttribute()
    {
        $id = 1;
        $bundle = 'article';

        $entityInterface = m::mock(EntityInterface::class);
        $entityInterface->shouldReceive('load')->withArgs([$id])->andReturn(null);

        $entityTypeManager = m::mock(EntityTypeManager::class);
        $entityTypeManager->shouldReceive('getStorage')->withArgs(['node'])->andReturn($entityInterface);

        $nodeParamConverter = new EntityParamConverter($entityTypeManager);

        $name = 'test';
        $request = new Request();
        $request->attributes->set($name, '');

        $paramConverter = m::mock(ParamConverter::class);
        $paramConverter->shouldReceive('getName')->once()->andReturn($name);
        $paramConverter->shouldReceive('getOptions')->never()->andReturn(['bundle' => $bundle]);
        $paramConverter->shouldReceive('isOptional')->once()->andReturn(true);

        $this->assertFalse($nodeParamConverter->apply($request, $paramConverter));
    }

    public function tearDown()
    {
        m::close();
    }
}

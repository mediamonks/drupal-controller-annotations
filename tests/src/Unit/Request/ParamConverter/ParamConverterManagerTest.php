<?php

namespace Drupal\Tests\controller_annotations\Unit\Request\ParamConverter;

use Drupal\controller_annotations\Configuration;
use Drupal\controller_annotations\Request\ParamConverter\ParamConverterInterface;
use Drupal\controller_annotations\Request\ParamConverter\ParamConverterManager;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group controller_annotations
 */
class ParamConverterManagerTest extends UnitTestCase {

  public function testPriorities() {
    $manager = new ParamConverterManager();
    $this->assertEquals([], $manager->all());

    $high = $this->createParamConverterMock();
    $low = $this->createParamConverterMock();

    $manager->add($low);
    $manager->add($high, 10);

    $this->assertEquals([$high, $low], $manager->all());
  }

  public function testApply() {
    $supported = $this->createParamConverterMock();
    $supported
      ->expects($this->once())
      ->method('supports')
      ->will($this->returnValue(TRUE));
    $supported
      ->expects($this->once())
      ->method('apply')
      ->will($this->returnValue(FALSE));

    $invalid = $this->createParamConverterMock();
    $invalid
      ->expects($this->once())
      ->method('supports')
      ->will($this->returnValue(FALSE));
    $invalid
      ->expects($this->never())
      ->method('apply');

    $configurations = [
      new Configuration\ParamConverter([
      'name' => 'var',
      ]),
    ];

    $manager = new ParamConverterManager();
    $manager->add($supported);
    $manager->add($invalid);
    $manager->apply(new Request(), $configurations);
  }

  public function testApplyNamedConverter() {
    $converter = $this->createParamConverterMock();
    $converter
      ->expects($this->any())
      ->method('supports')
      ->will($this->returnValue(TRUE));

    $converter
      ->expects($this->any())
      ->method('apply');

    $request = new Request();
    $request->attributes->set('param', '1234');

    $configuration = new Configuration\ParamConverter([
      'name' => 'param',
      'class' => 'stdClass',
      'converter' => 'test',
    ]);

    $manager = new ParamConverterManager();
    $manager->add($converter, 0, 'test');
    $this->assertNull($manager->apply($request, [$configuration]));
  }

  /**
   * @expectedException    \RuntimeException
   * @expectedExceptionMessage Converter 'test' does not support conversion of parameter 'param'.
   */
  public function testApplyNamedConverterNotSupportsParameter() {
    $converter = $this->createParamConverterMock();
    $converter
      ->expects($this->any())
      ->method('supports')
      ->will($this->returnValue(FALSE));

    $request = new Request();
    $request->attributes->set('param', '1234');

    $configuration = new Configuration\ParamConverter([
      'name' => 'param',
      'class' => 'stdClass',
      'converter' => 'test',
    ]);

    $manager = new ParamConverterManager();
    $manager->add($converter, 0, 'test');
    $manager->apply($request, [$configuration]);
  }

  /**
   * @expectedException    \RuntimeException
   * @expectedExceptionMessage No converter named 'test' found for conversion of parameter 'param'.
   */
  public function testApplyNamedConverterNoConverter() {
    $request = new Request();
    $request->attributes->set('param', '1234');

    $configuration = new Configuration\ParamConverter([
      'name' => 'param',
      'class' => 'stdClass',
      'converter' => 'test',
    ]);

    $manager = new ParamConverterManager();
    $manager->apply($request, [$configuration]);
  }

  public function testApplyNotCalledOnAlreadyConvertedObjects() {
    $converter = $this->createParamConverterMock();
    $converter
      ->expects($this->never())
      ->method('supports');

    $converter
      ->expects($this->never())
      ->method('apply');

    $request = new Request();
    $request->attributes->set('converted', new \stdClass());

    $configuration = new Configuration\ParamConverter([
      'name' => 'converted',
      'class' => 'stdClass',
    ]);

    $manager = new ParamConverterManager();
    $manager->add($converter);
    $manager->apply($request, [$configuration]);
  }

  public function testApplyWithoutArray() {
    $converter = $this->createParamConverterMock();
    $converter
      ->expects($this->any())
      ->method('supports')
      ->will($this->returnValue(FALSE));

    $converter
      ->expects($this->never())
      ->method('apply');

    $request = new Request();

    $configuration = new Configuration\ParamConverter([
      'name' => 'var',
    ]);

    $manager = new ParamConverterManager();
    $manager->add($converter);
    $manager->apply($request, $configuration);
  }

  protected function createParamConverterMock() {
    return $this->getMockBuilder(ParamConverterInterface::class)->getMock();
  }

}

<?php

namespace Drupal\Tests\controller_annotations\Unit\Request\ParamConverter;

use Drupal\controller_annotations\Configuration\ParamConverter;
use Drupal\controller_annotations\Request\ParamConverter\DateTimeParamConverter;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group controller_annotations
 */
class DateTimeParamConverterTest extends UnitTestCase
{

    private $converter;

    public function setUp()
    {
        $this->converter = new DateTimeParamConverter();
    }

    public function testSupports()
    {
        $config = $this->createConfiguration('DateTime');
        $this->assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(__CLASS__);
        $this->assertFalse($this->converter->supports($config));

        $config = $this->createConfiguration();
        $this->assertFalse($this->converter->supports($config));
    }

    public function testApply()
    {
        $request = new Request([], [], ['start' => '2012-07-21 00:00:00']);
        $config = $this->createConfiguration('DateTime', 'start');

        $this->converter->apply($request, $config);

        $this->assertInstanceOf('DateTime', $request->attributes->get('start'));
        $this->assertEquals(
            '2012-07-21',
            $request->attributes->get('start')->format('Y-m-d')
        );
    }

    public function testApplyInvalidDate404Exception()
    {
        $request = new Request([], [], ['start' => 'Invalid DateTime Format']);
        $config = $this->createConfiguration('DateTime', 'start');

        $this->setExpectedException(
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            'Invalid date given for parameter "start".'
        );
        $this->converter->apply($request, $config);
    }

    public function testApplyWithFormatInvalidDate404Exception()
    {
        $request = new Request([], [], ['start' => '2012-07-21']);
        $config = $this->createConfiguration('DateTime', 'start');
        $config->expects($this->any())->method('getOptions')->will(
            $this->returnValue(['format' => 'd.m.Y'])
        );

        $this->setExpectedException(
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            'Invalid date given for parameter "start".'
        );
        $this->converter->apply($request, $config);
    }

    public function testApplyOptionalWithEmptyAttribute()
    {
        $request = new Request([], [], ['start' => null]);
        $config = $this->createConfiguration('DateTime', 'start');
        $config->expects($this->once())
          ->method('isOptional')
          ->will($this->returnValue(true));

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get('start'));
    }

    public function testApplyEmptyAttribute()
    {
        $request = new Request();
        $config = $this->createConfiguration('DateTime', 'start');

        $this->assertFalse($this->converter->apply($request, $config));
    }

    public function createConfiguration($class = null, $name = null)
    {
        $config = $this
          ->getMockBuilder(ParamConverter::class)
          ->setMethods(
              [
              'getClass',
              'getAliasName',
              'getOptions',
              'getName',
              'allowArray',
              'isOptional',
              ]
          )
          ->disableOriginalConstructor()
          ->getMock();

        if ($name !== null) {
            $config->expects($this->any())
              ->method('getName')
              ->will($this->returnValue($name));
        }
        if ($class !== null) {
            $config->expects($this->any())
              ->method('getClass')
              ->will($this->returnValue($class));
        }

        return $config;
    }
}

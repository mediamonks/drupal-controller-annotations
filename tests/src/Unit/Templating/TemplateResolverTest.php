<?php

namespace Drupal\Tests\controller_annotations\Unit\Templating;

use Drupal\controller_annotations\Templating\TemplateResolver;
use Drupal\Tests\UnitTestCase;

require_once __DIR__.'/../../polyfill.php';

/**
 * @group controller_annotations
 */
class TemplateResolverTest extends UnitTestCase
{
    /**
     * @var TemplateResolver
     */
    private $templateResolver;

    public function setUp()
    {
        $this->templateResolver = new TemplateResolver();
    }

    /**
     * @dataProvider controllerActionProvider
     */
    public function testResolveByControllerAndAction(
        $controller,
        $action,
        $expected
    ) {
        $this->assertEquals(
            $expected,
            $this->templateResolver->resolveByControllerAndAction(
                $controller,
                $action
            )
        );
    }

    /**
     * @return array
     */
    public function controllerActionProvider()
    {
        $expected = 'modules/foo/templates/foo-foo-bar.html.twig';

        return [
          ['Drupal\foo\Controller\FooController', 'barAction', $expected],
          ['Drupal\foo\Controller\Foo', 'barAction', $expected],
          ['Drupal\foo\Controller\FooController', 'bar', $expected],
          ['Drupal\foo\Controller\Foo', 'bar', $expected],
          [
            'Drupal\foo\Controller\Bar\FooController',
            'barAction',
            'modules/foo/templates/foo-bar-foo-bar.html.twig',
          ],
          [
            'Drupal\foo\Controller\Foo\FooController',
            'barAction',
            'modules/foo/templates/foo-foo-foo-bar.html.twig',
          ],
          [
            'Drupal\foo\Controller\Foo\FooController',
            'fooAction',
            'modules/foo/templates/foo-foo-foo-foo.html.twig',
          ],
        ];
    }

    /**
     * @dataProvider normalizeProvider
     */
    public function testNormalize($template, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->templateResolver->normalize($template)
        );
    }

    /**
     * @return array
     */
    public function normalizeProvider()
    {
        return [
          ['foo:bar', 'modules/foo/templates/foo-bar.html.twig'],
          ['foobar:baz', 'modules/foobar/templates/foobar-baz.html.twig'],
          ['foo:bar:baz', 'modules/foo/templates/foo-bar-baz.html.twig'],
        ];
    }

    public function testNormalizeWithInvalidTemplate()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->templateResolver->normalize('foo');
    }

    public function testResolveByControllerAndActionWithInvalidController()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->templateResolver->resolveByControllerAndAction('Foo', 'fooAction');
    }
}

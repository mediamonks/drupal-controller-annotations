<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\controller_annotations\Configuration\ParamConverter;
use Drupal\Core\Controller\ControllerBase;
use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("test/param-converter/")
 */
class ParamConverterController extends ControllerBase
{

    /**
     * @Route("date/{start}")
     * @Security(access=true)
     * @ParamConverter()
     */
    public function dateAction(\DateTime $start)
    {
        return new Response($start->format('Y-m-d'));
    }

    /**
     * @Route("date-format/{start}")
     * @Security(access=true)
     * @ParamConverter("start", options={"format": "d-m-Y"})
     */
    public function dateFormatAction(\DateTime $start)
    {
        return new Response($start->format('Y-m-d'));
    }

    /**
     * @Route("date-multiple/{start}/{end}")
     * @Security(access=true)
     * @ParamConverter
     */
    public function dateMultipleAction(\DateTime $start, \DateTime $end)
    {
        return new Response($start->format('Y-m-d').'-'.$end->format('Y-m-d'));
    }

    /**
     * @Route("date-optional/{start}")
     * @Security(access=true)
     * @ParamConverter()
     */
    public function optionalDateAction(\DateTime $start = null)
    {
        if (empty($start)) {
            return new Response('empty');
        }
        return new Response($start->format('Y-m-d'));
    }
}

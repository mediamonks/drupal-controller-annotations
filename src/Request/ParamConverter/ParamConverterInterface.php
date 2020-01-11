<?php

namespace Drupal\controller_annotations\Request\ParamConverter;

use Drupal\controller_annotations\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Converts request parameters to objects and stores them as request
 * attributes, so they can be injected as controller method arguments.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface ParamConverterInterface {

  /**
   * Stores the object in the request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param \Drupal\controller_annotations\Configuration\ParamConverter $configuration
   *   Contains the name, class and options of the object.
   *
   * @return bool True if the object has been successfully set, else false
   */
  public function apply(Request $request, ParamConverter $configuration);

  /**
   * Checks if the object is supported.
   *
   * @param \Drupal\controller_annotations\Configuration\ParamConverter $configuration
   *   Should be an instance of ParamConverter.
   *
   * @return bool True if the object is supported, else false
   */
  public function supports(ParamConverter $configuration);

}

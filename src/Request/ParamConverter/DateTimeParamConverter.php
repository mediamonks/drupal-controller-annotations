<?php

namespace Drupal\controller_annotations\Request\ParamConverter;

use Drupal\controller_annotations\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;

/**
 * Convert DateTime instances from request attribute variable.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class DateTimeParamConverter implements ParamConverterInterface {

  /**
   * {@inheritdoc}
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   When invalid date given.
   */
  public function apply(Request $request, ParamConverter $configuration) {
    $param = $configuration->getName();
    if (!$request->attributes->has($param)) {
      return FALSE;
    }

    $value = $request->attributes->get($param);
    if (!$value && $configuration->isOptional()) {
      return FALSE;
    }

    $request->attributes->set(
      $param,
      $this->getDateTime($configuration, $value, $param)
    );

    return TRUE;
  }

  /**
   * @param \Drupal\controller_annotations\Configuration\ParamConverter $configuration
   * @param $value
   * @param $param
   * @return bool|DateTime
   * @throws \Exception
   */
  protected function getDateTime(ParamConverter $configuration, $value, $param) {
    $options = $configuration->getOptions();

    if (isset($options['format'])) {
      $date = DateTime::createFromFormat($options['format'], $value);
    }
    elseif (FALSE !== strtotime($value)) {
      $date = new DateTime($value);
    }

    if (empty($date)) {
      throw new NotFoundHttpException(
        sprintf('Invalid date given for parameter "%s".', $param)
      );
    }

    return $date;
  }

  /**
   * {@inheritdoc}
   */
  public function supports(ParamConverter $configuration) {
    return \DateTime::class === $configuration->getClass();
  }

}

<?php

namespace Drupal\controller_annotations\Request\ParamConverter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NodeParamConverter implements ParamConverterInterface
{
    /**
     * @var EntityTypeManagerInterface
     */
    private $entityTypeManager;

    /**
     * @param EntityTypeManagerInterface $entityTypeManager
     */
    public function __construct(EntityTypeManagerInterface $entityTypeManager)
    {
        $this->entityTypeManager = $entityTypeManager;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = $configuration->getName();
        $options = $configuration->getOptions();

        if (!$request->attributes->has($param)) {
            return false;
        }

        $value = $request->attributes->get($param);

        $object = $this->entityTypeManager->getStorage('node')->load($value);

        $class = 'node';
        if (isset($options['bundle'])) {
            $class = $options['bundle'];
        }
        if (
            (is_null($object) && false === $configuration->isOptional())
            || (!is_null($object) && isset($options['bundle']) && $object->bundle() !== $options['bundle'])
        ) {
            throw new NotFoundHttpException(sprintf('%s not found.', $class));
        }

        $request->attributes->set($param, $object);
    }

    /**
     * @param ParamConverter $configuration
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return Node::class === $configuration->getClass();
    }
}

<?php

namespace Drupal\controller_annotations\Request\ParamConverter;

use Drupal\controller_annotations\Configuration\ParamConverter;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityParamConverter implements ParamConverterInterface
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
     *
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = $configuration->getName();
        if (!$request->attributes->has($param)) {
            return false;
        }

        $value = $request->attributes->get($param);
        if (!$value && $configuration->isOptional()) {
            return false;
        }

        $request->attributes->set(
            $param,
            $this->getNode($value, $configuration)
        );

        return true;
    }

    /**
     * @param string $value
     * @param ParamConverter $configuration
     *
     * @return EntityInterface|null
     */
    protected function getNode($value, ParamConverter $configuration)
    {
        $node = $this->entityTypeManager->getStorage('node')->load($value);
        $this->assertValidNode($configuration, $node);

        return $node;
    }

    /**
     * @param ParamConverter $configuration
     * @param EntityInterface $node
     */
    protected function assertValidNode(
        ParamConverter $configuration,
        EntityInterface $node = null
    ) {
        if (is_null($node) && $configuration->isOptional()) {
            return;
        }
        if (is_null($node)) {
            throw new NotFoundHttpException('entity not found.');
        }
        $options = $configuration->getOptions();
        if (isset($options['bundle']) && $node->bundle() !== $options['bundle']) {
            throw new NotFoundHttpException(
                sprintf('%s not found.', $options['bundle'])
            );
        }
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return in_array(
            $configuration->getClass(),
            [
                NodeInterface::class,
                Node::class,
                EntityInterface::class,
                Entity::class,
                ContentEntityInterface::class,
                ContentEntityBase::class,
            ]
        );
    }
}

<?php

namespace Drupal\controller_annotations\Request\ParamConverter;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
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

        $request->attributes->set(
          $param,
          $this->getNode($value, $configuration)
        );
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
        $options = $configuration->getOptions();

        if (
          (is_null($node) && false === $configuration->isOptional())
          || (!is_null($node) && isset($options['bundle']) && $node->bundle(
            ) !== $options['bundle'])
        ) {
            $class = 'node';
            if (isset($options['bundle'])) {
                $class = $options['bundle'];
            }
            throw new NotFoundHttpException(sprintf('%s not found.', $class));
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

<?php

namespace Drupal\controller_annotations\Templating;

class TemplateResolver
{
    /**
     * Convert controller class
     * "Drupal\<module>\Controller\<controller>Controller"
     * and controller action
     * "<action>Action"
     * into template file path:
     * "modules/<module>/templates/<module>-<controller>-<action>.html.twig"
     *
     * @param string $controllerClass
     * @param string $action
     * @return string
     */
    public function resolveByControllerAndAction($controllerClass, $action)
    {
        preg_match('/^Drupal\\\(.*)\\\Controller\\\(.*)/', $controllerClass, $data);
        if (!empty($data)) {
            $module = $data[1];
            $controller = $data[2];
        } else {
            throw new \InvalidArgumentException(
                sprintf('Controller class "%s" not supported', $controllerClass)
            );
        }

        if (preg_match('/^(.+)Controller$/', $controller, $matchController)) {
            $controller = $matchController[1];
        }
        if (preg_match('/^(.+)Action$/', $action, $matchAction)) {
            $action = $matchAction[1];
        }

        return $this->format($module, $controller, $action);
    }

    /**
     * Convert
     * "<module>:<controller>"
     * and
     * "<module>:<controller>:<action>"
     * into
     * "modules/<module>/templates/<module>-<controller>(-<action>).html.twig"
     *
     * @param string $template
     * @return string
     */
    public function normalize($template)
    {
        if (preg_match('/^(.+):(.+):(.+)$/', $template, $matches)) {
            return $this->format($matches[1], $matches[2], $matches[3]);
        }
        if (preg_match('/^(.+):(.+)$/', $template, $matches)) {
            return $this->format($matches[1], $matches[2]);
        }

        throw new \InvalidArgumentException(
            sprintf('Template pattern "%s" not supported', $template)
        );
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return string
     */
    private function format($module, $controller, $action = null)
    {
        $controller = $this->normalizeString($controller);

        $templateName = sprintf('%s-%s', $module, $controller);
        if (!empty($action)) {
            $templateName = sprintf(
                '%s-%s',
                $templateName,
                $this->normalizeString($action)
            );
        }

        return sprintf('modules/%s/templates/%s.html.twig', $module, $templateName);
    }

    /**
     * @param string $value
     * @return string
     */
    private function normalizeString($value)
    {
        return str_replace('\\', '-', mb_strtolower($value));
    }
}

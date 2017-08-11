<?php

namespace Drupal\controller_annotations_test\Title;

class Custom
{

    /**
     * @return string
     */
    public function title()
    {
        return 'Hello Callback';
    }
}

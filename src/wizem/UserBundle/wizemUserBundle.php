<?php

namespace wizem\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class wizemUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}

<?php

namespace MK\FOSUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MKFOSUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}

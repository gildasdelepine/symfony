<?php

namespace SML\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SMLUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}

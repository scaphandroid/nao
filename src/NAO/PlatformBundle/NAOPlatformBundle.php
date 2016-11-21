<?php

namespace NAO\PlatformBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NAOPlatformBundle extends Bundle
{
      public function getParent()
      {
          return 'FOSUserBundle';
      }
}

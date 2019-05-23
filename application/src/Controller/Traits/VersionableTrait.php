<?php

namespace App\Controller\Traits;

use Symfony\Component\HttpKernel\Exception\HttpException;

trait VersionableTrait
{
    /**
     * @param int $version
     * @param int $min
     * @param int $max
     *
     * @throws HttpException
     */
    protected function minMaxVersion(int $version, int $min = null, int $max = null): void
    {
        if (null !== $min && $version < $min) {
            throw new HttpException(404);
        }

        if (null !== $max && $version > $max) {
            throw new HttpException(404);
        }
    }
}

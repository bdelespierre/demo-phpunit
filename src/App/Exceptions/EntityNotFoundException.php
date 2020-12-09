<?php

namespace App\Exceptions;

class EntityNotFoundException extends \RuntimeException
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(string $class, array $args)
    {
        $json = json_encode($args);

        parent::__construct("unable to find entity '{$class}' using '{$json}'");
    }
}

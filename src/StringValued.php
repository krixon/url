<?php

declare(strict_types=1);

namespace Krixon\URL;

trait StringValued
{
    private $value;


    public function __toString() : string
    {
        return $this->toString();
    }


    public function toString() : string
    {
        return $this->value;
    }
}

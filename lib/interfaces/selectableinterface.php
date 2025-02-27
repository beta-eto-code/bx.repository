<?php

namespace Bx\Repository\Interfaces;

interface SelectableInterface
{
    /**
     * @return string[]
     */
    public static function getSelectFiledNames(): array;
}

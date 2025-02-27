<?php

namespace Bx\Repository\Interfaces;

use Access\Scope\Interfaces\AccessRecipientContextInterface;
use Data\Provider\Interfaces\DataProviderInterface;
use Data\Provider\Interfaces\PkOperationResultInterface;

interface SavableInterface
{
    public function save(
        DataProviderInterface $dataProvider,
        ?AccessRecipientContextInterface $recipientContext = null
    ): PkOperationResultInterface;
}

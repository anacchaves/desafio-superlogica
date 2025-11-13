<?php

namespace App\Exceptions;

class ProductCannotBeDeletedException extends BusinessRuleException
{
    public function __construct()
    {
        parent::__construct('Não é possível excluir um produto com estoque maior que zero');
    }
}

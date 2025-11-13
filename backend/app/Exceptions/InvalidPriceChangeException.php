<?php

namespace App\Exceptions;

class InvalidPriceChangeException extends BusinessRuleException
{
    public function __construct(float $minPrice, float $maxPrice)
    {
        $message = sprintf(
            'O preço não pode variar mais de 30%% do valor atual (R$ %.2f - R$ %.2f)',
            $minPrice,
            $maxPrice
        );
        parent::__construct($message);
    }
}

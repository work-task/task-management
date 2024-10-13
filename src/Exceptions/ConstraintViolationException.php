<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ConstraintViolationException extends \Exception
{
    public function __construct(private readonly ConstraintViolationListInterface $violationList)
    {
        parent::__construct('Validation failed');
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}

<?php

namespace App\Exceptions;

use Exception;

class TenantAccessException extends Exception
{
    protected int $status = 403;

    public function __construct(
        string $message = 'No access',
        int $status = 403
    ) {
        parent::__construct($message);
        $this->status = $status;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}

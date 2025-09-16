<?php

namespace App\Dto;


class ServiceFault
{
    public string $code;
    public string $message;
    public ?string $details = null;
}

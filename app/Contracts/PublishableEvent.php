<?php

namespace App\Contracts;

interface PublishableEvent
{
    public function data(): array;

    public function topic(): string;
}

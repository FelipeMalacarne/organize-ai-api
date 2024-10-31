<?php

namespace App\Services\DocumentAI;

use Google\Cloud\DocumentAI\V1\Processor;

class ProcessorFactory
{
    public static function createProcessor(string $processorName): Processor
    {
        //..
    }
}

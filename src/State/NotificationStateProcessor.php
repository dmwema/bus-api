<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class NotificationStateProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $persistProcessor, private ProcessorInterface $removeProcessor)
    {
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Handle the state
        $data->setIsSent(false);
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        return $result;

    }

}

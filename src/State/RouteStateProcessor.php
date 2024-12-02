<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class RouteStateProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $persistProcessor, private ProcessorInterface $removeProcessor)
    {
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $data->setEndingTime(new \DateTime('now',new \DateTimeZone('Africa/Kinshasa')));
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        return $result;
    }
}

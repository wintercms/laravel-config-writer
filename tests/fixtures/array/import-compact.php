<?php
use Symfony\Component\HttpFoundation\Response;
use Example\EnumExample;
return [
    'foo' => Response::HTTP_OK,
    'bar' => EnumExample::Value->value
];

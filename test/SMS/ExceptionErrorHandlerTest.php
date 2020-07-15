<?php
declare(strict_types=1);

namespace NexmoTest\SMS;

use Nexmo\Client\Exception\Request as ExceptionRequest;
use Nexmo\Client\Exception\ThrottleException;
use Nexmo\SMS\ExceptionErrorHandler;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ResponseFactory;

class ExceptionErrorHandlerTest extends TestCase
{
    public function test429ThrowsThrottleException()
    {
        $this->expectException(ThrottleException::class);
        $this->expectExceptionMessage('Too many concurrent requests');

        $respFactory = new ResponseFactory();
        $response = $respFactory->createResponse(429);

        $handler = new ExceptionErrorHandler();
        $handler($response, new Request());
    }

    public function testGenericErrorThrowsRequestException()
    {
        $this->expectException(ExceptionRequest::class);
        $this->expectExceptionMessage('This is a generic error being thrown');
        $this->expectExceptionCode(499);

        $response = new JsonResponse([
            'error-code' => 499,
            'error-code-label' => 'This is a generic error being thrown'
        ]);

        $handler = new ExceptionErrorHandler();
        $handler($response, new Request());
    }
}

<?php

use PHPUnit\Framework\TestCase,
    Common\NotFoundController,
    Common\Response;

class NotFoundControllerTest extends TestCase
{
    public function testSetResponse()
    {
        $responseMock      = $this->createMock(Response::class);
        $setResponseReturn = (new NotFoundController())->setResponse($responseMock);

        $this->assertInstanceOf(NotFoundController::class, $setResponseReturn);
    }

    public function testResponse()
    {
        $responseMock = $this->createMock(Response::class);

        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(404, ['message' => 'Route doesn\'t exist']);

        $response = (new NotFoundController())
            ->setResponse($responseMock)
            ->response();
    }
}

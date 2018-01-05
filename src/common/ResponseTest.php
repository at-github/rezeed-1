<?php

use PHPUnit\Framework\TestCase,
    Common\Response;

class ResponseTest extends TestCase
{
    static public function setupBeforeClass()
    {
        $_SERVER['SERVER_PROTOCOL'] = null;
    }

    /**
     * @dataProvider jsonProvider
     * @runInSeparateProcess because json method use header
     */
    public function testJson($expect, $status, $message)
    {
        ob_start();
        Response::json($status, $message);
        $output = ob_get_clean();

        $this->assertSame($expect, $output);
    }

    public function jsonProvider(){
        return [
            404 => ['{"message":"not found"}', 404, 'not found'],
            200 => ['{"message":"fine"}',      200, 'fine']
        ];
    }
}

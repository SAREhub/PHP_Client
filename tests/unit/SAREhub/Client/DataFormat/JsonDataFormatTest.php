<?php

namespace SAREhub\Client\DataFormat;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicExchange;
use SAREhub\Client\Message\BasicMessage;

class JsonDataFormatTest extends TestCase
{

    /**
     * @var DataFormat
     */
    private $dataFormat;

    protected function setUp()
    {
        $this->dataFormat = new JsonDataFormat();
    }

    public function testMarshalThenExchangeOutBody()
    {
        $data = ['param1' => 1, 'param2' => 2];
        $marshaled = $this->dataFormat->marshal($this->createExchange($data));
        $this->assertEquals(json_encode($data), $marshaled);
    }

    public function testUnmarshalThenExchangeOutBody()
    {
        $data = ['param1' => 1, 'param2' => 2];
        $unmarshaled = $this->dataFormat->unmarshal($this->createExchange(json_encode($data)));
        $this->assertEquals($data, $unmarshaled);
    }

    private function createExchange($inData)
    {
        return BasicExchange::newInstance()
            ->setIn(BasicMessage::newInstance()
                ->setBody($inData));
    }
}

<?php

namespace SAREhub\Client\Util;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Message\BasicMessage;

class DefaultJsonLogFormatterTest extends TestCase
{

    /**
     * @var DefaultJsonLogFormatter
     */
    private $formatter;

    public function setUp()
    {
        $this->formatter = new DefaultJsonLogFormatter();
    }

    public function testNormalizeDateTime()
    {
        $dateTime = new \DateTime();
        $expectedNormalized = $dateTime->format(DefaultJsonLogFormatter::DATE_TIME_FORMAT);
        $this->assertEquals($expectedNormalized, $this->formatter->normalizeDateTime($dateTime));
    }

    public function testNormalizeObjectWhenImplementsJsonSerializable()
    {
        $object = BasicMessage::newInstance();
        $this->assertSame($object, $this->formatter->normalizeObject($object));
    }

    public function testNormalizeObjectWhenIsThrowable()
    {
        $object = new \Exception();
        $this->assertSame($object, $this->formatter->normalizeObject($object));
    }

    public function testNormalizeObjectWhenHasToStringMethod()
    {
        $object = new ClassWithToStringMethod();
        $this->assertEquals((string)$object, $this->formatter->normalizeObject($object));
    }

    public function testNormalizeObjectWhenIsNotJsonSerializable()
    {
        $object = new NotJsonSerializable();
        $expectedNormalized = "object of class: " . get_class($object) . " can't be serialized to json";
        $this->assertEquals($expectedNormalized, $this->formatter->normalizeObject($object));
    }

    public function testFormatWhenJsonSerializableInContext()
    {
        $object = new ClassWithJsonSerializable();
        $record = $this->createLogRecord(["object" => $object]);
        $expected = [
            "message" => $record["message"],
            "datetime" => $record["datetime"]->format(DefaultJsonLogFormatter::DATE_TIME_FORMAT),
            "context" => [
                "object" => $object
            ]
        ];

        $this->assertFormattedRecord($expected, $record);
    }

    public function testFormatWhenThrowableInContext()
    {
        $object = new \Exception();
        $record = $this->createLogRecord(["object" => $object]);
        $expected = [
            "message" => $record["message"],
            "datetime" => $record["datetime"]->format(DefaultJsonLogFormatter::DATE_TIME_FORMAT),
            "context" => [
                "object" => $this->formatter->normalizeException($object)
            ]
        ];

        $this->assertFormattedRecord($expected, $record);
    }

    public function testFormatWhenObjectWithToStringMethodButNotJsonSerializableInContext()
    {
        $object = new ClassWithToStringMethod();
        $record = $this->createLogRecord(["object" => $object]);
        $expected = [
            "message" => $record["message"],
            "datetime" => $record["datetime"]->format(DefaultJsonLogFormatter::DATE_TIME_FORMAT),
            "context" => [
                "object" => (string)$object
            ]
        ];

        $this->assertFormattedRecord($expected, $record);
    }

    public function testFormatWhenObjectNotJsonSerializableInContext()
    {
        $object = new NotJsonSerializable();
        $record = $this->createLogRecord(["object" => $object]);
        $expected = [
            "message" => $record["message"],
            "datetime" => $record["datetime"]->format(DefaultJsonLogFormatter::DATE_TIME_FORMAT),
            "context" => [
                "object" => "object of class: " . get_class($object) . " can't be serialized to json"
            ]
        ];

        $this->assertFormattedRecord($expected, $record);
    }

    private function assertFormattedRecord(array $expected, array $record)
    {
        $this->assertJsonStringEqualsJsonString(json_encode($expected), $this->formatter->format($record));
    }

    private function createLogRecord(array $context)
    {
        return [
            "message" => "test",
            "datetime" => new \DateTime(),
            "context" => $context
        ];
    }
}

class ClassWithToStringMethod
{

    public function __toString()
    {
        return "to_string_method_output";
    }

}

class NotJsonSerializable
{

}

class ClassWithJsonSerializable implements \JsonSerializable
{

    public function jsonSerialize()
    {
        return "jsonSerialized";
    }
}

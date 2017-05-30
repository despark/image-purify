<?php


namespace Despark\Tests\ImagePurify\Classes\Commands;


use Despark\ImagePurify\Commands\PngQuant;
use Despark\Tests\ImagePurify\TestCase;
use Mockery\Mock;

class PngQuantTest extends TestCase
{
    public function testGetArguments()
    {
        /** @var PngQuant|Mock $mock */
        $mock = \Mockery::mock(PngQuant::class, ['test', 'test.png'])->makePartial();

        $expected = ["--output='test.png'"];
        $arguments = $mock->getArguments();
        $this->assertEquals($expected, $arguments);

        $mock->setArguments(["--output=other.png"]);
        $expected = ["'--output=other.png'"];
        $arguments = $mock->getArguments();
        $this->assertEquals($expected, $arguments);
    }

}
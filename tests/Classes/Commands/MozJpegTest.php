<?php


namespace Despark\Tests\ImagePurify\Classes\Commands;


use Despark\ImagePurify\Commands\MozJpeg;
use Despark\ImagePurify\Exceptions\CommandException;
use Despark\Tests\ImagePurify\TestCase;
use Mockery\Mock;

class MozJpegTest extends TestCase
{

    /**
     *
     */
    public function testBuildCommand()
    {
        /** @var Mock|MozJpeg $mock */
        $mock = \Mockery::mock(MozJpeg::class, ['cjpeg', 'file.png'])
                        ->shouldAllowMockingProtectedMethods()
                        ->makePartial();

        $mock->shouldReceive('getSpongeBin')->andReturn('sponge');
        $mock->shouldReceive('getBin')->andReturn('cjpeg');

        $mock->setArguments(['-optimize']);

        $command = $mock->buildCommand();

        $this->assertEquals("cjpeg '-optimize' | sponge file.png", $command);

        // Test without sponge bin
        $mock = \Mockery::mock(MozJpeg::class, ['cjpeg', 'file.png'])
                        ->shouldAllowMockingProtectedMethods()
                        ->makePartial();

        $mock->shouldReceive('getSpongeBin')->andReturn('');

        $this->expectException(CommandException::class);
        $mock->buildCommand();
    }

}
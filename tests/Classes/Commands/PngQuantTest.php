<?php


namespace Despark\Tests\ImagePurify\Classes\Commands;


use Despark\ImagePurify\Commands\PngQuant;
use Despark\ImagePurify\Exceptions\CommandException;
use Despark\Tests\ImagePurify\TestCase;
use Mockery\Mock;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class PngQuantTest.
 */
class PngQuantTest extends TestCase
{
    /**
     * @var PngQuant|Mock
     */
    protected $mock;


    /**
     * Test set up.
     */
    public function setUp()
    {
        $this->mock = \Mockery::mock(PngQuant::class, ['test', 'test.png'])->makePartial();
    }

    /**
     * @group png
     */
    public function testExecuteThrow()
    {
        $this->mock->shouldReceive('getProcess')->andReturnUsing(function ($command) {
            $process = \Mockery::mock(Process::class, [null => null])->shouldIgnoreMissing();

            $process->shouldReceive('getExitCode')->andReturn(98);

            $process->shouldReceive('mustRun')->andThrow(new ProcessFailedException($process));

            return $process;
        });

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('File probably already optimized');

        $this->mock->execute();
    }


    /**
     * @group png
     */
    public function testExecute()
    {
        $this->mock->shouldReceive('getProcess')->andReturn($this->getProcessMock());

        $this->mock->execute();
    }

    /**
     * @group png
     */
    public function testBuildCommand()
    {
        $this->mock->shouldReceive('getBin')->andReturn('test');

        $this->mock->setArguments(['-f', '--skip-if-larger', '--strip']);

        $expected = "test '-f' '--skip-if-larger' '--strip' --output 'test.png' -- 'test.png'";

        $this->assertEquals($expected, $this->mock->buildCommand());
    }

    /**
     * @group png
     */
    public function testGetArguments()
    {
        $expected = ["--output 'test.png'"];
        $arguments = $this->mock->getArguments();
        $this->assertEquals($expected, $arguments);

        $this->mock->setArguments(["--output=other.png"]);
        $expected = ["'--output=other.png'"];
        $arguments = $this->mock->getArguments();
        $this->assertEquals($expected, $arguments);
    }

}
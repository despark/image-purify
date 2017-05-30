<?php


namespace Despark\Tests\ImagePurify\Classes\Commands;


use Despark\ImagePurify\Commands\Command;
use Despark\ImagePurify\Exceptions\CommandException;
use Despark\Tests\ImagePurify\TestCase;
use Mockery\Mock;
use Symfony\Component\Process\Process;

class CommandTest extends TestCase
{
    /**
     * @var Mock|Command
     */
    protected $mock;

    /**
     * Setu method
     */
    protected function setUp()
    {
        $this->mock = \Mockery::mock(Command::class, ['test', 'testSourceFile.png'])->makePartial();
    }

    /**
     * @group class
     */
    public function testGetBin()
    {
        /** @var Mock|Command $class */
        $class = \Mockery::mock(Command::class, ['somenonexistentcommand', 'testSourceFile.png'])->makePartial();

        $this->expectException(CommandException::class);
        $this->expectExceptionMessageRegExp('/Binary (\w+?) is not executable/');
        $class->getBin();

        $bin = realpath(__DIR__.'/../resources/test_bash.sh');
        $class = \Mockery::mock(Command::class, [$bin])->makePartial();

        $this->assertEquals($bin, $class->getBin());


    }

    /**
     * @group class
     * @covers \Despark\ImagePurify\Commands\Command::setRawArguments
     * @covers \Despark\ImagePurify\Commands\Command::setArguments
     * @covers \Despark\ImagePurify\Commands\Command::getArguments
     * @covers \Despark\ImagePurify\Commands\Command::processArgument
     */
    public function testGetArguments()
    {
        $this->mock->setArguments(['argument 1', 'argument 2']);

        $this->mock->setRawArguments(['-raw arg1', 'raw arg2']);

        $expected = [
            "'argument 1'",
            "'argument 2'",
            "-raw arg1",
            "raw arg2",
        ];


        $args = $this->mock->getArguments();

        $this->assertEquals($expected, $args);
    }

    /**
     * @group class
     */
    public function testExecute()
    {
        $this->mock->setArguments(['arg1']);

        $this->mock->shouldReceive('getProcess')->andReturnUsing(function ($command) {
            $this->assertNotFalse(strstr($command, "test 'arg1'"));

            return $this->getProcessMock();
        });

        $this->mock->execute();
    }

    /**
     * @group class
     */
    public function testExecuteException()
    {
        $this->mock->setArguments(['arg1']);

        $this->mock->shouldReceive('getProcess')->andReturnUsing(function ($command) {
            $this->assertNotFalse(strstr($command, "test 'arg1'"));
            $process = \Mockery::mock(Process::class, [null => null])->shouldIgnoreMissing();

            $process->shouldReceive('mustRun')->andThrow(new \Exception());

            return $process;
        });

        $this->expectException(CommandException::class);

        $this->mock->execute();
    }

    /**
     * @group class
     */
    public function testGetArgument()
    {
        $this->mock->setArguments([1, 2, 3]);

        $this->assertEquals(1, $this->mock->getArgument(0));
        $this->assertEquals(2, $this->mock->getArgument(1));
        $this->assertEquals(3, $this->mock->getArgument(2));
    }

    /**
     * @group class
     */
    public function testGetProcess()
    {
        $return = $this->mock->getProcess('test');

        $this->assertEquals(Process::class, get_class($return));
    }

    /**
     * @group class
     */
    public function testGetFiles()
    {
        $class = new Command('test');
        $class->setSourceFile('testInFile');
        $this->assertEquals("testInFile", $class->getOutFile());
        $this->assertEquals("testInFile", $class->getSourceFile());

        $class = new Command('test');
        $class->setOutFile('testOutFile');
        $this->assertEquals("testOutFile", $class->getOutFile());
    }

    /**
     * @group class
     */
    public function testBuildCommand()
    {
        $this->mock->setOutFile('testOutFile.jpg');

        $this->mock->shouldReceive('getBin')->andReturn('test');

        $this->mock->setArguments(['arg1', 'arg2']);

        $command = $this->mock->buildCommand();

        $this->assertEquals("test 'arg1' 'arg2' 'testSourceFile.png'", $command);
    }

}
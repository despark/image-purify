<?php


namespace Despark\Tests\ImagePurify;


use Despark\ImagePurify\Interfaces\CommandInterface;
use Mockery\Expectation;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @return Expectation
     */
    protected function getExecutableTestCommand()
    {
        return $this->mockCommand()
                    ->shouldReceive('execute')
                    ->once()
                    ->shouldReceive('setSourceFile')
                    ->once();
    }


    /**
     * @return \Mockery\MockInterface|CommandInterface
     */
    protected function mockCommand()
    {
        return \Mockery::mock(CommandInterface::class);
    }
}
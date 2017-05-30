<?php


namespace Despark\Tests\ImagePurify\Classes\Commands;


use Despark\ImagePurify\Commands\MozJpeg;
use Despark\Tests\ImagePurify\TestCase;
use Mockery\Mock;
use phpmock\mockery\PHPMockery;

/**
 * Class MozJpegTest.
 */
class MozJpegTest extends TestCase
{

    /**
     * @var Mock|MozJpeg
     */
    protected $mock;

    /**
     * Set up test.
     */
    protected function setUp()
    {
        $this->mock = \Mockery::mock(MozJpeg::class, ['test', 'test.jpg'])->makePartial();
    }


    /**
     * @group jpeg
     */
    public function testExecute()
    {
        PHPMockery::mock('Despark\ImagePurify\Commands', "rename")->andReturn(true);

        $processMock = $this->getProcessMock();

        $this->mock->shouldReceive('getProcess')->andReturn($processMock);

        $this->mock->execute();
    }

    /**
     * @group jpeg
     */
    public function testGetArguments()
    {
        $this->mock->setArguments(['-outfile=test.jpg']);
        $arguments = $this->mock->getArguments();
        $expected = ["'-outfile=test.jpg'"];
        $this->assertEquals($expected, $arguments);

        $expected = ["-outfile 'other.jpeg'"];
        $this->mock->setArguments([]);
        $this->mock->setOutFile('other.jpeg');
        $arguments = $this->mock->getArguments();
        $this->assertEquals($expected, $arguments);
    }


}
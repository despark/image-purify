<?php


namespace Despark\Tests\ImagePurify\Classes\Commands;


use Despark\ImagePurify\Commands\MozJpeg;
use Despark\ImagePurify\Exceptions\CommandException;
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
        PHPMockery::mock('Despark\ImagePurify\Commands', "fileperms")->andReturn(0644);
        PHPMockery::mock('Despark\ImagePurify\Commands', "chmod")->andReturn(true);
        PHPMockery::mock('Despark\ImagePurify\Commands', "file_exists")->andReturn(true);
        PHPMockery::mock('Despark\ImagePurify\Commands', "filesize")->andReturn(100);

        $processMock = $this->getProcessMock();

        $this->mock->shouldReceive('getProcess')->andReturn($processMock);

        $this->mock->execute();
    }

    /**
     * @group jpeg
     */
    public function testFailedFile()
    {
        PHPMockery::mock('Despark\ImagePurify\Commands', "rename")->andReturn(true);
        PHPMockery::mock('Despark\ImagePurify\Commands', "fileperms")->andReturn(0644);
        PHPMockery::mock('Despark\ImagePurify\Commands', "chmod")->andReturn(true);
        PHPMockery::mock('Despark\ImagePurify\Commands', "file_exists")->andReturn(false);

        $processMock = $this->getProcessMock();

        $this->mock->shouldReceive('getProcess')->andReturn($processMock);

        $this->expectException(CommandException::class);

        $this->mock->execute();
    }

    /**
     * @group jpeg
     */
    public function testEmptyFile()
    {
        PHPMockery::mock('Despark\ImagePurify\Commands', "rename")->andReturn(true);
        PHPMockery::mock('Despark\ImagePurify\Commands', "fileperms")->andReturn(0644);
        PHPMockery::mock('Despark\ImagePurify\Commands', "chmod")->andReturn(true);
        PHPMockery::mock('Despark\ImagePurify\Commands', "file_exists")->andReturn(true);
        PHPMockery::mock('Despark\ImagePurify\Commands', "filesize")->andReturn(0);

        $processMock = $this->getProcessMock();

        $this->mock->shouldReceive('getProcess')->andReturn($processMock);

        $this->expectException(CommandException::class);

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
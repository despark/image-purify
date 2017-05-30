<?php


namespace Despark\Tests\ImagePurify\Classes\Chains;


use Despark\ImagePurify\Chains\ChainAbstract;
use Despark\ImagePurify\Chains\GifChain;
use Despark\ImagePurify\Chains\JpegChain;
use Despark\ImagePurify\Chains\PngChain;
use Despark\Tests\ImagePurify\TestCase;
use Mockery\Mock;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ChainsTest extends TestCase
{

    /**
     * @var Mock|ChainAbstract
     */
    protected $mock;

    protected function setUp()
    {
        $this->mock = \Mockery::mock(ChainAbstract::class)->makePartial();
    }

    /**
     * @group class
     */
    public function testAddGetSetCommand()
    {
        // Create dummy command
        $command1 = $this->mockCommand();
        $this->mock->addCommand($command1);

        $command2 = $this->mockCommand();
        $this->mock->addCommand($command2);

        $commands = $this->mock->getCommands();

        $this->assertEquals(2, count($commands));

        // Clear the commands.
        $this->mock->setCommands([$command1]);
        $commands = $this->mock->getCommands();
        $this->assertEquals(1, count($commands));

    }

    /**
     * @group class
     */
    public function testPurifyFirstOnlyDisabled()
    {
        $this->mock->shouldReceive('canHandle')->andReturn(true);
        /*
         * test default first only
         */
        $this->assertFalse($this->mock->isExecuteFirstOnly());

        $command1 = $this->getExecutableTestCommand()->getMock();

        $command2 = $this->getExecutableTestCommand()->getMock();
        
        $this->mock->setCommands([$command1, $command2]);

        $this->mock->purify('path');
    }

    /**
     * @group class
     */
    public function testPurifyFirstOnlyEnabled()
    {
        $this->mock->shouldReceive('canHandle')->andReturn(true);
        $this->mock->executeFirstOnly(true);
        $this->assertTrue($this->mock->isExecuteFirstOnly());

        $command1 = $this->getExecutableTestCommand()->getMock();

        $command2 = $this->mockCommand()
                         ->shouldNotReceive('execute')
                         ->getMock();

        $this->mock->setCommands([$command1, $command2]);

        $this->mock->purify('path');
    }

    /**
     * @group class
     */
    public function testCanHandle()
    {
        $class = new JpegChain();

        $this->assertTrue($class->canHandle(__DIR__.'/../../resources/jpeg.jpg'));
        $this->assertFalse($class->canHandle(__DIR__.'/../../resources/png.png'));
        $this->assertFalse($class->canHandle(__DIR__.'/../../resources/gif.gif'));

        $class = new PngChain();

        $this->assertFalse($class->canHandle(__DIR__.'/../../resources/jpeg.jpg'));
        $this->assertTrue($class->canHandle(__DIR__.'/../../resources/png.png'));
        $this->assertFalse($class->canHandle(__DIR__.'/../../resources/gif.gif'));

        $class = new GifChain();

        $this->assertFalse($class->canHandle(__DIR__.'/../../resources/jpeg.jpg'));
        $this->assertFalse($class->canHandle(__DIR__.'/../../resources/png.png'));
        $this->assertTrue($class->canHandle(__DIR__.'/../../resources/gif.gif'));
    }

    /**
     * @group class
     */
    public function testGetLogger()
    {
        $this->assertEquals(NullLogger::class, get_class($this->mock->getLogger()));

        $logger = \Mockery::mock(LoggerInterface::class);

        /** @var ChainAbstract $class */
        $class = \Mockery::mock(ChainAbstract::class, [$logger])->makePartial();

        $this->assertEquals(get_class($logger), get_class($class->getLogger()));
    }
}
<?php


namespace Despark\Tests\ImagePurify\Classes;


use Despark\ImagePurify\Chains\ChainAbstract;
use Despark\ImagePurify\Chains\JpegChain;
use Despark\ImagePurify\Chains\PngChain;
use Despark\ImagePurify\Exceptions\CommandException;
use Despark\ImagePurify\ImagePurifier;
use Despark\ImagePurify\Interfaces\CommandInterface;
use Despark\Tests\ImagePurify\TestCase;
use Mockery\Mock;
use Psr\Log\LoggerInterface;

/**
 * Class PurifierChainsTest.
 */
class ImagePurifierTest extends TestCase
{

    /**
     *
     */
    public function testPurify()
    {
        $purifier = new ImagePurifier();

        $logger = \Mockery::mock(LoggerInterface::class)->shouldReceive('warning')->once()->getMock();

        /** @var ChainAbstract|Mock $chain */
        $chain = \Mockery::mock(ChainAbstract::class, [$logger])->makePartial();
        $chain->shouldReceive('canHandle')->andReturn('true');

        $command1 = $this->getExecutableTestCommand()->getMock();
        $command2 = $this->getExecutableTestCommand()->getMock();

        $chain->setCommands([$command1, $command2]);

        $purifier->setChains([$chain]);

        $purifier->purify('path');

        // Create chain with one failing execute
        $failingCommand = \Mockery::mock(CommandInterface::class)
                                  ->shouldReceive('setSourceFile')
                                  ->once()
                                  ->shouldReceive('execute')
                                  ->once()
                                  ->andThrow(new CommandException)->getMock();

        $chain->setCommands([$failingCommand]);

        $purifier->setSuppressErrors(true);

        $purifier->purify('path');

    }

    /**
     * @group class
     */
    public function testPurifierChainTrait()
    {
        $chains = new ImagePurifier();

        $jpegChain = new JpegChain();
        $pngChain = new PngChain();

        $chains->addChain($jpegChain);

        $this->assertEquals(1, count($chains->getChains()));

        $this->assertNotNull($chains->getChainByClass(JpegChain::class));
        $this->assertNull($chains->getChainByClass(PngChain::class));

        $chains->setChains([$jpegChain, $pngChain]);

        $this->assertEquals(2, count($chains->getChains()));

        $this->assertNotNull($chains->getChainByClass(PngChain::class));
    }


}
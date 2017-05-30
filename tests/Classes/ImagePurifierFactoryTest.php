<?php


namespace Despark\Tests\ImagePurify\Classes;


use Despark\ImagePurify\Chains\JpegChain;
use Despark\ImagePurify\Commands\Command;
use Despark\ImagePurify\Commands\MozJpeg;
use Despark\ImagePurify\Exceptions\PurifyException;
use Despark\ImagePurify\ImagePurifierFactory;
use Despark\Tests\ImagePurify\TestCase;

/**
 * Class ImagePurifierFactoryTest.
 */
class ImagePurifierFactoryTest extends TestCase
{

    /**
     * @group class
     */
    public function testCreate()
    {
        $factory = new ImagePurifierFactory();

        $purifier = $factory->create();

        $this->assertFalse($purifier->suppressErrors());

        $chains = $purifier->getChains();

        $this->assertCount(3, $chains);

        $chain = reset($chains);

        $commands = $chain->getCommands();

        $this->assertCount(1, $commands);

        $command = reset($commands);

        $this->assertEquals(MozJpeg::class, get_class($command));

    }

    /**
     * @group class
     */
    public function testGetOptions()
    {
        $factory = new ImagePurifierFactory(['chains' => [], 'suppress_errors' => true]);
        $this->assertEquals(['chains' => [], 'suppress_errors' => true], $factory->getOptions());
    }

    /**
     *
     */
    public function testMissingCustomClass()
    {
        $options = [
            'chains' => [
                JpegChain::class => [
                    'commands' => [
                        [
                            'bin' => 'test',
                            'customClass' => 'nonExisting',
                        ],
                    ],
                ],
            ],
        ];

        $factory = new ImagePurifierFactory($options);

        $this->expectException(PurifyException::class);

        $factory->create();
    }


    /**
     *
     */
    public function testDefaultClass()
    {
        $options = [
            'chains' => [
                JpegChain::class => [
                    'commands' => [
                        [
                            'bin' => 'test',
                        ],
                    ],
                ],
            ],
        ];

        $factory = new ImagePurifierFactory($options);

        $purifier = $factory->create();

        $chains = $purifier->getChains();

        $chain = reset($chains);

        $commands = $chain->getCommands();

        $command = reset($commands);

        $this->assertEquals(Command::class, get_class($command));
    }

    /**
     *
     */
    public function testWrongCustomClass()
    {
        $options = [
            'chains' => [
                JpegChain::class => [
                    'commands' => [
                        [
                            'bin' => 'test',
                            'customClass' => static::class,
                        ],
                    ],
                ],
            ],
        ];

        $factory = new ImagePurifierFactory($options);

        $this->expectException(PurifyException::class);

        $factory->create();
    }

    /**
     *
     */
    public function testWrongChainClass()
    {
        $options = [
            'chains' => [
                self::class => [],
            ],
        ];

        $factory = new ImagePurifierFactory($options);

        $this->expectException(PurifyException::class);

        $factory->create();
    }

    /**
     *
     */
    public function testWrongCommandArray()
    {
        $options = [
            'chains' => [
                JpegChain::class => [
                ],
            ],
        ];

        $factory = new ImagePurifierFactory($options);

        $this->expectException(PurifyException::class);

        $factory->create();
    }

    /**
     *
     */
    public function testCommandNotAnArray()
    {
        $options = [
            'chains' => [
                JpegChain::class => [
                    'commands' => 'notarray',
                ],
            ],
        ];

        $factory = new ImagePurifierFactory($options);

        $this->expectException(PurifyException::class);

        $factory->create();
    }

    /**
     *
     */
    public function testMissingBin()
    {
        $options = [
            'chains' => [
                JpegChain::class => [
                    'commands' => [
                        ['arguments' => []],
                    ],
                ],
            ],
        ];

        $factory = new ImagePurifierFactory($options);

        $this->expectException(PurifyException::class);

        $factory->create();
    }


}
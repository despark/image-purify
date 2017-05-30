<?php


namespace Despark\ImagePurify;


use Despark\ImagePurify\Chains\GifChain;
use Despark\ImagePurify\Chains\JpegChain;
use Despark\ImagePurify\Chains\PngChain;
use Despark\ImagePurify\Commands\Command;
use Despark\ImagePurify\Commands\MozJpeg;
use Despark\ImagePurify\Commands\PngQuant;
use Despark\ImagePurify\Exceptions\PurifyException;
use Despark\ImagePurify\Interfaces\ChainInterface;
use Despark\ImagePurify\Interfaces\CommandInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImagePurifierFactory.
 */
class ImagePurifierFactory
{

    /**
     * @var array
     */
    protected $options;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ImagePurifierFactory constructor.
     * @param array $options
     */
    public function __construct(array $options = [], LoggerInterface $logger = null)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
        $this->logger = $logger;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'chains' => [
                JpegChain::class => [
                    'commands' => [
                        'mozJpeg' => [
                            'bin' => 'cjpeg',
                            'arguments' => ['-optimize', '-progressive'],
                            'customClass' => MozJpeg::class,
                        ],
                    ],
                    'first_only' => false,
                ],
                PngChain::class => [
                    'commands' => [
                        'pngQuant' => [
                            'bin' => 'pngquant',
                            'arguments' => ['-f', '--skip-if-larger', '--strip'],
                            'customClass' => PngQuant::class,
                        ],
                        'optiPng' => [
                            'bin' => 'optipng',
                            'arguments' => ['-i0', '-o2', '-quiet'],

                        ],
                    ],
                    'first_only' => false,
                ],
                GifChain::class => [
                    'commands' => [
                        'giflossy' => [
                            'bin' => 'gifsicle',
                            'arguments' => ['-b', '-O3', '--lossy=100', '--no-extensions'],
                        ],
                    ],
                ],
            ],
            'suppress_errors' => false,
        ]);

        $resolver->setRequired(['chains', 'suppress_errors']);
        $resolver->setAllowedTypes('chains', ['array']);
        $resolver->setAllowedTypes('suppress_errors', ['boolean']);

    }

    /**
     * @return ImagePurifier
     */
    public function create(): ImagePurifier
    {
        $purifier = new ImagePurifier();

        $purifier->setSuppressErrors($this->getOption('suppress_errors', false));

        $chainsOptions = $this->getOption('chains', []);

        $chains = $this->createChains($chainsOptions);

        $purifier->setChains($chains);

        return $purifier;
    }

    /**
     * @param array $options
     * @return ChainInterface[]
     * @throws PurifyException
     */
    public function createChains(array $options = []): array
    {
        $chains = [];
        foreach ($options as $chainClass => $properties) {
            if (! is_a($chainClass, ChainInterface::class, true)) {
                throw new PurifyException('Chain class must implement '.ChainInterface::class);
            }

            if (! isset($properties['commands'])) {
                throw new PurifyException('Missing commands for chain '.$chainClass);
            }
            $commandsOptions = $properties['commands'];
            if (! is_array($commandsOptions)) {
                throw new PurifyException('Commands not an array for chain '.$chainClass);
            }

            $commands = [];

            foreach ($commandsOptions as $commandName => $commandOption) {
                $commands[] = $this->createCommand($commandOption);
            }
            /** @var ChainInterface $chain */
            $chain = new $chainClass($this->logger);
            $chain->setCommands($commands);

            if (isset($properties['first_only']) && is_bool($properties['first_only'])) {
                $chain->executeFirstOnly($properties['first_only']);
            }

            $chains[] = $chain;
        }

        return $chains;
    }

    /**
     * @param $options
     * @return CommandInterface
     */
    public function createCommand($options): CommandInterface
    {
        $this->validateCommandOptions($options);

        if (isset($options['customClass'])) {
            $class = $options['customClass'];
            $this->validateCustomCommandClass($class);
        } else {
            $class = Command::class;
        }

        /** @var CommandInterface $command */
        $command = new $class($options['bin']);
        $arguments = $options['arguments'] ?? [];

        $command->setArguments($arguments);

        return $command;
    }

    /**
     * @param $options
     * @return bool
     * @throws PurifyException
     */
    protected function validateCommandOptions($options)
    {
        if (! isset($options['bin'])) {
            throw new PurifyException('Invalid options');
        }
    }

    /**
     * @param $class
     * @throws PurifyException
     */
    protected function validateCustomCommandClass($class)
    {
        if (! class_exists($class)) {
            throw new PurifyException('Command custom class ('.$class.') doesn\'t exist');
        }

        if (! is_a($class, CommandInterface::class, true)) {
            throw new PurifyException('Command must implement '.CommandInterface::class);
        }
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param      $key
     * @param null $default
     * @return mixed|null
     */
    public function getOption($key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }


}
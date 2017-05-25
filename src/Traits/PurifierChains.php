<?php


namespace Despark\ImagePurify\Traits;


use Despark\ImagePurify\Interfaces\ChainInterface;

/**
 * Class PurifierChains.
 */
trait PurifierChains
{

    /**
     * @var ChainInterface[]
     */
    protected $chains = [];

    /**
     * @param ChainInterface $chain
     */
    public function addChain(ChainInterface $chain)
    {
        $this->chains[get_class($chain)] = $chain;
    }

    /**
     * @return ChainInterface[]
     */
    public function getChains()
    {
        return $this->chains;
    }

    /**
     * @param array $chains
     */
    public function setChains(array $chains)
    {
        $this->chains = [];

        foreach ($chains as $chain) {
            $this->addChain($chain);
        }
    }

    /**
     * @param string $class
     * @return ChainInterface|null
     */
    public function getChainByClass(string $class)
    {
        return $this->chains[$class] ?? null;
    }

}
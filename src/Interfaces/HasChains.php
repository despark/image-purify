<?php


namespace Despark\ImagePurify\Interfaces;


/**
 * Interface HasChains
 * @package Despark\ImagePurify\Interfaces
 */
interface HasChains
{
    /**
     * @param ChainInterface $chain
     */
    public function addChain(ChainInterface $chain);

    /**
     * @return ChainInterface[]
     */
    public function getChains();

    /**
     * @param ChainInterface[] $chains
     */
    public function setChains(array $chains);

    /**
     * @param string $class
     * @return ChainInterface|null
     */
    public function getChainByClass(string $class);
}
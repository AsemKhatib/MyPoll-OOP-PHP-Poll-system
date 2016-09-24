<?php

namespace MyPoll\Classes;

/**
 * Class FeaturesAbstract
 * @package MyPoll\Classes
 */
abstract class FeaturesAbstract
{
    /** @var  Factory */
    protected $factory;

    /** @var int */
    protected $maxResults;

    /**
     * @param Factory $factory
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
        $this->maxResults = $this->factory->getSettingsObj()->getResultNumber();
    }

    abstract function add();

    abstract function addExecute();

    abstract function edit();

    abstract function editExecute();

    abstract function show();

    abstract function delete();

}
<?php

namespace MyPoll\Classes;

/**
 * Class FeaturesAbstract
 *
 * @package MyPoll\Classes
 */
abstract class FeaturesAbstract
{
    /**
     * @return string
     */
    abstract public function add();

    /**
     * @return array
     */
    abstract public function getPostParamsForAddMethod();

    /**
     * @return array
     */
    abstract public function getPostParamsForEditMethod();

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    abstract public function addExecute($paramsArray);

    /**
     * @param int $id
     *
     * @return string
     */
    abstract public function edit($id);

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    abstract public function editExecute($paramsArray);

    /**
     * @param int $startPage
     *
     * @return string
     */
    abstract public function show($startPage = 0);


    /**
     * @param int $id
     *
     * @return void
     */
    abstract public function delete($id);

}
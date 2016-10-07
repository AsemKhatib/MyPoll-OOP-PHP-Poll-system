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
     * @return mixed
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
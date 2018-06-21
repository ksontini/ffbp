<?php

namespace Tests;

class BaseTest
{

    private $_test;
    protected $f3;

    public function __construct($level=null, $f3)
    {
        //$f3->run();
        $this->f3 = $f3;
        $this->_test = new \Test($level);
        $this->setup();
    }

    protected function setup() {
        return true;
    }

    public function expect($cond, $text = NULL)
    {
        return $this->_test->expect($cond, $text);
    }

    public function results()
    {
        $result = $this->_test->results();

        foreach ($result as $r) {
            echo $r['text'] . "\n";
            if ($r['status'])
                echo "\033[32m " . 'Pass';
            else
                echo "\033[31m " . 'Fail (' . $r['source'] . ')';
            echo "\n";
        }

        return $result;
    }

    public function passed()
    {
        $isSuccess = $this->_test->passed();

        if ($isSuccess === false)
            echo "\033[31m " . get_class($this) . ": Test Failed \n";
        else
            echo "\033[32m " . get_class($this) . ": Test success \n";

        return $isSuccess;

    }


    public function execute()
    {
        $methods = get_class_methods(get_class($this));
        foreach ($methods as $method) {
            if (strpos($method, 'test')===0) {
                $this->{$method}();
            }
        }

        $this->results();
        $this->passed();
    }
}
<?php
namespace Services;


class BaseService
{
    /**
     * @var \Base
     */
    protected $f3;

    public function __construct($f3) {
        $this->f3 = $f3;
    }



}
<?php namespace fw__github__klude_org__epx_fw\sample;

class model {
    public static function _(...$args) { return new static(...$args); }
    public function prt(){
        echo __METHOD__;
    }
}
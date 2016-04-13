<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/4/13
 * Time: 9:36
 */
namespace Application\Test\Controller;

use System\Core\Cache;

class CacheTest {


    public function index(){


    }


    public function get(){
        Cache::using(0);
        dumpout(Cache::get('key01'));
    }

    public function del(){
        Cache::using(0);
        dumpout(Cache::delete('key01'));
    }

    public function set(){
        Cache::using(0);
        dumpout(Cache::set('key01',array('hello world'),30));
    }

}
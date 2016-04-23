<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/4/13
 * Time: 11:26
 */
$handler = new Memcache();
//$handler->addserver('192.168.200.174','11211');
$handler->addserver('localhost','11211');

echo '<pre>';
var_dump($handler->set('aaa',''));
<<<<<<< HEAD
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
=======
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
>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
var_dump($handler->set('aaa',''));
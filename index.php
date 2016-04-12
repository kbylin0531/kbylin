<?php
/**
 * User: linzh
 * Date: 2016/3/14
 * Time: 20:50
 */
const PAGE_TRACE_ON = true;
const DEBUG_MODE_ON = true;

include './System/Bylin.class.php';
(new Bylin())->liten(false)->inspect(false)->start();
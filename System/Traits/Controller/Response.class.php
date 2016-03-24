<?php
/**
 * Created by PhpStorm.
 * User: linzh
 * Date: 2016/3/8
 * Time: 20:38
 */
namespace System\Traits\Controller;
use System\Core\BylinException;
use System\Utils\SEK;
use System\Utils\XMLHelper;

trait Response {
    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param int $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     * @throws BylinException
     */
    protected function ajaxBack($data,$type=AJAX_JSON,$json_option=0) {
        SEK::cleanOutput();
        switch (strtoupper($type)){
            case AJAX_JSON :// 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data,$json_option));
            case AJAX_XML :// 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(XMLHelper::encodeHtml($data));
            default:
                throw new BylinException('Invalid output!');
        }
    }

}
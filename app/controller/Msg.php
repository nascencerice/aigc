<?php
// +----------------------------------------------------------------------
// | 文件: index.php
// +----------------------------------------------------------------------
// | 功能: 提供todo api接口
// +----------------------------------------------------------------------
// | 时间: 2021-11-15 16:20
// +----------------------------------------------------------------------
// | 作者: rangangwei<gangweiran@tencent.com>
// +----------------------------------------------------------------------

namespace app\controller;

use Error;
use Exception;
use app\model\Counters;
use think\response\Html;
use think\response\Json;
use think\facade\Log;
use think\facade\Request;


class Msg
{

    /**
     * 主页静态页面
     * @return Html
     */
    public function index(): Html
    {
        # html路径: ../view/index.html
        //return response(file_get_contents(dirname(dirname(__FILE__)).'/view/index.html'));
    }


    /**
     * 获取todo list
     * @return Json
     */
    public function getAll(): Json
    {
        // try {
        //     $data = (new Counters)->find(1);
        //     if ($data == null) {
        //         $count = 0;
        //     }else {
        //         $count = $data["count"];
        //     }
        //     $res = [
        //         "code" => 0,
        //         "data" =>  $count
        //     ];
        //     Log::write('getCount rsp: '.json_encode($res));
        //     return json($res);
        // } catch (Error $e) {
        //     $res = [
        //         "code" => -1,
        //         "data" => [],
        //         "errorMsg" => ("查询计数异常" . $e->getMessage())
        //     ];
        //     Log::write('getCount rsp: '.json_encode($res));
        //     return json($res);
        // }
        
        //参数
        //{"ToUserName":"gh_78591558a86c","FromUserName":"ob38cw-sjjr8LVz9oXEbcCyf1j38","CreateTime":1688055642,"MsgType":"text","Content":"444","MsgId":24167148576592230}
        
        
        
        $res = Request::param(false);
        Log::write('收到的参数: '.json_encode($res));

        Log::write('我的openid是: '.$_SERVER['X-WX-OPENID'] );

        $this->send($res['FromUserName'], $res['Content'] );
        
        
        return json(['success']);
        
        
        
        
    }


    /**
     * 请求三方处理
     * @param $action `string` 类型，枚举值，等于 `"inc"` 时，表示计数加一；等于 `"reset"` 时，表示计数重置（清零）
     * @return Json
     */
    protected function cc($url, $msg)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($msg));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        
        $ret = curl_exec($ch);
        Log::write('发送参数: '.json_encode($msg));
        Log::write('发送结果: ['.$ret.']' );
        curl_close($ch);
        
        
        
        
    }
    
    
    
    protected function send($openid, $content) {
        
        $msg = ['touser'=>$openid, 'msgtype'=>"text", 'text'=>['content'=>'你发送的内容是：'.$content]];
        
        $url = 'http://api.weixin.qq.com/cgi-bin/message/custom/send';
        
        $this->cc($url, $msg);
    }
}

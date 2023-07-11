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
use think\facade\Db;

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


        $content = '';
        //文本消息
        if($res['MsgType'] == 'text') {
            $content = $res['Content'];
        }else if($res['MsgType'] == 'voice') {
            $content = $res['Recognition'];
        }


        $url = "http://107.173.168.46/echo/".urlencode($content);
        $ret = $this->cg($url );
        $ret = json_decode($ret, true);
        
        $rrr = $this->cs($res['FromUserName'], $ret['result']);

        return json(['']);
        
        
        
        
    }

    
    public function send() {
        $res = Request::param(false);
        Log::write('msg-send-收到的参数: '.json_encode($res));

        $this->cs($res['openid'], $res['content'] );

        return json(['success']);
    }



    /**
     * 请求三方处理
     * @param $action `string` 类型，枚举值，等于 `"inc"` 时，表示计数加一；等于 `"reset"` 时，表示计数重置（清零）
     * @return Json
     */
    protected function cc($url, $msg)
    {
        $json_msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_msg );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_msg))
        );
        
        $ret = curl_exec($ch);
        //Log::write('发送参数: '.json_encode($msg));
        Log::write('发送结果: ['.$ret.']' );
        curl_close($ch);
        
        return $ret;
        
        
    }


    protected function cg($url)
    {
        Log::write("请求: {$url}" );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $ret = curl_exec($ch);
        //Log::write('发送参数: '.json_encode($msg));
        Log::write('发送结果: ['.$ret.']' );
        curl_close($ch);
        
        return $ret;
    }
    
    
    protected function cs($openid, $content) {
        
        $msg = ['touser'=>$openid, 'msgtype'=>"text", 'text'=>['content'=>$content]];
        
        $url = 'http://api.weixin.qq.com/cgi-bin/message/custom/send';
        
        $res = $this->cc($url, $msg);

        
        return $ret;
    }
}

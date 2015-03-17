<?php

class Base_Util_Common {
    /* 
     * Comment by chengyi: 
     * 内网IP段：
     * 10.x.x.x
     * 172.16.x.x至172.31.x.x
     * 192.168.x.x
     * */
    protected static function checkIsInnerIp($strIp){/*{{{*/
        if(preg_match('/^(10|172|192)\.(\d+)\./', $strIp, $matches) <= 0){
            return false;
        }

        if ('10' == $matches[1]){
            return true;
        }

        if ('172' == $matches[1] && ($matches[2] >= 16 && $matches[2] <= 31)){
            return true;
        }

        if ('192' == $matches[1] && '168' == $matches[2]){
            return true;
        }

        return false;
    }/*}}}*/

    /* 
     * Comment by chengyi: 
     * 获取客户端IP
     * 兼容内网ip识别
     *
     * 优先级：
     *     HTTP_CLIENT_IP外网IP > HTTP_X_FORWARDED_FOR外网IP > REMOTE_ADDR
     * */
    public static function getClientIp() {/*{{{*/
        $ip = '';
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode (",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) { 
                array_unshift($ips, $ip);  // 将HTTP_CLIENT_IP也插入到ips数组的开头，判断内网IP
                $ip = FALSE; 
            } 
            $cnt = 0;
            foreach($ips as $iptmp){
                $iptmp = trim($iptmp);
                if (!self::checkIsInnerIp($iptmp)){
                    $ip = $iptmp;
                    break;
                }
                // 防止过多ip段攻击
                if (++$cnt > 10){
                    break;
                }
            }
        }

        //ip is empty or not a real ip  
        //$_SERVER['REMOTE_ADDR'] cannot be modified by the user or via HTTP so you CAN trust it.
        if (empty($ip) || (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false
            && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)) {
            $ip = empty($_SERVER['REMOTE_ADDR'])? '127.0.0.1': $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }/*}}}*/

    /**
     * 生成请求唯一ID
     * 可作为与其他系统通信时的唯一ID、日志ID等，唯一标识一次请求。
     *
     * @return unsigned int32 requestid
     */
    public static function getReqID() {/*{{{*/
        static $reqID;
        if (!$reqID && array_key_exists('UC_NGX_LOGID', $_SERVER)) {
            $reqID = intval($_SERVER['UC_NGX_LOGID']);
        }
        if (!$reqID){
            $arr = gettimeofday();
            $reqID = mt_rand(1, 9999)+(((($arr['sec']*100000 + $arr['usec']/10) & 0x7FFFFFFF) | 0x80000000) - 10000);
        }
        return $reqID;
    }/*}}}*/

}

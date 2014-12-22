<?php
/*
 * http请求封装类
 */
class Http{
    // 是否post
    public static function isPost(){
      return $_SERVER['REQUEST_METHOD'] == 'POST'; 
    }
    // 是否get
    public static function isGet(){
      return $_SERVER['REQUEST_METHOD'] == 'GET'; 
    }
    /**
     * buildUrl  (http_build_query)
     * 拼接url  
     * @param string $baseURL   基于的url
     * @param array  $params    参数列表数组
     * @return string           返回拼接的url
     */
    public static function buildUrl($baseURL,$params=false){
        $url = $baseURL;
        if (!empty($params)) {
            $query = http_build_query($params);
            $url .= '?'.$query;
        }
        return $url;
    }
    /**
     * get
     * get方式请求资源
     * @param string $url     基于的baseUrl
     * @param array $headers    请求的headers头数组
     * @param array $params     请求的参数列表    
     * @param int $ssl          是否https请求
     * @return string         返回的资源内容
     */
    public static function get($url, $headers=false,$params=false,$ssl = false){
        $url = self::buildUrl($url, $params);
        // 整合header
        $_headers = array();
        if (!empty($headers)) {
            foreach ($headers as $_k => $_v) {
                $_headers[] = $_k.':'.$_v;
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl);//SSL证书认证
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers); // header头
        curl_setopt($ch, CURLOPT_HEADER, false); //启用时会将头文件的信息作为数据流输出。
        $result =  curl_exec($ch);
        // var_dump( curl_error($ch) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($ch);
        return $result;
    }
    /**
     * post
     * post方式请求资源
     * @param string $url       基于的baseUrl
     * @param array $headers    请求的headers头数组
     * @param array $params     请求的参数列表
     * @param array $$filepaths 请求的文件列表，文件路径的数组key代表post的key
     * @param int $ssl          是否https请求
     * @return string           返回的资源内容
     */
    public static function post($url,$headers=false, $params=false, $filePaths=false,$ssl = false){
        $ch = curl_init();
        // 整合post数据
        $_params = array();
        if (!empty($filePaths)) {
            foreach ($filePaths as $_k => $_v) {
                $_params[$_k] = '@'.$_v;
            }
        }
        if (!empty($params)) {
            $_params = array_merge($_params,$params);
        }
        $_params = empty($_params)?false:$_params;
        // 整合header
        //  Content-Type: application/x-www-form-urlencoded
        $_headers = array();
        if (!empty($headers)) {
            foreach ($headers as $_k => $_v) {
                $_headers[] = $_k.':'.$_v;
            }
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl);//SSL证书认证
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        // curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // 显示输出结果
        curl_setopt($ch, CURLOPT_POST, TRUE); // post传输数据
        // curl_setopt($ch, CURLOPT_HTTPGET, TRUE); // get传输数据
        curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers); // header头
        curl_setopt($ch, CURLOPT_HEADER, false); //启用时会将头文件的信息作为数据流输出。
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_params); // post传输的数据
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);
        // var_dump( curl_error($ch) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($ch);
        return $ret;
    }
}

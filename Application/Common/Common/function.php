<?php
//根程序文件
// 判断是否是在微信浏览器里
//author:panjie 3792535@qq.com
//是返回true,否返回false
function isWeixinBrowser() {
    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if (! strpos ( $agent, "icroMessenger" )) {
            return false;
    }
    return true;
}
/**
 * 获取用户id
 * todo:用session来实现 
 * @return [type]
 */
function get_user_id()
{
    return "3";
}
//向url上以POST方式提交数据
//author:panjie 3792535@qq.com
//@url 提交的地址
//@data POST的数据，格式为json
function http_post_json($url, $data) {  
    $ch = curl_init($url);
    $timeout = C('WXPAY_CRUL_TIMEOUT');//超时  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");//设置提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//添加提交数据
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过SSL验证
    //设置传输文件头
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
    $result = curl_exec($ch); //执行
    curl_close($ch);//关闭
    return $result;
 }  
 //向url上以GET方式提交数据
 //author:panjie 3792535@qq.com
//@url 提交的地址
 function http_get_data($url)
 {
    $ch = curl_init();  
    $timeout = 30;  
    curl_setopt ($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);  
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过SSL验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);  
    //$error = curl_error($ch);
    curl_close($ch);  
    return $result;  
 }
 /**
 * 把返回的数据集转换成Tree
 *
 * @param array $list
 *        	要转换的数据集
 * @param string $pid
 *        	parent标记字段
 * @param string $level
 *        	level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array ();
    if (is_array ( $list )) {
        // 创建基于主键的数组引用
        $refer = array ();
        foreach ( $list as $key => $data ) {
                $refer [$data [$pk]] = & $list [$key];
        }
        foreach ( $list as $key => $data ) {
            // 判断是否存在parent
            $parentId = $data [$pid];
            if ($root == $parentId) {
                $tree [] = & $list [$key];
            } else {
                if (isset ( $refer [$parentId] )) {
                    $parent = & $refer [$parentId];
                    $parent [$child] [] = & $list [$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 *
 * @param array $tree
 *        	原来的树
 * @param string $child
 *        	孩子节点的键
 * @param string $order
 *        	排序显示的键，一般是主键 升序排列
 * @param array $list
 *        	过渡用的中间数组，
 * @return array 返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list_think($tree, $child = '_child', $order = 'id', &$list = array()) {
	if (is_array ( $tree )) {
		$refer = array ();
		foreach ( $tree as $key => $value ) {
			$reffer = $value;
			if (isset ( $reffer [$child] )) {
				unset ( $reffer [$child] );
				tree_to_list_think ( $value [$child], $child, $order, $list );
			}
			$list [] = $reffer;
		}
		//$list = list_sort_by ( $list, $order, $sortby = 'asc' );
	}
	return $list;
}
/*
 * 将TREE转换为一行一行可以显示的LIST
 * level带表深度
 * 
 */

function tree_to_list($tree , $i = 0,$child = '_child',$level = '_level',$order='id', &$list = array()){
    if (is_array ( $tree )) {
        $refer = array ();
        //$tree = list_sort_by ( $tree, $order, $sortby = 'desc' );
        foreach ( $tree as $key => $value ) {
            $reffer = $value;
            $reffer[$level] = $i;  
            $i++;

            if (isset ( $reffer [$child] )) 
            {
                unset ( $reffer [$child] );
                $list [] = $reffer;
                tree_to_list ( $value [$child], $i, $child, $level ,$order ,$list );
            }     
            else
            {
                $list [] = $reffer;
            }
            $i--;
        }
        
    }
    return $list;
}

/**
* 对查询结果集进行排序
* @access public
* @param array $list 查询结果
* @param string $field 排序的字段名
* @param array $sortby 排序类型
* asc正向排序 desc逆向排序 nat自然排序
* @return array
*/
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}


//为数组中的值增加根路
//@$data 传入的一维数组，需要以/打头。
//author:panjie
//3792535@qq.com
//return 增加完根路径的数组
function addRootPath($data)
{
    foreach ($data as $key => $value) {
            $data[$key] = __ROOT__ . $value;
    }
    return $data;    
}
function add_root_path($data)
{
    if(is_array($data))
    {
        foreach ($data as $key => $value) {
            $data[$key] = __ROOT__ . $value;
        }
    }
    else
    {
        $data = __ROOT__ . $data;
    }
    return $data;    
}
function redirect_url($url)
{
    echo "<script language='javascript'>document.location = '" . $url . "'</script>";
}
//添加host前缀
function add_host($url)
{
    return I('server.REQUEST_SCHEME','http') . '://' . I('server.HTTP_HOST') . ':' . I('server.SERVER_PORT') . $url;
}
//如果字符串为空串，返回NULL，否则返回字符串信息
function trim_string($value)
{
    $ret = null;
    if (null != $value) 
    {
        $ret = $value;
        if (strlen($ret) == 0) 
        {
            $ret = null;
        }
    }
    return $ret;
}
/**
* 作用：产生随机字符串，不长于32位.
 * 输入大于0小于32的INT
 * 输出：指定位数的随机数,默认为32位
*/
function create_noncestr( $length = 32 ) 
{
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
    $str ="";
    for ( $i = 0; $i < $length; $i++ )  {  
        $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
    }  
    return $str;
}
/**
* 	作用：array转xml
*/
function array_to_xml($arr)
{
    $xml = "<xml>";
    foreach ($arr as $key=>$val)
    {
        if (is_numeric($val))
        {
            $xml.="<".$key.">".$val."</".$key.">"; 

        }
        else
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
    }
    $xml.="</xml>";
    return $xml; 
}

/**
 * 	作用：将xml转为array
 */
function xml_to_array($xml)
{		
//将XML转为array        
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
}

/**
*作用：以post方式提交xml到对应的接口url
 * @xml xml数据
 * @url 提交地址
 * @second 默认超时时长
*/
function post_xml_curl($xml,$url,$second=30)
{		
    //初始化curl        
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOP_TIMEOUT, $second);
    //这里设置代理，如果有的话
    //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
    //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //post提交方式
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    //运行curl
    $data = curl_exec($ch);
    curl_close($ch);
    //返回结果
    if($data)
    {
        curl_close($ch);
        return $data;
    }
    else 
    { 
        $error = curl_errno($ch);
        echo "curl出错，错误码:$error"."<br>"; 
        echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
        curl_close($ch);
        return false;
    }
}
/**
* 	作用：使用证书，以post方式提交xml到对应的接口url
*/
function post_xml_ssl_curl($xml,$url,$second=30)
{
    $ch = curl_init();
    //超时时间
    curl_setopt($ch,CURLOPT_TIMEOUT,$second);
    //这里设置代理，如果有的话
    //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
    //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    //设置header
    curl_setopt($ch,CURLOPT_HEADER,FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
    //设置证书
    //使用证书：cert 与 key 分别属于两个.pem文件
    //默认格式为PEM，可以注释
    curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
    curl_setopt($ch,CURLOPT_SSLCERT,add_root_path(C('WXPAY_SSLCERT_PATH')));
    //默认格式为PEM，可以注释
    curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
    curl_setopt($ch,CURLOPT_SSLKEY, add_root_path(C('WXPAY_SSLKEY_PATH')));
    //post提交方式
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
    $data = curl_exec($ch);
    //返回结果
    if($data){
        curl_close($ch);
        return $data;
    }
    else { 
        $error = curl_errno($ch);
        echo "curl出错，错误码:$error"."<br>"; 
        echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
        curl_close($ch);
        return false;
    }
}
// 获取access_token，自动带缓存功能
//先判断缓存中是否存在，存在直接输出，不存在则取出后输出
function get_access_token(){
    $key = 'access_token';
    $access_token = S( $key );
    if($access_token !== FALSE)
    {
        return $access_token;
    }
    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . C('WECHAT_APPID') . '&secret=' . C('WECHAT_SECRET');
    $tempArr = json_decode ( file_get_contents ( $url ), true );
    if (@array_key_exists ( 'access_token', $tempArr )) {
            S ( $key, $tempArr ['access_token'], 6000 );
            return $tempArr ['access_token'];
    } else {
            return 0;
    }
}
/*
 * 获取JSSDK使用的jsapiticket
 * 官方DEMO升级为THINKPHP缓存版本
 */
function get_jsapi_ticket() {
    $key = "jsapi_ticket";
    $jsapiTicket = S( $key );
    if($jsapiTicket != FALSE)
    {
        return $jsapiTicket;
    }
    $accessToken = get_access_token();
    $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
    $res = json_decode(http_get_data($url));
 //   var_dump($res);
    $ticket = $res->ticket;
    if ($ticket) {
        S ( $key, $ticket , 6000 );
    }
    return $ticket;
  }
// 通过openid获取微信用户基本信息,此功能只有认证的服务号才能用
function get_weichat_user_info($openid, $accessToken) {
	if (empty ( $accessToken )) {
		return false;
	}	
	$param2 ['access_token'] = $accessToken;
	$param2 ['openid'] = $openid;
	$param2 ['lang'] = 'zh_CN';
	
	$url = 'https://api.weixin.qq.com/cgi-bin/user/info?' . http_build_query ( $param2 );
	$content = file_get_contents ( $url );
	$content = json_decode ( $content, true );
	return $content;
}
/*
 * 通过OPENID来取用户的所有信息
 */
function get_customer_info($openId)
{
    if(strlen(trim($openId)) == 28)
    {
        $map['openid'] = $openId;
    }
    else
    {
        $map['id'] = $openId;
    }
    return M('Customer')->where($map)->find();
}
/*
 * 获取当前URL，为于微信答名或是跳转
 */
function get_current_url()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    return $url;
}
/*
 * 跳转至autho认证以获取当前用户的当前CODE
 * code作为换取access_token的票据，每次用户授权带上的code将不一样，code只能使用一次，5分钟未被使用自动过期。
 * 用于进行收货地址的管理
 */
function goto_auth($state = 0)
{
    $redirectUrl = 'http://%s%s';
    $redirectUrl = urlencode(sprintf($redirectUrl,I('server.SERVER_NAME'),I('server.REQUEST_URI')));
    $oauth = C('WECHAT_OAUTH');
    $appid = C('WECHAT_APPID');
    //$scode = 'snsapi_userinfo';
    $scode = 'snsapi_base';//只获取OPENID
    $url = sprintf($oauth,$appid,$redirectUrl,$scode,$state);    
    //跳转
    redirect_url($url);
}

/*
 * 获取包括用户access_token及openId等信息的数组
 */
function get_user_info($code)
{
    return get_user_token($code);
}
function get_user_token($code)
{
    if(session('userInfo') != null)
    {
        $userInfo = session('userInfo');
        if( (time() - $userInfo['make_time']) < $userInfo['expires_in'])
        {
            session('userInfo',$userInfo);
            return $userInfo;
        }
    }
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
    $appId = C("WECHAT_APPID");
    $secret = C("WECHAT_SECRET");
    $url = sprintf($url,$appId,$secret,$code);
    $ch=curl_init();
    $timeout=5;
    //设置URL和相应的选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    // 抓取URL,并获取内容
    $lines_string=curl_exec($ch);
    // 关闭cURL资源，并且释放系统资源
    curl_close($ch);
    //抓取内容转换
    $dataArr = json_decode($lines_string); 
    $userInfo = (array)$dataArr;
    if($userInfo['errcode'] == '')
    {
        $userInfo['make_time'] = time();
        //session 用户基本信息
        session('userInfo',$userInfo);
    }
    return $userInfo;
}

// 获取当前用户的OpenId
function get_openid($openid = NULL) {
//        return 'oZuoxt8tnEUUf6YPBG-mNPYjoKQA';
	$openid = session ('openid');
        $openidTime = session('openidTime');
        if($openid != false && $openidTime!=false && (time()-$openidTime < 60))
        {
            return $openid;
        }
        $code = I('code','');
	$isWeixinBrowser = isWeixinBrowser ();
	if ($isWeixinBrowser) {
            if( $openid == false && ($code == '' || $code == session('code')) )
            {
                goto_auth();
            }
            else
            {
                session('code',$code);//缓存code防止二次使用
                $userInfo = get_user_info($code);
                $openid = $userInfo['openid'];
                session('openid',$openid);
                session('openidTime',time());
                //将抓取到的信息存库
                $map['openid'] = $openid;
                $customer = M('Customer');
                $res = $customer->where($map)->find();
                if($res == false)
                {
                    $customer->data($userInfo)->add();
                }
                else
                {
                    $userInfo['id'] = $res['id'];
                    $customer->data($userInfo)->save();
                }
            }
	}	
	if (empty ( $openid )) {
		return false;
	}	
	return $openid;
}

 /**
* 	作用：生成签名
* @arr,要生成签名的数组
*  步骤：
* 1按字典序排序各参数(数组)
* 2按参数生成get信息
* 3.加入微信支付key值
* 4.MD5加密
* 5.小写转大写
* 6.输出
*/
function get_wechat_sign($arr)
{
    foreach ($arr as $k => $v)
    {
        $Parameters[$k] = $v;
    }
    //签名步骤一：按字典序排序参数
    ksort($Parameters);
    $String = formatBizQueryParaMap($Parameters, false);
    //echo '【string1】'.$String.'</br>';
    //签名步骤二：在string后加入KEY
    $String = $String."&key=".C('WXPAY_KEY');
    //echo "【string2】".$String."</br>";
    //签名步骤三：MD5加密
    $String = md5($String);
    //echo "【string3】 ".$String."</br>";
    //签名步骤四：所有字符转为大写
    $result_ = strtoupper($String);
    //echo "【result】 ".$result_."</br>";
    return $result_;
}

/**
* 	作用：格式化参数，签名过程需要使用
* 先按字典序进行排序，再拼接成get信息
* @paraMap 需要格式化的带有key的数组
* @urlencode 为真是表示进行unlencode转化，为假不转换
*/
function formatBizQueryParaMap($paraMap, $urlencode = true)
{
    $buff = "";
    ksort($paraMap);
    foreach ($paraMap as $k => $v)
    {
        if($urlencode)
        {
            $v = urlencode($v);
        }
        //$buff .= strtolower($k) . "=" . $v . "&";
        $buff .= $k . "=" . $v . "&";
    }
    $reqPar;
    if (strlen($buff) > 0) 
    {
        $reqPar = substr($buff, 0, strlen($buff)-1);
    }
    return $reqPar;
}
/*
 * 按关键字分类
 * 将包含关键字的数组
 * 增加一维
 * 示例:
 * 源有数组:
 * array(
 *      array(id=1,name=zhangsa,sex=1),
 *      array(id=2,name=lisi,sex=1),
 *      array(id=3,name=xiaohong,sex=4),
 *      array(id=4,name=zhangmei,sex=4)
 * )
 * @key = sex
 * 返回数组
 * array(
 *      1=>array(
 *          array(id=1,name=zhangsan,sex=1),
 *          array(id=2,name=lisi,sex=1)
 *      )
 *      4=>array(
 *          array(id=3,name=xiaohong,sex=4),
 *          array(id=4,name=zhangmei,sex=4)
 *      )
 * )
 */
function group_by_key($arr,$key)
{
    $resArr = array();
    foreach ($arr as $k => $v) {
        $resArr[$v[$key]][] = $v;
    }
    return $resArr;
}

function group_by_key1_key2($arr,$key1,$key2)
{
    $resArr = array();
    foreach ($arr as $k => $v) {
        $resArr[$v[$key1][$key2]][] = $v;
    }
    return $resArr;
    
    
}


/*
 * 格式化金钱函数
 * @money 344340 分
 * @return 3,443.40
 * 输出格式10,000.00
 * 实现思想
 * 1.是否位数小于3,否,前面补0.0返回
 * 2.是去除后两位
 * 3.判断字符长度是否大于0.否,近回0.
 * 4.字符进行反转
 * 5.加入逗号
 * 6.字符进行反转
 * 7.拼接分
 */
function format_money($money){
    $money = trim((string)$money);
    if(!isset($money) || empty($money) || $money == '' || $money == false || $money == null)
    {
        return '0.00';
    }
    if(strlen($money) < 3 )
    {
        if(strlen($money) < 2)
        {
            $res = '0.0' . $money;
        }
        else
        {
            $res = '0.' . $money;
        }
        return $res;
    }
    else
    {
        //取出分
        $xiaoshu = substr( $money, strlen($money) - 2,strlen($money) - 1 );
        //取出元
        $shishu = substr( $money, 0 ,strlen($money)-2 );
        //进行反转
        $tmp_money = strrev($shishu);     
        $format_money = '';//进行初始化
        if( strlen($shishu) > 3)
        {
            for($i = 3 ; $i < strlen($shishu) ; $i+=3 )
            {
                $format_money .= substr($tmp_money,0,3).",";
                $tmp_money = substr($tmp_money,3, strlen($tmp_money));
            }
            $format_money .= $tmp_money ;
        }
        else
        {
            $format_money = $tmp_money;            
        }
    } 
    $res = strrev($format_money) .  '.' . $xiaoshu;   
    return $res;
}
/*
 * 加密方法
 * 把用户名和密码进行拼接，然后采用sha1加密方法
 * 2015-4-28 19:34:26
 */
function ao_mi($username,$password){
    $message = $username.$password;
    return sha1($message);
}
/*
 * 通过openid获取到customer信息
 * @key = 'openid';
 * @input array(array('id'=>1,'openid'=>2),array('id'=>2,'openid'=>2))
 * @output array(array('id'=>1,array('openid'=>2,'id'=3)),array('id'=>2,array('openid'=>2,'id'=>2));
 * 
 */
function get_customer_info_by_openid($arr,$key = 'openid')
{
    $customer = M('Customer');
    foreach($arr as $k=>$v)
    {
        $map['openid'] = $arr[$k][$key];
        $arr[$k][$key] = $customer->where($map)->find();
    }
    return $arr;
}
/*
 * 通过客户id获取到customer信息
 * 输入输出基本同上
 */
function get_customer_info_by_id($arr,$key = 'customerId')
{
    $customer = M('Customer');
    foreach($arr as $k=>$v)
    {
        $map['id'] = $arr[$k][$key];
        $arr[$k][$key] = $customer->where($map)->find();
    }
    return $arr;
}
function get_all_source(){
    $model = M("Source");
    $source = $model ->select();
    return $source ;
}
function get_all_logistics(){
    $model = M("Logistics");
    $logistics = $model ->select();
    return $logistics;
}
/*去除较大数字中间的逗号，例如，金额，1000在前台显示1,000，
 * 传入从前台获取的格式化的数字
 * 返回经过处理的数字
存库的时候要去掉逗号并换算单位为分*/
function huansuan($price){
    $price = str_replace(',','',$price);//去除大的数字金额里的逗号
    $price = (float)$price;
    $price = (int)($price*100 + 0.5);
    return $price;
}

/*
 * 返回自动回复信息
 */
function get_return_word(){
    $model = M("config");
    $map['name']="SYSTEM_RETURN_WORD";
    $res = $model -> where($map) ->select();
    $word = $res[0]['value'];
    return $word;
}

/*
 * 改变数据组KEY的值
 */
function change_key_by_key1_key2($arr,$key1,$key2)
{
    $arrRes = array();
    foreach($arr as $k => $v)
    {
        $arrRes[$v[$key1][$key2]] = $v;
    }
    return $arrRes;
}
/*
 * 改变数据组KEY的值
 */
function change_key($arr,$key)
{
    $arrRes = array();
    foreach($arr as $k => $v)
    {
        $arrRes[$v[$key]] = $v;
    }
    return $arrRes;
}

/**
* 获取服务器端IP地址
 * @return string
 */
function get_server_ip() { 
    if (isset($_SERVER)) { 
        if($_SERVER['SERVER_ADDR']) {
            $server_ip = $_SERVER['SERVER_ADDR']; 
        } else { 
            $server_ip = $_SERVER['LOCAL_ADDR']; 
        } 
    } else { 
        $server_ip = getenv('SERVER_ADDR');
    } 
    return $server_ip; 
}
    
    /**
     * 将树形结构转化为list列表
     * @param type $tree 数组，要转化成List的树
     * @param type $i  树的层级
     * @param type $type  子集数组的下标
     * @return type 返回list列表
     * creat by pan
     */
    
function treeToList($tree,$i,$type){
    $list = array();
    foreach($tree as $key => $value)
    {
        $value['level'] = $i;
        $list[] = $value;
        if(is_array($value[$type]))
        {
            $i++;
            $list = array_merge($list,treeToList($value[$type],$i));
            $i--;
        }
    }
    return $list;
}
/**
 * [add_url 添加url信息]
 * @param [type] $array     [要添加的信息数组]
 * @param [type] $key1    [添加之后的数组下标]
 * @param [type] $url_array [添加的url数组,键为存的下标值为要跳转的url]
 *  * @param [type] $key2    [添加url的key]
 * xuao 295184686@qq.com
 */
function add_url($array,$key1,$url_array,$key2){
    $data = $array;
    $url = $url_array;
    foreach ($data as $key => $value) {
        foreach ($url_array as $k => $v) {
            $url[$k] = $v.'?'.$key2.'='.$value[$key2];
        }
        $data[$key][$key1] = $url;
    }
    return $data;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 * author:oneThink
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

/**
 * 将DATE字符串转换为INT类型
 * 例输入2015-09-15,则输出2015年9月15日0时的时间戳
 * @return int字符串
 */
function date_to_int($date , $connecter = '-')
{
    $date = trim($date);
    //查找分隔符的位置，如果小于2，则返回FALSE。
    if(!$firstPosition = strpos($date ,$connecter))
    {
        return $date;
    }

    //截取出年,如果是2位，则拼加20，如果即不是2位，也不是4位，flase
    $year = (int)substr($date , 0 , $firstPosition);
    if($year == 0 || $year > 9999)
    {
        return $date;
    }

    if($year < 100)
    {
        $year += 2000;
    }
    elseif($year < 1000)
    {
        return $date;
    }

    //截取月，如果等于0，或是大于12，返回$date    
    $secondPosition = strpos($date , $connecter , $firstPosition+1);
    $month = (int)substr($date , $firstPosition+1 , $secondPosition);
 
    if($month < 1 || $month > 12)
    {
        return $date;
    }
  
    //截取日，如果不大于0和小于31，则返回FLASE
    $day = (int)substr($date , $secondPosition+1);
    if($day < 1 || $day > 31)
    {
        return $date;
    }

    return mktime(0,0,0,$month,$day,$year);
}

/**
 * 将数组中，包括有特定尾缀的字符串，进行 时间转换
 * @param array $lists     = array(array('begin_time'=>'2015-09-15'));
 * @param  string $connecter 连续符，可知，我们不能时间化没有连接符号的
 * @param  后缀 $suffix    比如_time
 * @return arry            替换后返回
 */
function lists_date_format_to_int($lists , $connecter = '-' , $suffix = "date")
{
    foreach($lists as $key => $value)
    {
        $lastPosition = strrpos($key , '_');
        if( substr($key, $lastPosition+1) == $suffix)
        {
            $lists[$key] = date_to_int($value , $connecter);
        }
    }
    return $lists;
}

/**
 * 时间字符串格式化
 * @param  int $int 232213213    
 * @return [String]         [2015/09/15]
 */
function date_to_string($int , $param = 'Y-m-d')
{
    $int = (int)$int;
    if(!$int)
    {
        return date($param);
    }
    else
    {
        return date($param,$int);
    }
}
/**
 * 导出Excel报表
 *  @param  array $header excel的表头数组  
 *  @param  array $data excel的数据数组 
 */
function export_excel($header,$data){
        Vendor('Classes.PHPExcel');
        $excel = new \PHPExcel();
        $letter = array('A','B','C','D','E','F','F','G');
        //表头数组
        $tableheader = $header;
        //填充表头信息           
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }
        //表格数组
        $data = $data;
        //填充表格信息
        for ($i = 2;$i <= count($data) + 1;$i++) {
            $j = 0;
            foreach ($data[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }
        //创建Excel输入对象
        Vendor('Classes.PHPExcel.Writer.Excel5');
        $write = new \PHPExcel_Writer_Excel5($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="testdata.xls"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
        }
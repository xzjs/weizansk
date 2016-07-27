<?php
defined('IN_IA') or exit('Access Denied');
function set_code($url = '' , $type = '')
{
    global $_W, $_GPC, $code;
    load()->func('communication');
//    message('debug');
//    $domain = $_SERVER['HTTP_HOST'];
    $domain = get_domain();
    $ip = gethostbyname($domain);
    $result = ihttp_post($url, array('type' => $type, 'ip' => $ip, 'code' => $code, 'domain' => $domain));
    echo $result['content'];
}
function get_domain()
{
    $host = $_SERVER['HTTP_HOST'];
    $host = strtolower($host);
    if (strpos($host, '/') !== false) {
        $parse = @parse_url($host);
        $host = $parse['host'];
    }
    $topleveldomaindb = array('com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'pro', 'name', 'museum', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me');
    $str = '';
    foreach ($topleveldomaindb as $v) {
        $str .= ($str ? '|' : '') . $v;
    }
    $matchstr = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$";
    if (preg_match("/" . $matchstr . "/ies", $host, $matchs)) {
        $domain = $matchs['0'];
    } else {
        $domain = $host;
    }
    return $domain;
}

function authorization()
{
   $host = get_domain();
   return base64_encode($host);
}

function code_compare($a, $b)
{
    //if ($a != $b) {
    //    message(base64_decode("57O757uf5q2j5Zyo57u05oqk77yM6K+35oKo56iN5ZCO5YaN6K+V77yM5pyJ55aR6Zeu6K+36IGU57O757O757uf566h55CG5ZGYIQ=="));
    //}
}

function findNum($str=''){
    $str=trim($str);
    if(empty($str)){return '';}
    $reg='/(\d{3}(\.\d+)?)/is';//匹配数字的正则表达式
    preg_match_all($reg,$str,$result); if(is_array($result)&&!empty($result)&&!empty($result[1])&&!empty($result[1][0])){
        return $result[1][0];
    }
    return '';
}


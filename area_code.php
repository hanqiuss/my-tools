<?php
ini_set('memory_limit', '1G');
$provinceFile = 'province.json';
$cityFile = 'city.json';
$countyFile = 'county.json';
$townFile = 'town.json';
$villageFile = 'village.json';
$url = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2020/';
const SLEEP_TIME = 100000;
$province = [];
if(is_file($provinceFile)){
    $data = file_get_contents($provinceFile);
    $province = json_decode($data, true);
}
if(!$province){
    $province = getProvince();
    if(!$province){
        echo '获取省数据失败';
        exit(-1);
    }
    file_put_contents($provinceFile, json_encode($province, JSON_UNESCAPED_UNICODE));
}

$city = [];
if(is_file($cityFile)){
    $data = file_get_contents($cityFile);
    $city = json_decode($data, true);
}
if(!$city){
    $city = getCity();
    if(!$city){
        echo '获取市数据失败';
        exit(-1);
    }
    file_put_contents($cityFile, json_encode($city, JSON_UNESCAPED_UNICODE));
}

$county = [];
if(is_file($countyFile)){
    $data = file_get_contents($countyFile);
    $county = json_decode($data, true);
}
if(!$county){
    $county = getCounty();
    if(!$county){
        echo '获取区县数据失败';
        exit(-1);
    }
    file_put_contents($countyFile, json_encode($county, JSON_UNESCAPED_UNICODE));
}

$town = [];
if(is_file($townFile)){
    $data = file_get_contents($townFile);
    $town = json_decode($data, true);
}
if(!$town){
    $town = getTown();
    if(!$town){
        echo '获取乡镇数据失败';
        exit(-1);
    }
    file_put_contents($townFile, json_encode($town, JSON_UNESCAPED_UNICODE));
}

$village = [];
if(is_file($villageFile)){
    $data = file_get_contents($villageFile);
    $village = json_decode($data, true);
}
if(!$village){
    $village = getVillage();
    if(!$village){
        echo '获取社区数据失败';
        exit(-1);
    }
    file_put_contents($villageFile, json_encode($village, JSON_UNESCAPED_UNICODE));
}


function getVillage(){
    global $city,$town,$url;
    $village = [];
    $dir = 'tmp/village';
    if(!is_dir($dir)){
        mkdir($dir,0777,true);
    }
    $city_map = [];
    foreach ($town as $value){
        if($value['url'] != ''){
            $c_code = substr($value['code'], 0,4);
            $city_map[$c_code][] = $value;
        }
    }
    foreach ($city as $value){
        $arr = [];
        $file = $dir.'/' . $value['code'].'.json';
        if(is_file($file)){
            $str = file_get_contents($file);
            $arr = json_decode($str, true);
        }
        if(!$arr){
            echo $value['name'] . PHP_EOL;
            $c_code = substr($value['code'], 0,4);
            $list = $city_map[$c_code];
            foreach ($list as $k=>$v){
                usleep(SLEEP_TIME);
                $path = substr($v['code'], 0,6);
                $path = str_split($path,2);
                $path = implode('/',array_filter($path,fn($x)=>$x!='00'));
                $path = $path.'/'. substr($v['url'], 3);
                
                $s = httpGet($path);
                if(!$s){
                    $s = httpGet($path);
                }
                $s = mb_convert_encoding($s, 'UTF-8', 'gbk');
                preg_match("#<tr class='villagetr'>(.*)</tr>#", $s, $match);
                //$s = httpGet($path);
                //var_dump($match);exit;
                if ( !$match) {
                    var_dump($v,$s);
                    exit(-1);
                }
                $doc = new DOMDocument();
                $doc->loadHTML('<?xml version="1.0" encoding="utf-8"?>' . $match[0]);
                foreach ($doc->getElementsByTagName('tr') as $vv) {
                    $a = $vv->getElementsByTagName('td');
                    $village[] = $arr[] = [
                        'code' => $a[0]->textContent,
                        'name' => $a[2]->textContent,
                        'type' => $a[1]->textContent,
                    ];
                }
                unset($doc);
                echo $value['name'] . "\t".$k.'/'.count($list).PHP_EOL;
            }
            file_put_contents($file, json_encode($arr,JSON_UNESCAPED_UNICODE));
        }else{
            $village = [...$village,...$arr];
        }
    }
    
    return $village;
}

function getTown(){
    global $province ,$county,$url;
    $town = [];
    $dir = 'tmp/town';
    if(!is_dir($dir)){
        mkdir($dir,0777,true);
    }
    $pro_map = [];
    foreach ($county as $value){
        if($value['url'] != ''){
            $p_code = substr($value['code'], 0,2);
            $pro_map[$p_code][] = $value;
        }
    }
    foreach ($province as $pro){
        $arr = [];
        $file = $dir.'/' . $pro['code'].'.json';
        if(is_file($file)){
            $str = file_get_contents($file);
            $arr = json_decode($str, true);
        }
        if(!$arr){
            echo $pro['name'].PHP_EOL;
            $p_code = substr($pro['code'], 0,2);
            $list = $pro_map[$p_code];
            foreach ($list as $v){
                usleep(SLEEP_TIME);
                $path = $p_code.'/'. $v['url'];
                $s = httpGet($path);
                if(!$s){
                    $s = httpGet($path);
                }

                $s = mb_convert_encoding($s, 'UTF-8', 'gbk');
                preg_match("#<tr class='towntr'>(.*)</tr>#", $s, $match);
                if ( !$match) {
                    if(preg_match("#<tr class='villagetr'>(.*)</tr>#", $s, $match)){
                        $arr[] = $v;
                        continue;
                    }
                    var_dump($v,$s);
                    exit(-1);
                    continue;
                }
                $doc = new DOMDocument();
                $doc->loadHTML('<?xml version="1.0" encoding="utf-8"?>' . $match[0]);
                foreach ($doc->getElementsByTagName('tr') as $vv) {
                    $a = $vv->getElementsByTagName('a');
                    if($a->length){
                        $arr[] = [
                            'code' => $a[0]->textContent,
                            'name' => $a[1]->textContent,
                            'url'  => $a[0]->getAttribute('href'),
                        ];
                    }else{
                        $a = $vv->getElementsByTagName('td');
                        $arr[] = [
                            'code' => $a[0]->textContent,
                            'name' => $a[1]->textContent,
                            'url'  => '',
                        ];
                    }
                }
            }
            file_put_contents($file, json_encode($arr,JSON_UNESCAPED_UNICODE));
        }
        $town = [...$town,...$arr];
    }
    return $town;
}
function getCounty(){
    global $city,$url;
    $county = [];
    foreach($city as $v){
        echo $v['name'].PHP_EOL;
        usleep(SLEEP_TIME);
        $s = httpGet($v['url']);
        if(!$s){
            $s = httpGet($v['url']);
        }
        $s = mb_convert_encoding($s, 'UTF-8', 'gbk');
        preg_match("#<tr class='countytr'>(.*)</tr>#", $s, $match);
        if ( !$match) {
            if(preg_match("#<tr class='towntr'>(.*)</tr>#", $s, $match)){
                $v['url'] = substr($v['url'], 3);
                $county[] = $v;
                continue;
            }
            var_dump($v,$s);
            exit(-1);
            continue;
        }
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml version="1.0" encoding="utf-8"?>' . $match[0]);
        foreach ($doc->getElementsByTagName('tr') as $vv) {
            $a = $vv->getElementsByTagName('a');
            if($a->length){
                $county[] = [
                    'code' => $a[0]->textContent,
                    'name' => $a[1]->textContent,
                    'url'  => $a[0]->getAttribute('href'),
                ];
            }else{
                $a = $vv->getElementsByTagName('td');
                $county[] = [
                    'code' => $a[0]->textContent,
                    'name' => $a[1]->textContent,
                    'url'  => '',
                ];
            }
        }
    }
    return $county;
}
function getCity(){
    global $province,$url;
    $city = [];
    foreach ($province as $v) {
        echo $v['name'].PHP_EOL;
        usleep(SLEEP_TIME);
        $s = httpGet($v['url']);
        if(!$s){
            $s = httpGet($v['url']);
        }

        $s = mb_convert_encoding($s, 'UTF-8', 'gbk');
        preg_match("#<tr class='citytr'>(.*)</tr>#", $s, $match);
        if ( !$match) {
            var_dump($v);
            exit(-1);
            continue;
        }
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml version="1.0" encoding="utf-8"?>' . $match[0]);
        foreach ($doc->getElementsByTagName('tr') as $vv) {
            $a = $vv->getElementsByTagName('a');
            $city[] = [
                'code' => $a[0]->textContent,
                'name' => $a[1]->textContent,
                'url'  => $a[0]->getAttribute('href')];
        }
    }
    return $city;
}
function getProvince()
{
    $path = 'index.html';
    $s = httpGet($path);
    if(!$s){
        $s = httpGet($path);
    }
    $s = mb_convert_encoding($s, 'UTF-8', 'gbk');
    preg_match("#<tr class='provincetr'>(.*)</tr>#", $s, $match);
    if ( !$match) {
        exit(-1);
    }
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml version="1.0" encoding="utf-8"?>' . $match[0]);
    $pro = [];
    foreach ($doc->getElementsByTagName('td') as $v) {
        $a = $v->getElementsByTagName('a')[0];
        if ( !$a) {
            continue;
        }
        $href = $a->getAttribute('href');
        $p_code = substr($href, 0, 2);
        $pro[] = ['code' => $p_code . '0000000000', 'url' => $href, 'name' => $a->textContent];
    }
    return $pro;
}
function unGzip($s){
    $h = substr($s,0,3);
    if($h === hex2bin('1F8B08')){
        return gzdecode($s);
    }
    return $s;
}

function httpGet($url){
    static $fp;
    if(!$fp || !is_resource($fp) || feof($fp)){
        if($fp)@fclose($fp);
        $fp = stream_socket_client('tcp://www.stats.gov.cn:80',$errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
            exit(-1);
        }
    }
    $str = <<<EEE
GET /tjsj/tjbz/tjyqhdmhcxhfdm/2020/$url HTTP/1.1
Host: www.stats.gov.cn
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:90.0) Gecko/20100101 Firefox/90.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2
Accept-Encoding: gzip, deflate
Connection: keep-alive
Cookie: SF_cookie_1=37059734


EEE;
    $time = -microtime(true);
    fwrite($fp, $str);
    list($header,$body) = httpRead($fp);
    $time += microtime(true);
    echo "time: ",sprintf('%f', $time),PHP_EOL;
    if(!$body){
        echo 'http get error'.PHP_EOL;
        echo $url . PHP_EOL;
        var_dump($header);
        @fclose($fp);
        $fp = null;
        return false;
    }
    return unGzip($body);
}
function httpRead($fp){
    $headers = [];
    while ($s = fgets($fp,1024)){
        $headers[] = $s;
        if($s == "\r\n"){
            break;
        }
        usleep(1);
    }
    if(!$s){
        var_dump('连接已断开：' . feof($fp));
        return [$headers,''];
    }
    $len = 0;
    foreach ($headers as $header){
        if (strpos($header, 'Content-Length: ')===0){
            $len = (int)substr($header, 15);
        }
    }
    $body = '';
    if($len){
        $body = fread($fp, $len);
    }
    return [$headers,$body];
}

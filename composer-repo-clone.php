<?php
define('DIR', 'e:\repo' . DIRECTORY_SEPARATOR);
define('HEADER', <<<EEE
 HTTP/1.1
Host: packagist.org
User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2
Accept-Encoding: gzip,deflate, br
Connection: keep-alive


EEE
);

open();
$result = getRemoteFile('packages.json', DIR .'packages.json');
//$result = file_get_contents(DIR .'packages.json');
$providers = json_decode($result,true)['provider-includes'];
foreach ($providers as $provider => $hash){
    if(!empty($argv[1]) && $argv[1] !== $provider ){
        continue;
    }
    $mod = 0;
    $ret = getProviderFile($provider, $hash['sha256']);
    $packages = json_decode($ret, true)['providers'];
    foreach ($packages as $package => $pHash){
        $mod++;
        if(!empty($argv[2])&&!empty($argv[3]) && ($mod%intval($argv[2])) !== intval($argv[3]))continue;
        getPackageFile($package, $pHash['sha256']);
        
    }
}
fclose($GLOBALS['fp']);
function open(){
    $fp = stream_socket_client('ssl://packagist.org:443',$errno,$str);
    if($fp){
        $GLOBALS['fp'] = $fp;
    }else{
        echo 'connect fail ' . $str . PHP_EOL;
        exit(1);
    }
}

function getProviderFile($url,$hash){
    $localFileName = DIR . 'p-' .str_replace('$%hash%', '', substr($url,2));
    $remoteUrl = str_replace('$%hash%', '%24'.$hash, $url);
    if(file_exists($localFileName)){
        if(hash_file('sha256',$localFileName) === $hash){
            return file_get_contents($localFileName);
        }
    }
    return getRemoteFile($remoteUrl, $localFileName);
}

function getPackageFile($package,$hash){
    $localFileName = DIR . 'provider-' .str_replace('/', '$', $package) . '.json';
    $remoteUrl = "p/$package%24$hash.json";
    if(file_exists($localFileName)){
        if(hash_file('sha256',$localFileName) === $hash){
            return file_get_contents($localFileName);
        }
    }
    return getRemoteFile($remoteUrl, $localFileName);
}

function getRemoteFile($url,$filename){
    $str = "GET /$url" . HEADER;
    $fp = $GLOBALS['fp'];
    fwrite($fp, $str);
    list($header,$result) = readData();
    if($header['state']!=='200'){
        var_dump(stream_get_meta_data($GLOBALS['fp']));
        throw new Exception("get $url error : {$header['protocol']} {$header['state']} {$header['code']}" );
    }
    if(intval($header['Content-Length'])!==strlen($result)){
        throw new Exception("get $url len error : {$header['Content-Length']} : " . strlen($result));
    }
    $result = zlib_decode($result);
    $result = file_get_contents('compress.zlib://data:application/octet-stream;base64,'.base64_encode($result));
    if(!$result){
        var_dump($url);
        throw new Exception('Failed to decode zlib stream');
    }
    if($filename){
        $len = file_put_contents($filename, $result);
    }
    return $result;
}

function parseHeader($header){
    $list = explode("\r\n", $header);
    $header = [];
    list($header['protocol'],$header['state'],$header['code']) = explode(' ', $list[0], 3);
    for ($i = 1; $i < count($list); $i++){
        list($k,$v) = explode(': ', $list[$i]);
        $header[$k] = $v;
    }
    return $header;
}

function readData(){
    static $count;
    $ret = fread($GLOBALS['fp'],8192);
    if(!$ret){
        throw new Exception("read file error : read count : $count");
    }
    list($headers, $body) = explode("\r\n\r\n", $ret,2);
    $headers = parseHeader($headers);
    while (!feof($GLOBALS['fp']) &&isset($headers['Content-Length'])&&$headers['Content-Length'] > strlen($body)){
        $body .= fread($GLOBALS['fp'],8192);
    }    
    if(!$count)$count=0;
    $count++;
    if($count>=99){
        @fclose($GLOBALS['fp']);
        open();
        $count = 0;
    }
    return [$headers,$body];
}

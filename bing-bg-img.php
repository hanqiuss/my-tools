<?php
$url = 'https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1&nc=' . time() .'308&pid=hp&FORM=BEHPTB&video=1&ensearch=0';
$ret = file_get_contents($url);
$ret = json_decode($ret, true);
$imgUrl = $ret['images'][0]['url'];
if(!$imgUrl) exit(1);
$imgUrl = 'https://cn.bing.com' . $imgUrl;
$data   = file_get_contents($imgUrl);
if($data){
    $list = glob('img\\*.jpg');
    file_put_contents('img/' . time().'.jpg', $data);
    file_put_contents('img/' . (time()+1).'.jpg', $data);

    foreach($list as $file){
        unlink($file);
    }
    
}

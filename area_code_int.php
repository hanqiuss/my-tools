<?php
ini_set('memory_limit', '1G');

getCountry();
getProvince();
getCity();


exit;


function getCity()
{
    getCityCache();
    $a = file_get_contents('province.json');
    $a = json_decode($a, true);
    $c = count($a);
    $list = [];
    for ($i = 0; $i < $c; $i++) {
        $l1 = [];
        $s = file_get_contents("cache/$i.json");
        $s = json_decode($s, true);
        foreach ($s as $v) {
            if (is_string($v)) {
                $v = json_decode($v, true);
                $l2 = [];
                $v = $v['data'];
                $curData = $v['currentAddressNode'];
                $vList = getList($v);
                foreach ($vList as $v2) {
                    $l2[] = [
                        'label' => $v2['name'],
                        'value' => $v2['code'],
                        'pCode' => $curData['code'],
                    ];
                }
                $l1[] = $l2;
            } else {
                $l1[] = [$v];
            }
        }
        $list[] = $l1;
    }
    file_put_contents('area.json', json_encode($list, JSON_UNESCAPED_UNICODE));
}

function getCityCache()
{
    if (is_file('area1.json')) {
        return;
    }
    $a = file_get_contents('city.json');
    $a = json_decode($a, true);
    $list = [];
    foreach ($a as $i => $v) {
        if ( !is_file("cache/$i.json")) {
            $l1 = [];
            foreach ($v as $v2) {
                if ($v2['child']) {
                    $l1[] = getAddressList($v2['pCode'], $v2['value']);
                    usleep(20000);
                } else {
                    $l1[] = $v2;
                }
            }
            file_put_contents("cache/$i.json", json_encode($l1, JSON_UNESCAPED_UNICODE));
        } else {
            $s = file_get_contents("cache/$i.json");
            $l1 = json_decode($s, true);
        }
        $list[] = $l1;
    }
    file_put_contents("area1.json", json_encode($list, JSON_UNESCAPED_UNICODE));
}


function getProvince()
{
    if (is_file('city.json')) {
        return;
    }
    $s = file_get_contents('province.json');
    $d = json_decode($s, true);
    $cList = [];
    if ( !is_file('city1.json')) {
        $list = [];
        foreach ($d as $v) {
            if ($v['child']) {
                $list[] = getAddressList($v['value']);
                usleep(20000);
            } else {
                $list[] = [$v];
            }
        }
        file_put_contents('city1.json', json_encode($list, JSON_UNESCAPED_UNICODE));
        $cList = $list;
    } else {
        $s = file_get_contents('city1.json');
        $cList = json_decode($s, true);
    }
    $rList = [];
    foreach ($cList as $value) {
        if (is_array($value)) {
            $rList[] = $value;
            continue;
        }
        $l1 = [];
        $arr = json_decode($value, true);
        $curData = $arr['data']['currentAddressNode'];
        $adList = getList($arr['data']);
        foreach ($adList as $v2) {
            $l1[] = [
                'label' => $v2['name'],
                'value' => $v2['code'],
                'child' => $v2['hasChildren'],
                'pCode' => $curData['code'],
            ];
            
        }
        $rList[] = $l1;
    }
    file_put_contents('city.json', json_encode($rList, JSON_UNESCAPED_UNICODE));
}


function getCountry()
{
    if (is_file('province.json')) {
        return;
    }
    $a = <<<EEE
{"addressNodeList":[{"addressNodeList":[{"code":"AF","hasChildren":false,"i18nMap":{},"id":100001,"language":"EN_US","level":1,"name":"Afghanistan","type":"COUNTRY"},{"code":"ALA","hasChildren":false,"i18nMap":{},"id":100002,"language":"EN_US","level":1,"name":"Aland Islands","type":"COUNTRY"},{"code":"AL","hasChildren":false,"i18nMap":{},"id":100003,"language":"EN_US","level":1,"name":"Albania","type":"COUNTRY"},{"code":"GBA","hasChildren":false,"i18nMap":{},"id":100004,"language":"EN_US","level":1,"name":"Alderney","type":"COUNTRY"},{"code":"DZ","hasChildren":false,"i18nMap":{},"id":100005,"language":"EN_US","level":1,"name":"Algeria","type":"COUNTRY"},{"code":"AS","hasChildren":false,"i18nMap":{},"id":100006,"language":"EN_US","level":1,"name":"American Samoa","type":"COUNTRY"},{"code":"AD","hasChildren":false,"i18nMap":{},"id":100007,"language":"EN_US","level":1,"name":"Andorra","type":"COUNTRY"},{"code":"AO","hasChildren":false,"i18nMap":{},"id":100008,"language":"EN_US","level":1,"name":"Angola","type":"COUNTRY"},{"code":"AI","hasChildren":false,"i18nMap":{},"id":100009,"language":"EN_US","level":1,"name":"Anguilla","type":"COUNTRY"},{"code":"AG","hasChildren":false,"i18nMap":{},"id":100011,"language":"EN_US","level":1,"name":"Antigua and Barbuda","type":"COUNTRY"},{"code":"AR","hasChildren":true,"i18nMap":{},"id":100012,"language":"EN_US","level":1,"name":"Argentina","type":"COUNTRY"},{"code":"AM","hasChildren":false,"i18nMap":{},"id":100013,"language":"EN_US","level":1,"name":"Armenia","type":"COUNTRY"},{"code":"AW","hasChildren":false,"i18nMap":{},"id":100014,"language":"EN_US","level":1,"name":"Aruba","type":"COUNTRY"},{"code":"ASC","hasChildren":false,"i18nMap":{},"id":100015,"language":"EN_US","level":1,"name":"Ascension Island","type":"COUNTRY"},{"code":"AU","hasChildren":true,"i18nMap":{},"id":100016,"language":"EN_US","level":1,"name":"Australia","type":"COUNTRY"},{"code":"AT","hasChildren":false,"i18nMap":{},"id":100017,"language":"EN_US","level":1,"name":"Austria","type":"COUNTRY"},{"code":"AZ","hasChildren":false,"i18nMap":{},"id":100018,"language":"EN_US","level":1,"name":"Azerbaijan","type":"COUNTRY"}],"group":"A"},{"addressNodeList":[{"code":"BS","hasChildren":false,"i18nMap":{},"id":100019,"language":"EN_US","level":1,"name":"Bahamas","type":"COUNTRY"},{"code":"BH","hasChildren":false,"i18nMap":{},"id":100020,"language":"EN_US","level":1,"name":"Bahrain","type":"COUNTRY"},{"code":"BD","hasChildren":false,"i18nMap":{},"id":100021,"language":"EN_US","level":1,"name":"Bangladesh","type":"COUNTRY"},{"code":"BB","hasChildren":false,"i18nMap":{},"id":100022,"language":"EN_US","level":1,"name":"Barbados","type":"COUNTRY"},{"code":"BY","hasChildren":false,"i18nMap":{},"id":100023,"language":"EN_US","level":1,"name":"Belarus","type":"COUNTRY"},{"code":"BE","hasChildren":false,"i18nMap":{},"id":100024,"language":"EN_US","level":1,"name":"Belgium","type":"COUNTRY"},{"code":"BZ","hasChildren":false,"i18nMap":{},"id":100025,"language":"EN_US","level":1,"name":"Belize","type":"COUNTRY"},{"code":"BJ","hasChildren":false,"i18nMap":{},"id":100026,"language":"EN_US","level":1,"name":"Benin","type":"COUNTRY"},{"code":"BM","hasChildren":false,"i18nMap":{},"id":100027,"language":"EN_US","level":1,"name":"Bermuda","type":"COUNTRY"},{"code":"BT","hasChildren":false,"i18nMap":{},"id":100028,"language":"EN_US","level":1,"name":"Bhutan","type":"COUNTRY"},{"code":"BO","hasChildren":false,"i18nMap":{},"id":100029,"language":"EN_US","level":1,"name":"Bolivia","type":"COUNTRY"},{"code":"BA","hasChildren":false,"i18nMap":{},"id":100030,"language":"EN_US","level":1,"name":"Bosnia and Herzegovina","type":"COUNTRY"},{"code":"BW","hasChildren":false,"i18nMap":{},"id":100031,"language":"EN_US","level":1,"name":"Botswana","type":"COUNTRY"},{"code":"BR","hasChildren":true,"i18nMap":{},"id":32,"language":"en_US","level":1,"name":"Brazil","type":"COUNTRY"},{"code":"BN","hasChildren":false,"i18nMap":{},"id":243,"language":"en_US","level":1,"name":"Brunei","type":"COUNTRY"},{"code":"BG","hasChildren":false,"i18nMap":{},"id":100036,"language":"EN_US","level":1,"name":"Bulgaria","type":"COUNTRY"},{"code":"BF","hasChildren":false,"i18nMap":{},"id":100037,"language":"EN_US","level":1,"name":"Burkina Faso","type":"COUNTRY"},{"code":"BI","hasChildren":false,"i18nMap":{},"id":100038,"language":"EN_US","level":1,"name":"Burundi","type":"COUNTRY"}],"group":"B"},{"addressNodeList":[{"code":"KH","hasChildren":false,"i18nMap":{},"id":100039,"language":"EN_US","level":1,"name":"Cambodia","type":"COUNTRY"},{"code":"CM","hasChildren":false,"i18nMap":{},"id":100040,"language":"EN_US","level":1,"name":"Cameroon","type":"COUNTRY"},{"code":"CA","hasChildren":true,"i18nMap":{},"id":37,"language":"en_US","level":1,"name":"Canada","type":"COUNTRY"},{"code":"CV","hasChildren":false,"i18nMap":{},"id":100042,"language":"EN_US","level":1,"name":"Cape Verde","type":"COUNTRY"},{"code":"BQ","hasChildren":false,"i18nMap":{},"id":246,"language":"en_US","level":1,"name":"Caribbean Netherlands","type":"COUNTRY"},{"code":"KY","hasChildren":false,"i18nMap":{},"id":100043,"language":"EN_US","level":1,"name":"Cayman Islands","type":"COUNTRY"},{"code":"CF","hasChildren":false,"i18nMap":{},"id":100044,"language":"EN_US","level":1,"name":"Central African Republic","type":"COUNTRY"},{"code":"TD","hasChildren":false,"i18nMap":{},"id":100045,"language":"EN_US","level":1,"name":"Chad","type":"COUNTRY"},{"code":"CL","hasChildren":true,"i18nMap":{},"id":43,"language":"en_US","level":1,"name":"Chile","type":"COUNTRY"},{"code":"CX","hasChildren":false,"i18nMap":{},"id":100048,"language":"EN_US","level":1,"name":"Christmas Island","type":"COUNTRY"},{"code":"CC","hasChildren":false,"i18nMap":{},"id":100049,"language":"EN_US","level":1,"name":"Cocos (Keeling) Islands","type":"COUNTRY"},{"code":"CO","hasChildren":true,"i18nMap":{},"id":46,"language":"en_US","level":1,"name":"Colombia","type":"COUNTRY"},{"code":"KM","hasChildren":false,"i18nMap":{},"id":100051,"language":"EN_US","level":1,"name":"Comoros","type":"COUNTRY"},{"code":"ZR","hasChildren":false,"i18nMap":{},"id":100052,"language":"EN_US","level":1,"name":"Congo, The Democratic Republic Of The","type":"COUNTRY"},{"code":"CK","hasChildren":false,"i18nMap":{},"id":100054,"language":"EN_US","level":1,"name":"Cook Islands","type":"COUNTRY"},{"code":"CR","hasChildren":false,"i18nMap":{},"id":100055,"language":"EN_US","level":1,"name":"Costa Rica","type":"COUNTRY"},{"code":"CI","hasChildren":false,"i18nMap":{},"id":100056,"language":"EN_US","level":1,"name":"Cote D'Ivoire","type":"COUNTRY"},{"code":"HR","hasChildren":false,"i18nMap":{},"id":100057,"language":"EN_US","level":1,"name":"Croatia (local name: Hrvatska)","type":"COUNTRY"},{"code":"CW","hasChildren":false,"i18nMap":{},"id":257,"language":"en_US","level":1,"name":"Curacao","type":"COUNTRY"},{"code":"CY","hasChildren":false,"i18nMap":{},"id":100059,"language":"EN_US","level":1,"name":"Cyprus","type":"COUNTRY"},{"code":"CZ","hasChildren":false,"i18nMap":{},"id":100060,"language":"EN_US","level":1,"name":"Czech Republic","type":"COUNTRY"},{"code":"CG","hasChildren":false,"i18nMap":{},"id":100053,"language":"EN_US","level":1,"name":"Congo, The Republic of Congo","type":"COUNTRY"}],"group":"C"},{"addressNodeList":[{"code":"DK","hasChildren":false,"i18nMap":{},"id":100061,"language":"EN_US","level":1,"name":"Denmark","type":"COUNTRY"},{"code":"DJ","hasChildren":false,"i18nMap":{},"id":100062,"language":"EN_US","level":1,"name":"Djibouti","type":"COUNTRY"},{"code":"DM","hasChildren":false,"i18nMap":{},"id":100063,"language":"EN_US","level":1,"name":"Dominica","type":"COUNTRY"},{"code":"DO","hasChildren":false,"i18nMap":{},"id":100064,"language":"EN_US","level":1,"name":"Dominican Republic","type":"COUNTRY"}],"group":"D"},{"addressNodeList":[{"code":"EC","hasChildren":false,"i18nMap":{},"id":100066,"language":"EN_US","level":1,"name":"Ecuador","type":"COUNTRY"},{"code":"EG","hasChildren":false,"i18nMap":{},"id":100067,"language":"EN_US","level":1,"name":"Egypt","type":"COUNTRY"},{"code":"SV","hasChildren":false,"i18nMap":{},"id":100068,"language":"EN_US","level":1,"name":"El Salvador","type":"COUNTRY"},{"code":"GQ","hasChildren":false,"i18nMap":{},"id":100069,"language":"EN_US","level":1,"name":"Equatorial Guinea","type":"COUNTRY"},{"code":"ER","hasChildren":false,"i18nMap":{},"id":100070,"language":"EN_US","level":1,"name":"Eritrea","type":"COUNTRY"},{"code":"EE","hasChildren":false,"i18nMap":{},"id":100071,"language":"EN_US","level":1,"name":"Estonia","type":"COUNTRY"},{"code":"ET","hasChildren":false,"i18nMap":{},"id":100072,"language":"EN_US","level":1,"name":"Ethiopia","type":"COUNTRY"}],"group":"E"},{"addressNodeList":[{"code":"FK","hasChildren":false,"i18nMap":{},"id":100073,"language":"EN_US","level":1,"name":"Falkland Islands (Malvinas)","type":"COUNTRY"},{"code":"FO","hasChildren":false,"i18nMap":{},"id":100074,"language":"EN_US","level":1,"name":"Faroe Islands","type":"COUNTRY"},{"code":"FJ","hasChildren":false,"i18nMap":{},"id":100075,"language":"EN_US","level":1,"name":"Fiji","type":"COUNTRY"},{"code":"FI","hasChildren":false,"i18nMap":{},"id":100076,"language":"EN_US","level":1,"name":"Finland","type":"COUNTRY"},{"code":"FR","hasChildren":true,"i18nMap":{},"id":72,"language":"en_US","level":1,"name":"France","type":"COUNTRY"},{"code":"PF","hasChildren":false,"i18nMap":{},"id":100080,"language":"EN_US","level":1,"name":"French Polynesia","type":"COUNTRY"},{"code":"GF","hasChildren":false,"i18nMap":{},"id":100079,"language":"EN_US","level":1,"name":"French Guiana","type":"COUNTRY"}],"group":"F"},{"addressNodeList":[{"code":"GA","hasChildren":false,"i18nMap":{},"id":100082,"language":"EN_US","level":1,"name":"Gabon","type":"COUNTRY"},{"code":"GM","hasChildren":false,"i18nMap":{},"id":100083,"language":"EN_US","level":1,"name":"Gambia","type":"COUNTRY"},{"code":"GE","hasChildren":false,"i18nMap":{},"id":100084,"language":"EN_US","level":1,"name":"Georgia","type":"COUNTRY"},{"code":"DE","hasChildren":true,"i18nMap":{},"id":77,"language":"en_US","level":1,"name":"Germany","type":"COUNTRY"},{"code":"GH","hasChildren":false,"i18nMap":{},"id":100086,"language":"EN_US","level":1,"name":"Ghana","type":"COUNTRY"},{"code":"GI","hasChildren":false,"i18nMap":{},"id":100087,"language":"EN_US","level":1,"name":"Gibraltar","type":"COUNTRY"},{"code":"GR","hasChildren":false,"i18nMap":{},"id":100088,"language":"EN_US","level":1,"name":"Greece","type":"COUNTRY"},{"code":"GL","hasChildren":false,"i18nMap":{},"id":100089,"language":"EN_US","level":1,"name":"Greenland","type":"COUNTRY"},{"code":"GD","hasChildren":false,"i18nMap":{},"id":100090,"language":"EN_US","level":1,"name":"Grenada","type":"COUNTRY"},{"code":"GP","hasChildren":false,"i18nMap":{},"id":100091,"language":"EN_US","level":1,"name":"Guadeloupe","type":"COUNTRY"},{"code":"GU","hasChildren":false,"i18nMap":{},"id":100092,"language":"EN_US","level":1,"name":"Guam","type":"COUNTRY"},{"code":"GT","hasChildren":false,"i18nMap":{},"id":100093,"language":"EN_US","level":1,"name":"Guatemala","type":"COUNTRY"},{"code":"GGY","hasChildren":false,"i18nMap":{},"id":100094,"language":"EN_US","level":1,"name":"Guernsey","type":"COUNTRY"},{"code":"GN","hasChildren":false,"i18nMap":{},"id":100095,"language":"EN_US","level":1,"name":"Guinea","type":"COUNTRY"},{"code":"GW","hasChildren":false,"i18nMap":{},"id":100096,"language":"EN_US","level":1,"name":"Guinea-Bissau","type":"COUNTRY"},{"code":"GY","hasChildren":false,"i18nMap":{},"id":100097,"language":"EN_US","level":1,"name":"Guyana","type":"COUNTRY"}],"group":"G"},{"addressNodeList":[{"code":"HT","hasChildren":false,"i18nMap":{},"id":100098,"language":"EN_US","level":1,"name":"Haiti","type":"COUNTRY"},{"code":"HN","hasChildren":false,"i18nMap":{},"id":100100,"language":"EN_US","level":1,"name":"Honduras","type":"COUNTRY"},{"code":"HK","hasChildren":false,"i18nMap":{},"id":810000,"language":"en_US","level":1,"name":"Hong Kong,China","type":"COUNTRY"},{"code":"HU","hasChildren":false,"i18nMap":{},"id":100202,"language":"EN_US","level":1,"name":"Hungary","type":"COUNTRY"}],"group":"H"},{"addressNodeList":[{"code":"IS","hasChildren":false,"i18nMap":{},"id":100203,"language":"EN_US","level":1,"name":"Iceland","type":"COUNTRY"},{"code":"IN","hasChildren":true,"i18nMap":{},"id":100204,"language":"EN_US","level":1,"name":"India","type":"COUNTRY"},{"code":"ID","hasChildren":true,"i18nMap":{},"id":100101,"language":"EN_US","level":1,"name":"Indonesia","type":"COUNTRY"},{"code":"IQ","hasChildren":false,"i18nMap":{},"id":100206,"language":"EN_US","level":1,"name":"Iraq","type":"COUNTRY"},{"code":"IE","hasChildren":false,"i18nMap":{},"id":100207,"language":"EN_US","level":1,"name":"Ireland","type":"COUNTRY"},{"code":"IL","hasChildren":false,"i18nMap":{},"id":100209,"language":"EN_US","level":1,"name":"Israel","type":"COUNTRY"},{"code":"IT","hasChildren":true,"i18nMap":{},"id":101,"language":"en_US","level":1,"name":"Italy","type":"COUNTRY"}],"group":"I"},{"addressNodeList":[{"code":"JM","hasChildren":false,"i18nMap":{},"id":100211,"language":"EN_US","level":1,"name":"Jamaica","type":"COUNTRY"},{"code":"JP","hasChildren":true,"i18nMap":{},"id":104,"language":"en_US","level":1,"name":"Japan","type":"COUNTRY"},{"code":"JEY","hasChildren":false,"i18nMap":{},"id":100213,"language":"EN_US","level":1,"name":"Jersey","type":"COUNTRY"},{"code":"JO","hasChildren":false,"i18nMap":{},"id":100214,"language":"EN_US","level":1,"name":"Jordan","type":"COUNTRY"}],"group":"J"},{"addressNodeList":[{"code":"KZ","hasChildren":true,"i18nMap":{},"id":108,"language":"en_US","level":1,"name":"Kazakhstan","type":"COUNTRY"},{"code":"KE","hasChildren":false,"i18nMap":{},"id":100216,"language":"EN_US","level":1,"name":"Kenya","type":"COUNTRY"},{"code":"KI","hasChildren":false,"i18nMap":{},"id":100217,"language":"EN_US","level":1,"name":"Kiribati","type":"COUNTRY"},{"code":"KR","hasChildren":true,"i18nMap":{},"id":198,"language":"en_US","level":1,"name":"Korea","type":"COUNTRY"},{"code":"KS","hasChildren":false,"i18nMap":{},"id":100218,"language":"EN_US","level":1,"name":"Kosovo","type":"COUNTRY"},{"code":"KW","hasChildren":false,"i18nMap":{},"id":100219,"language":"EN_US","level":1,"name":"Kuwait","type":"COUNTRY"},{"code":"KG","hasChildren":false,"i18nMap":{},"id":100220,"language":"EN_US","level":1,"name":"Kyrgyzstan","type":"COUNTRY"}],"group":"K"},{"addressNodeList":[{"code":"LA","hasChildren":false,"i18nMap":{},"id":100221,"language":"EN_US","level":1,"name":"Lao People's Democratic Republic","type":"COUNTRY"},{"code":"LV","hasChildren":false,"i18nMap":{},"id":100222,"language":"EN_US","level":1,"name":"Latvia","type":"COUNTRY"},{"code":"LB","hasChildren":false,"i18nMap":{},"id":100223,"language":"EN_US","level":1,"name":"Lebanon","type":"COUNTRY"},{"code":"LS","hasChildren":false,"i18nMap":{},"id":100224,"language":"EN_US","level":1,"name":"Lesotho","type":"COUNTRY"},{"code":"LR","hasChildren":false,"i18nMap":{},"id":100225,"language":"EN_US","level":1,"name":"Liberia","type":"COUNTRY"},{"code":"LY","hasChildren":false,"i18nMap":{},"id":100226,"language":"EN_US","level":1,"name":"Libya","type":"COUNTRY"},{"code":"LI","hasChildren":false,"i18nMap":{},"id":100227,"language":"EN_US","level":1,"name":"Liechtenstein","type":"COUNTRY"},{"code":"LT","hasChildren":false,"i18nMap":{},"id":100228,"language":"EN_US","level":1,"name":"Lithuania","type":"COUNTRY"},{"code":"LU","hasChildren":false,"i18nMap":{},"id":100229,"language":"EN_US","level":1,"name":"Luxembourg","type":"COUNTRY"}],"group":"L"},{"addressNodeList":[{"code":"MO","hasChildren":false,"i18nMap":{},"id":820000,"language":"en_US","level":1,"name":"Macau,China","type":"COUNTRY"},{"code":"MG","hasChildren":false,"i18nMap":{},"id":100232,"language":"EN_US","level":1,"name":"Madagascar","type":"COUNTRY"},{"code":"MW","hasChildren":false,"i18nMap":{},"id":100233,"language":"EN_US","level":1,"name":"Malawi","type":"COUNTRY"},{"code":"MY","hasChildren":true,"i18nMap":{},"id":100234,"language":"EN_US","level":1,"name":"Malaysia","type":"COUNTRY"},{"code":"MV","hasChildren":false,"i18nMap":{},"id":100235,"language":"EN_US","level":1,"name":"Maldives","type":"COUNTRY"},{"code":"ML","hasChildren":false,"i18nMap":{},"id":100236,"language":"EN_US","level":1,"name":"Mali","type":"COUNTRY"},{"code":"MT","hasChildren":false,"i18nMap":{},"id":100237,"language":"EN_US","level":1,"name":"Malta","type":"COUNTRY"},{"code":"MH","hasChildren":false,"i18nMap":{},"id":100238,"language":"EN_US","level":1,"name":"Marshall Islands","type":"COUNTRY"},{"code":"MQ","hasChildren":false,"i18nMap":{},"id":100239,"language":"EN_US","level":1,"name":"Martinique","type":"COUNTRY"},{"code":"MR","hasChildren":false,"i18nMap":{},"id":100240,"language":"EN_US","level":1,"name":"Mauritania","type":"COUNTRY"},{"code":"MU","hasChildren":false,"i18nMap":{},"id":100241,"language":"EN_US","level":1,"name":"Mauritius","type":"COUNTRY"},{"code":"YT","hasChildren":false,"i18nMap":{},"id":100242,"language":"EN_US","level":1,"name":"Mayotte","type":"COUNTRY"},{"code":"MX","hasChildren":true,"i18nMap":{},"id":134,"language":"en_US","level":1,"name":"Mexico","type":"COUNTRY"},{"code":"FM","hasChildren":false,"i18nMap":{},"id":100244,"language":"EN_US","level":1,"name":"Micronesia","type":"COUNTRY"},{"code":"MC","hasChildren":false,"i18nMap":{},"id":100246,"language":"EN_US","level":1,"name":"Monaco","type":"COUNTRY"},{"code":"MN","hasChildren":false,"i18nMap":{},"id":100247,"language":"EN_US","level":1,"name":"Mongolia","type":"COUNTRY"},{"code":"MNE","hasChildren":false,"i18nMap":{},"id":100248,"language":"EN_US","level":1,"name":"Montenegro","type":"COUNTRY"},{"code":"MS","hasChildren":false,"i18nMap":{},"id":100249,"language":"EN_US","level":1,"name":"Montserrat","type":"COUNTRY"},{"code":"MA","hasChildren":false,"i18nMap":{},"id":100250,"language":"EN_US","level":1,"name":"Morocco","type":"COUNTRY"},{"code":"MZ","hasChildren":false,"i18nMap":{},"id":100251,"language":"EN_US","level":1,"name":"Mozambique","type":"COUNTRY"},{"code":"MM","hasChildren":false,"i18nMap":{},"id":100252,"language":"EN_US","level":1,"name":"Myanmar","type":"COUNTRY"},{"code":"MK","hasChildren":false,"i18nMap":{},"id":100231,"language":"EN_US","level":1,"name":"Macedonia","type":"COUNTRY"},{"code":"MD","hasChildren":false,"i18nMap":{},"id":100245,"language":"EN_US","level":1,"name":"Moldova","type":"COUNTRY"}],"group":"M"},{"addressNodeList":[{"code":"NA","hasChildren":false,"i18nMap":{},"id":100253,"language":"EN_US","level":1,"name":"Namibia","type":"COUNTRY"},{"code":"NR","hasChildren":false,"i18nMap":{},"id":100254,"language":"EN_US","level":1,"name":"Nauru","type":"COUNTRY"},{"code":"NP","hasChildren":false,"i18nMap":{},"id":100255,"language":"EN_US","level":1,"name":"Nepal","type":"COUNTRY"},{"code":"NL","hasChildren":true,"i18nMap":{},"id":147,"language":"en_US","level":1,"name":"Netherlands","type":"COUNTRY"},{"code":"AN","hasChildren":false,"i18nMap":{},"id":100257,"language":"EN_US","level":1,"name":"Netherlands Antilles","type":"COUNTRY"},{"code":"NC","hasChildren":false,"i18nMap":{},"id":100258,"language":"EN_US","level":1,"name":"New Caledonia","type":"COUNTRY"},{"code":"NZ","hasChildren":true,"i18nMap":{},"id":150,"language":"en_US","level":1,"name":"New Zealand","type":"COUNTRY"},{"code":"NI","hasChildren":false,"i18nMap":{},"id":100260,"language":"EN_US","level":1,"name":"Nicaragua","type":"COUNTRY"},{"code":"NE","hasChildren":false,"i18nMap":{},"id":100261,"language":"EN_US","level":1,"name":"Niger","type":"COUNTRY"},{"code":"NG","hasChildren":true,"i18nMap":{},"id":153,"language":"en_US","level":1,"name":"Nigeria","type":"COUNTRY"},{"code":"NU","hasChildren":false,"i18nMap":{},"id":100263,"language":"EN_US","level":1,"name":"Niue","type":"COUNTRY"},{"code":"NF","hasChildren":false,"i18nMap":{},"id":100264,"language":"EN_US","level":1,"name":"Norfolk Island","type":"COUNTRY"},{"code":"MP","hasChildren":false,"i18nMap":{},"id":100266,"language":"EN_US","level":1,"name":"Northern Mariana Islands","type":"COUNTRY"},{"code":"NO","hasChildren":false,"i18nMap":{},"id":100267,"language":"EN_US","level":1,"name":"Norway","type":"COUNTRY"}],"group":"N"},{"addressNodeList":[{"code":"OM","hasChildren":false,"i18nMap":{},"id":100268,"language":"EN_US","level":1,"name":"Oman","type":"COUNTRY"}],"group":"O"},{"addressNodeList":[{"code":"PK","hasChildren":false,"i18nMap":{},"id":100270,"language":"EN_US","level":1,"name":"Pakistan","type":"COUNTRY"},{"code":"PW","hasChildren":false,"i18nMap":{},"id":100271,"language":"EN_US","level":1,"name":"Palau","type":"COUNTRY"},{"code":"PS","hasChildren":false,"i18nMap":{},"id":100272,"language":"EN_US","level":1,"name":"Palestine","type":"COUNTRY"},{"code":"PA","hasChildren":false,"i18nMap":{},"id":100273,"language":"EN_US","level":1,"name":"Panama","type":"COUNTRY"},{"code":"PG","hasChildren":false,"i18nMap":{},"id":100274,"language":"EN_US","level":1,"name":"Papua New Guinea","type":"COUNTRY"},{"code":"PY","hasChildren":false,"i18nMap":{},"id":100275,"language":"EN_US","level":1,"name":"Paraguay","type":"COUNTRY"},{"code":"PE","hasChildren":true,"i18nMap":{},"id":165,"language":"en_US","level":1,"name":"Peru","type":"COUNTRY"},{"code":"PH","hasChildren":false,"i18nMap":{},"id":100277,"language":"EN_US","level":1,"name":"Philippines","type":"COUNTRY"},{"code":"PL","hasChildren":true,"i18nMap":{},"id":167,"language":"en_US","level":1,"name":"Poland","type":"COUNTRY"},{"code":"PT","hasChildren":false,"i18nMap":{},"id":100280,"language":"EN_US","level":1,"name":"Portugal","type":"COUNTRY"},{"code":"PR","hasChildren":false,"i18nMap":{},"id":100281,"language":"EN_US","level":1,"name":"Puerto Rico","type":"COUNTRY"}],"group":"P"},{"addressNodeList":[{"code":"QA","hasChildren":false,"i18nMap":{},"id":100282,"language":"EN_US","level":1,"name":"Qatar","type":"COUNTRY"}],"group":"Q"},{"addressNodeList":[{"code":"RE","hasChildren":false,"i18nMap":{},"id":100283,"language":"EN_US","level":1,"name":"Reunion","type":"COUNTRY"},{"code":"RO","hasChildren":false,"i18nMap":{},"id":100284,"language":"EN_US","level":1,"name":"Romania","type":"COUNTRY"},{"code":"RU","hasChildren":true,"i18nMap":{},"id":174,"language":"en_US","level":1,"name":"Russian Federation","type":"COUNTRY"},{"code":"RW","hasChildren":false,"i18nMap":{},"id":100286,"language":"EN_US","level":1,"name":"Rwanda","type":"COUNTRY"}],"group":"R"},{"addressNodeList":[{"code":"BLM","hasChildren":false,"i18nMap":{},"id":100287,"language":"EN_US","level":1,"name":"Saint Barthelemy","type":"COUNTRY"},{"code":"KN","hasChildren":false,"i18nMap":{},"id":100288,"language":"EN_US","level":1,"name":"Saint Kitts and Nevis","type":"COUNTRY"},{"code":"LC","hasChildren":false,"i18nMap":{},"id":100289,"language":"EN_US","level":1,"name":"Saint Lucia","type":"COUNTRY"},{"code":"MAF","hasChildren":false,"i18nMap":{},"id":100290,"language":"EN_US","level":1,"name":"Saint Martin","type":"COUNTRY"},{"code":"PM","hasChildren":false,"i18nMap":{},"id":100313,"language":"EN_US","level":1,"name":"St. Pierre and Miquelon","type":"COUNTRY"},{"code":"VC","hasChildren":false,"i18nMap":{},"id":100291,"language":"EN_US","level":1,"name":"Saint Vincent and the Grenadines","type":"COUNTRY"},{"code":"WS","hasChildren":false,"i18nMap":{},"id":100292,"language":"EN_US","level":1,"name":"Samoa","type":"COUNTRY"},{"code":"SM","hasChildren":false,"i18nMap":{},"id":100293,"language":"EN_US","level":1,"name":"San Marino","type":"COUNTRY"},{"code":"ST","hasChildren":false,"i18nMap":{},"id":100294,"language":"EN_US","level":1,"name":"Sao Tome and Principe","type":"COUNTRY"},{"code":"SA","hasChildren":true,"i18nMap":{},"id":185,"language":"en_US","level":1,"name":"Saudi Arabia","type":"COUNTRY"},{"code":"SN","hasChildren":false,"i18nMap":{},"id":100297,"language":"EN_US","level":1,"name":"Senegal","type":"COUNTRY"},{"code":"SRB","hasChildren":false,"i18nMap":{},"id":100298,"language":"EN_US","level":1,"name":"Serbia","type":"COUNTRY"},{"code":"SC","hasChildren":false,"i18nMap":{},"id":100299,"language":"EN_US","level":1,"name":"Seychelles","type":"COUNTRY"},{"code":"SL","hasChildren":false,"i18nMap":{},"id":100300,"language":"EN_US","level":1,"name":"Sierra Leone","type":"COUNTRY"},{"code":"SG","hasChildren":false,"i18nMap":{},"id":100301,"language":"EN_US","level":1,"name":"Singapore","type":"COUNTRY"},{"code":"SX","hasChildren":false,"i18nMap":{},"id":191,"language":"en_US","level":1,"name":"Sint Maarten","type":"COUNTRY"},{"code":"SK","hasChildren":false,"i18nMap":{},"id":100302,"language":"EN_US","level":1,"name":"Slovakia (Slovak Republic)","type":"COUNTRY"},{"code":"SI","hasChildren":false,"i18nMap":{},"id":100303,"language":"EN_US","level":1,"name":"Slovenia","type":"COUNTRY"},{"code":"SB","hasChildren":false,"i18nMap":{},"id":100304,"language":"EN_US","level":1,"name":"Solomon Islands","type":"COUNTRY"},{"code":"SO","hasChildren":false,"i18nMap":{},"id":100305,"language":"EN_US","level":1,"name":"Somalia","type":"COUNTRY"},{"code":"ZA","hasChildren":false,"i18nMap":{},"id":100306,"language":"EN_US","level":1,"name":"South Africa","type":"COUNTRY"},{"code":"SGS","hasChildren":false,"i18nMap":{},"id":100307,"language":"EN_US","level":1,"name":"South Georgia and the South Sandwich Islands","type":"COUNTRY"},{"code":"SS","hasChildren":false,"i18nMap":{},"id":100309,"language":"EN_US","level":1,"name":"South Sudan","type":"COUNTRY"},{"code":"ES","hasChildren":true,"i18nMap":{},"id":199,"language":"en_US","level":1,"name":"Spain","type":"COUNTRY"},{"code":"LK","hasChildren":false,"i18nMap":{},"id":100311,"language":"EN_US","level":1,"name":"Sri Lanka","type":"COUNTRY"},{"code":"SR","hasChildren":false,"i18nMap":{},"id":100315,"language":"EN_US","level":1,"name":"Suriname","type":"COUNTRY"},{"code":"SZ","hasChildren":false,"i18nMap":{},"id":100317,"language":"EN_US","level":1,"name":"Swaziland","type":"COUNTRY"},{"code":"SE","hasChildren":false,"i18nMap":{},"id":100318,"language":"EN_US","level":1,"name":"Sweden","type":"COUNTRY"},{"code":"CH","hasChildren":false,"i18nMap":{},"id":100319,"language":"EN_US","level":1,"name":"Switzerland","type":"COUNTRY"}],"group":"S"},{"addressNodeList":[{"code":"TLS","hasChildren":false,"i18nMap":{},"id":100325,"language":"EN_US","level":1,"name":"Timor-Leste","type":"COUNTRY"},{"code":"TW","hasChildren":false,"i18nMap":{},"id":710000,"language":"en_US","level":1,"name":"Taiwan,China","type":"COUNTRY"},{"code":"TJ","hasChildren":false,"i18nMap":{},"id":100322,"language":"EN_US","level":1,"name":"Tajikistan","type":"COUNTRY"},{"code":"TZ","hasChildren":false,"i18nMap":{},"id":100323,"language":"EN_US","level":1,"name":"Tanzania","type":"COUNTRY"},{"code":"TH","hasChildren":true,"i18nMap":{},"id":209,"language":"en_US","level":1,"name":"Thailand","type":"COUNTRY"},{"code":"TG","hasChildren":false,"i18nMap":{},"id":100326,"language":"EN_US","level":1,"name":"Togo","type":"COUNTRY"},{"code":"TO","hasChildren":false,"i18nMap":{},"id":100328,"language":"EN_US","level":1,"name":"Tonga","type":"COUNTRY"},{"code":"TT","hasChildren":false,"i18nMap":{},"id":100329,"language":"EN_US","level":1,"name":"Trinidad and Tobago","type":"COUNTRY"},{"code":"TN","hasChildren":false,"i18nMap":{},"id":100330,"language":"EN_US","level":1,"name":"Tunisia","type":"COUNTRY"},{"code":"TR","hasChildren":true,"i18nMap":{},"id":218,"language":"en_US","level":1,"name":"Turkey","type":"COUNTRY"},{"code":"TM","hasChildren":false,"i18nMap":{},"id":100332,"language":"EN_US","level":1,"name":"Turkmenistan","type":"COUNTRY"},{"code":"TC","hasChildren":false,"i18nMap":{},"id":100333,"language":"EN_US","level":1,"name":"Turks and Caicos Islands","type":"COUNTRY"},{"code":"TV","hasChildren":false,"i18nMap":{},"id":100334,"language":"EN_US","level":1,"name":"Tuvalu","type":"COUNTRY"}],"group":"T"},{"addressNodeList":[{"code":"UG","hasChildren":false,"i18nMap":{},"id":100335,"language":"EN_US","level":1,"name":"Uganda","type":"COUNTRY"},{"code":"UA","hasChildren":true,"i18nMap":{},"id":223,"language":"en_US","level":1,"name":"Ukraine","type":"COUNTRY"},{"code":"AE","hasChildren":true,"i18nMap":{},"id":224,"language":"en_US","level":1,"name":"United Arab Emirates","type":"COUNTRY"},{"code":"UK","hasChildren":true,"i18nMap":{},"id":225,"language":"en_US","level":1,"name":"United Kingdom","type":"COUNTRY"},{"code":"US","hasChildren":true,"i18nMap":{},"id":228,"language":"en_US","level":1,"name":"United States","type":"COUNTRY"},{"code":"UY","hasChildren":false,"i18nMap":{},"id":100341,"language":"EN_US","level":1,"name":"Uruguay","type":"COUNTRY"},{"code":"UZ","hasChildren":false,"i18nMap":{},"id":100342,"language":"EN_US","level":1,"name":"Uzbekistan","type":"COUNTRY"}],"group":"U"},{"addressNodeList":[{"code":"VG","hasChildren":false,"i18nMap":{},"id":100347,"language":"EN_US","level":1,"name":"Virgin Islands (British)","type":"COUNTRY"},{"code":"VA","hasChildren":false,"i18nMap":{},"id":100344,"language":"EN_US","level":1,"name":"Vatican City State (Holy See)","type":"COUNTRY"},{"code":"VI","hasChildren":false,"i18nMap":{},"id":100348,"language":"EN_US","level":1,"name":"Virgin Islands (U.S.)","type":"COUNTRY"},{"code":"VU","hasChildren":false,"i18nMap":{},"id":100343,"language":"EN_US","level":1,"name":"Vanuatu","type":"COUNTRY"},{"code":"VE","hasChildren":false,"i18nMap":{},"id":100345,"language":"EN_US","level":1,"name":"Venezuela","type":"COUNTRY"},{"code":"VN","hasChildren":true,"i18nMap":{},"id":100346,"language":"EN_US","level":1,"name":"Vietnam","type":"COUNTRY"}],"group":"V"},{"addressNodeList":[{"code":"WF","hasChildren":false,"i18nMap":{},"id":100349,"language":"EN_US","level":1,"name":"Wallis And Futuna Islands","type":"COUNTRY"}],"group":"W"},{"addressNodeList":[{"code":"YE","hasChildren":false,"i18nMap":{},"id":100351,"language":"EN_US","level":1,"name":"Yemen","type":"COUNTRY"}],"group":"Y"},{"addressNodeList":[{"code":"ZM","hasChildren":false,"i18nMap":{},"id":100353,"language":"EN_US","level":1,"name":"Zambia","type":"COUNTRY"},{"code":"EAZ","hasChildren":false,"i18nMap":{},"id":100354,"language":"EN_US","level":1,"name":"Zanzibar","type":"COUNTRY"},{"code":"ZW","hasChildren":false,"i18nMap":{},"id":100355,"language":"EN_US","level":1,"name":"Zimbabwe","type":"COUNTRY"}],"group":"Z"}],"i18nMap":{}}
EEE;
    $b = json_decode($a, true);
    $list = [];
    $list2 = getList($b);
    foreach ($list2 as $v) {
        $list[] = [
            'label' => $v['name'],
            'value' => $v['code'],
            'child' => $v['hasChildren'],
        ];
    }
    file_put_contents('province.json', json_encode($list, JSON_UNESCAPED_UNICODE));
}

function getList($data)
{
    $list = [];
    $data = $data['addressNodeList'];
    foreach ($data as $v) {
        foreach ($v['addressNodeList'] as $v2) {
            $list[] = $v2;
        }
    }
    return $list;
}

function getAddressList($code = '', $code2 = '')
{
    $url = 'https://m.pt.aliexpress.com/api/logistics/addresslist?countryCode=' . $code .
           '&useLocalAddress=false&targetLanguage=en_US';
    if ($code2) {
        $url = 'https://m.pt.aliexpress.com/api/logistics/addresslist?countryCode=' . $code . '&addressCode=' . $code2 .
               '&useLocalAddress=false&targetLanguage=en_US';
    }
    $header = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:97.0) Gecko/20100101 Firefox/97.0',
        'Accept: application/json, text/plain, */*',
        'Accept-Encoding: gzip,deflate, br',
        'Referer: https://m.pt.aliexpress.com/account/setting.html',
    ];
    $options = array(
        CURLOPT_URL              => $url,
        CURLOPT_RETURNTRANSFER   => true,
        CURLOPT_HEADER           => 0,
        CURLOPT_HTTPHEADER       => $header,
        CURLOPT_SSL_VERIFYPEER   => 0,
        CURLOPT_SSL_VERIFYSTATUS => 0,
    );
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $rs = curl_exec($ch);
    $errno = curl_errno($ch);
    if ($errno) {
        throw new Exception(sprintf("%s::%s(%d)\n", $url, curl_error($ch), $errno));
    }
    $rs = gzdecode($rs);
    return $rs;
}

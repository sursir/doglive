<?php
echo '<code><pre>', "\n";
// include 'a.inc.php';

// echo "\n";
// echo "excute file:\n";
// echo __DIR__, "\n";
// echo __FILE__, "\n";

// echo "\n";
// echo $_SERVER['HTTP_HOST'], "\n";
// echo $_SERVER['SCRIPT_NAME'], "\n";
// echo $_SERVER['SCRIPT_FILENAME'], "\n";
// echo $_SERVER['PHP_SELF'], "\n";

// var_dump(explode('.php', $_SERVER['PHP_SELF']));


$xml = <<<'EOF'
<itemRule>
    <field id="dealType" name="交易类型" type="singleCheck">
        <rules>
            <rule name="requiredRule" value="true" />
        </rules>
        <options>
            <option displayName="一口价" value="fixed" />
            <option displayName="拍卖" value="auction" />
        </options>
    </field>
    <field id="title" name="商品标题" type="input">
        <rules>
            <rule name="requiredRule" value="true" />
            <rule name="maxLengthRule" value="30" exProperty="include" />
            <rule name="valueTypeRule" value="text" />
        </rules>
    </field>
    <field id="price" name="价格" type="input">
        <rules>
            <rule name="valueTypeRule" value="decimal" />
            <rule name="requiredRule" value="true" />
        </rules>
    </field>
    <field id="prop_122276111" name="裤长" type="singleCheck">
        <options>
            <option displayName="七分裤" value="72202018" />
            <option displayName="九分裤" value="30465" />
            <option displayName="五分裤" value="30272" />
            <option displayName="短裤" value="20524" />
            <option displayName="长裤" value="20525" />
        </options>
    </field>
    <field id="prop_13021751" name="货号" type="input" />
</itemRule>
EOF;


// $xmlEle = simplexml_load_string($xml);
// foreach ($xmlEle as $key => $val) {
//     echo $key, "\n";
//     foreach ($val->attributes() as $attrKey => $attrVal) {
//         echo "$attrKey => $attrVal \n";
//     }
//     foreach ($val as $k => $v) {
//         echo "++ $k, \n";
//         foreach ($v as $kk => $vv) {
//             echo "++ ++ $kk,, \n";
//             $att = $vv->attributes();
//             echo "== == == ", $att[0], "\n";
//             foreach ($vv->attributes() as $attrKK => $attrVV) {
//                 echo "-- -- -- $attrKK => $attrVV \n";
//             }
//         }
//     }

//     echo "\n";
// }

include 'CurlMulti.php';


$url = 'http://127.0.0.1/a.php';
$options = array(
    CURLOPT_AUTOREFERER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 1,
    CURLOPT_TIMEOUT => 1,
);
$multiOptions = array(
    CURLOPT_CONNECTTIMEOUT => 1,
    CURLOPT_TIMEOUT => 1);

// $cm = new CurlMulti($url, $options);
// $cm->setMultiOpts($multiOptions);
// $res = $cm->run();
// $res = $res[0];

$mh = curl_multi_init();
$ch = curl_init($url);
curl_multi_add_handle($mh, $ch);
// curl_multi_setopt($mh, CURLMOPT_TIMEOUT, 1);
$running = null;

do {
    while (CURLM_CALL_MULTI_PERFORM === curl_multi_exec($mh, $running));
    if (!$running) break;
    while (($res = curl_multi_select($mh, 10)) === 0) {};
    if (($info = curl_multi_info_read($mh)) !== false) {
        $infos[$info['handle']] = $info;
    }
    if ($res === false) break;
} while (true);


var_dump($infos);
// $cont = curl_multi_getcontent($ch);
// $chInfo = curl_getinfo($ch);
// var_dump($cont, $chInfo);

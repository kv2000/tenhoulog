<?php
function downlog($paipu)
{
    $ch = curl_init('http://e.mjv.jp/0/log/?' . $paipu);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Encoding: gzip, deflate, sdch', 'Referer: tenhou.net'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    $a = curl_exec($ch);
    curl_close($ch);
    if (preg_match('/<a[^>]+href="([^"]+)"/', $a, $matches)) {
        $ch = curl_init($matches[1]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Encoding: gzip, deflate, sdch', 'Referer: tenhou.net'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        $a = curl_exec($ch);
        curl_close($ch);
    }
    return $a;
}
$year=2010;
$list = scandir("./log$year/");
array_shift($list);
array_shift($list);
$pattern = '/log=([^"]+)/'; //需要转义/
//$urls = array();
foreach ($list as $filename) {
    $myfile = fopen("./log$year/" . $filename, "r") or die("Unable to open file!");
    $text = fread($myfile, filesize("./log$year/" . $filename));
    fclose($myfile);
    preg_match_all($pattern, $text, $matches);
    //$urls = array_merge($urls, $matches[1]);
    foreach ($matches[1] as $paipu) {
        if (!file_exists("./xml$year/" . $paipu . ".txt")) {
            $xml = downlog($paipu);
            if (strlen($xml) > 50) {
                $myfile = fopen("./xml$year/" . $paipu . ".txt", "w");
                fwrite($myfile, $xml);
                fclose($myfile);
            }
        }
    }
}




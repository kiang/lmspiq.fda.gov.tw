<?php
require __DIR__ . '/vendor/autoload.php';
$basePath = dirname(__DIR__);

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

$browser = new HttpBrowser(HttpClient::create());

$licUnits = ['1', '2'];
foreach ($licUnits as $licUnit) {
    $rawPath = $basePath . '/raw/piq1000/' . $licUnit;
    if (!file_exists($rawPath)) {
        mkdir($rawPath, 0777, true);
    }
    $totalPages = 1;
    for ($page = 1; $page <= $totalPages; $page++) {
        $targetFile = $rawPath . '/' . $page . '.json';
        if (!file_exists($targetFile)) {
            $browser->jsonRequest('POST', 'https://lmspiq.fda.gov.tw/api/public/sh/piq/1000/search', array(
                'data' =>
                array(
                    'licUnit' => $licUnit,
                    'code' =>
                    array(
                        'code' => '4048',
                        'verifyCode' => '89NAK9NUpcy/hSAPys5N6vLMvG9i5sJf56biK5kShb5Rhtom/09hlUJcR69T2LF8',
                    ),
                ),
                'page' =>
                array(
                    'page' => $page,
                    'pageSize' => 100,
                ),
            ));
            $response = $browser->getResponse()->getContent();
            $data = json_decode($response, true);
            unset($data['response']);
            file_put_contents($targetFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        } else {
            $data = json_decode(file_get_contents($targetFile), true);
        }
        if ($page == 1 && !empty($data['page']['totalDatas'])) {
            $totalPages = ceil(intval($data['page']['totalDatas']) / 100);
        }

        foreach ($data['data'] as $item) {
            $licensePath = $basePath . '/raw/licenses/' . substr($item['licid'], 0, 2);
            if (!file_exists($licensePath)) {
                mkdir($licensePath, 0777, true);
            }

            $licenseFile = $licensePath . '/' . $item['licid'] . '.json';
            if (!file_exists($licenseFile)) {
                echo "getting {$item['licid']}\n";
                $browser->jsonRequest('POST', 'https://lmspiq.fda.gov.tw/api/public/sh/piq/1000/licSearch', [
                    'data' => [
                        'licBaseId' => $item['licBaseId'],
                    ],
                ]);
                $response = $browser->getResponse()->getContent();
                $license = json_decode($response, true);
                $license['data']['licBaseId'] = $item['licBaseId'];
                file_put_contents($licenseFile, json_encode($license['data'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            } else {
                $license = json_decode(file_get_contents($licenseFile), true);
                $license['licBaseId'] = $item['licBaseId'];
                file_put_contents($licenseFile, json_encode($license, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            }
        }
    }
}

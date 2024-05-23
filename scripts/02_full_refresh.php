<?php
require __DIR__ . '/vendor/autoload.php';
$basePath = dirname(__DIR__);

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

$browser = new HttpBrowser(HttpClient::create());

foreach (glob($basePath . '/raw/licenses/*/*.json') as $licenseFile) {
    $item = json_decode(file_get_contents($licenseFile), true);
    if (!empty($item['licBaseId'])) {
        $browser->jsonRequest('POST', 'https://lmspiq.fda.gov.tw/api/public/sh/piq/1000/licSearch', [
            'data' => [
                'licBaseId' => $item['licBaseId'],
            ],
        ]);
        $response = $browser->getResponse()->getContent();
        $license = json_decode($response, true);
        if (!empty($license['data'])) {
            $license['data']['licBaseId'] = $item['licBaseId'];
            file_put_contents($licenseFile, json_encode($license['data'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    } else {
        $browser->jsonRequest('GET', 'https://lmspiq.fda.gov.tw/api/public/vwHis/list/' . $item['licId']);
        $response = $browser->getResponse()->getContent();
        $license = json_decode($response, true);
        if (!empty($license)) {
            file_put_contents($licenseFile, json_encode($license, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }
}

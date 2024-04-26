<?php
require __DIR__ . '/vendor/autoload.php';
$basePath = dirname(__DIR__);

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

$browser = new HttpBrowser(HttpClient::create());

$browser->request('GET', 'https://lmspiq.fda.gov.tw/api/public/codes/search/simple/codeKind/MonthlyReport');
$reports = json_decode($browser->getResponse()->getContent(), true);


$theTime = time();
for ($i = 0; $i < 100; $i++) {
    $theTime = strtotime('-1 month', $theTime);
    foreach ($reports['data'] as $report) {
        $rawPath = $basePath . '/raw/reports/' . $report['text'] . '/' . date('Y', $theTime);
        if (!file_exists($rawPath)) {
            mkdir($rawPath, 0777, true);
        }
        $totalPages = 1;
        for ($j = 1; $j <= $totalPages; $j++) {
            $browser->jsonRequest('POST', 'https://lmspiq.fda.gov.tw/api/public/sh/piq/6000/search', [
                'data' => [
                    'type' => $report['value'],
                    'licUnit' => 1,
                    'year' => date('Y', $theTime),
                    'month' => date('m', $theTime),
                    'code' => [
                        'code' => 'wJN3818f2ZsoZ+jVkWZoqxTdmJSxmqA40zV4+MywCEGP06QCe9N5siNtudjKaxMEY',
                        'verifyCode' => 'wJN3818f2ZsoZ+jVkWZoqxTdmJSxmqA40zV4+MywCEGP06QCe9N5siNtudjKaxME',
                    ],
                ],
                'page' => [
                    'page' => $j,
                    'pageSize' => 100,
                ],
            ]);
            $response = $browser->getResponse()->getContent();
            $data = json_decode($response, true);
            unset($data['response']);
            if ($j == 1 && !empty($data['page']['totalDatas'])) {
                $totalPages = ceil(intval($data['page']['totalDatas']) / 100);
            }
            $targetFile = $rawPath . '/' . date('m', $theTime) . '-' . $j . '.json';
            file_put_contents($rawPath . '/' . date('m', $theTime) . '-' . $j . '.json', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }
}

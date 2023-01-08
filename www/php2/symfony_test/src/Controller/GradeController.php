<?php

namespace App\Controller;

use App\Entity\Fruit;
use GuzzleHttp\Client;
use Port\Spreadsheet\SpreadsheetReader;
use Port\Spreadsheet\SpreadsheetWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GradeController extends AbstractController
{
    private string $pbToken;
    private Client $client;

    public function __construct(string $pbToken)
    {
        $this->pbToken = $pbToken;
        $this->client = new Client();
    }

    public function showFunctions(): Response
    {
        return $this->render(
            'grade/showFunctions.html.twig',
            [
                'callFunctionWithString' => $this->callFunctionWithString(),
                'anonymousFunction' => $this->anonymousFunction(),
                'arrowFunctions' => $this->arrowFunctions()
            ]
        );
    }

    private function printMeSomething(): string
    {
        return 'Something has been printed';
    }

    private function callFunctionWithString(): string
    {
        $example = 'printMeSomething';
        return $this->$example();
    }

    private function anonymousFunction(): int
    {
        $totalCount = 0;
        $fruits = [
            'apples' => 10,
            'peaches' => 7,
            'bananas' => 4
        ];

        $callBack =
            static function ($fruits) use (&$totalCount)
            {
                $totalCount += $fruits;
            };

        array_walk($fruits, $callBack);

        return $totalCount;
    }

    private function arrowFunctions(): int
    {
        $num = 5;

        $func = fn($multiply) => $multiply * $num;

       return $func(5);
    }

    public function cloneObjects(): Response
    {
        $apple = new Fruit(
            'red',
            'small'
        );
        $linkToApple = $apple;

        $anotherApple = clone $apple;

        $banana = clone $apple;
        $banana->setColor('yellow');
        $banana->setSize('big');

        $comparisonFirst = $apple !== $anotherApple;
        $comparisonThird = $apple === $linkToApple;

        return $this->render(
            'grade/cloneObjects.html.twig',
            [
                'comparisonFirst' => $comparisonFirst,
                'comparisonThird' => $comparisonThird,
                'apple' => $apple,
                'anotherApple' => $anotherApple,
                'banana' => $banana
            ]
        );
    }

    public function dateTimePractice(): Response
    {
        setcookie(
            'test',
            'happy new year!',
            strtotime('+1 hour')
        );

        $currentTime = '2023-01-07 13:06:19.0';
        $anotherTimeFormat = '12:00 07.01.25';

        $unixTime = strtotime($currentTime);

        $timeFromString = new \DateTime($currentTime);
        $timeFromUnixTime = (new \DateTime())->setTimestamp($unixTime);

        $curTimeAtom = '2023-01-07T08:20:25+00:00';
        $timeAtom = (new \DateTime())->format(\DateTimeInterface::ATOM);

        $time = new \DateTime($currentTime);
        $time->add(new \DateInterval('P1Y'));
        $time->add(new \DateInterval('P1M'));


        $dtImmutable = new \DateTimeImmutable($currentTime);
        $anotherDtImmutable = (new \DateTimeImmutable())->setTimestamp($unixTime);

        $dateFromAnotherFormat = new \DateTime();
        $dateFromAnotherFormat = $dateFromAnotherFormat::createFromFormat('G:i d.m.y', $anotherTimeFormat);

        return $this->render(
            'grade/dateTimePractice.html.twig',
            [
                'dateTimeModified' => $time->format('Y-m-d H:i:s.u'),
                'dateTimeImmutable' => $dtImmutable->format(\DateTimeInterface::ATOM),
                'dateTimeAnotherFormat' => $dateFromAnotherFormat->format('Y-m-d H:i:s.u')
            ]
        );

    }

    public function getCookie(): Response
    {
        return $this->render(
            'grade/getCookie.html.twig',
            [
                'cookie' => $_COOKIE['test']
            ]
        );
    }

    public function guzzle()
    {
        $token = $this->getToken();

        $houseDecode = $this->getHouseDecode($token);
        $projectName = $houseDecode['data'][0]['projectName'];

        return $this->render(
            'grade/guzzle.html.twig',
            ['projectName' => $projectName]
        );
    }

    protected function getToken(): string
    {
        $params = [
            'type' => 'api-app',
            'credentials' => [
                'pb_api_key' => $this->pbToken
            ]
        ];

        $auth = $this->client->request(
            'POST',
            'https://pb14746.profitbase.ru/api/v4/json/authentication',
            [
                'json' => $params
            ]
        )->getBody()->getContents();

        $authDecode = json_decode($auth, true);
        return $authDecode['access_token'];
    }

    protected function getHouseDecode(string $token): array
    {
        $house = $this->client->request(
            'GET',
            'https://pb14746.profitbase.ru/api/v4/json/house',
            [
                'query' => [
                    'access_token' => $token,
                    'id' => 98793
                ]
            ]
        )->getBody()->getContents();

        return json_decode($house, true, JSON_UNESCAPED_UNICODE);
    }

    public function readAndWrite(): Response
    {
        $dir = '/var/www/php2/symfony_test/tmp/test.xlsx';
        $file = new \SplFileObject($dir, 'r');

        $data = $this->readFile($file);
        $this->writeFile($file, $data);

        $result = $this->readFile($file);

        return $this->render(
            'grade/readAndWrite.html.twig',
            ['result' => $result]
        );

    }

    private function readFile(\SplFileObject $file): array
    {
        $data = [];
        $reader = new SpreadsheetReader($file);

        foreach ($reader as $row) {
            foreach ($row as $item) {
                $data[] = $item;
            }
        }
        return $data;
    }

    private function writeFile(\SplFileObject $file, array $data): void
    {
        $writer = new SpreadsheetWriter($file);
        $writer->prepare();

        $newData = [];
        foreach ($data as $item) {
            if (isset($item)) {
                $newData[] = ++$item;
            }
        }

        $writer->writeItem($newData);
        $writer->finish();
    }
}

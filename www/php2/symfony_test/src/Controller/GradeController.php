<?php

namespace App\Controller;

use App\Entity\Fruit;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
        $dir = '/var/www/tmp/test.xlsx';
        $data = $this->readFile($dir);

        $this->writeFile($data, $dir);

        $result = $this->readFile($dir);
        return $this->render(
            'grade/readAndWrite.html.twig',
            ['result' => $result]
        );

    }

    private function readFile(string $dir): array
    {
        $inputType = 'Xlsx';

        $reader = IOFactory::createReader($inputType);
        $spreadsheet = $reader->load($dir);

        $workSheets = $spreadsheet->getWorksheetIterator();

        $data = [];
        foreach ($workSheets as $workSheet) {
            foreach ($workSheet->toArray() as $itemsArray) {
                foreach ($itemsArray as $item) {
                    $data[] = ++$item;
                }
            }
        }

        return $data;
    }

    private function writeFile(array $data, string $dir): void
    {
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();

        $activeSheet->getCell("A1")->setValue("RESULT");
        $tableHeaderStyles = [
            'font' => [
                'bold' => true,
            ],
        ];
        $activeSheet->getStyle("A1")->applyFromArray($tableHeaderStyles);

        $rowNum = 2;
        foreach ($data as $item) {
            $activeSheet->getCell("A$rowNum")->setValue($item);
            $rowNum += 1;
        }

        $lastRow = $rowNum - 1;
        $activeSheet->getCell("A$rowNum")->setValue("=SUM(A2:A$lastRow)");

        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($dir);
    }

    public function regulars(): Response
    {
        $pattern = '/^\w{1,10}@\w{1,10}.\w{1,3}$/';

        $mailOne = 'test@mail.ru';
        $mailTwo = 'test.mail@ru';
        $validateOne = preg_match($pattern, $mailOne);
        $validateTwo = preg_match($pattern, $mailTwo);


        $patternTwo = '/^(https):\/\/(profitbase).(ru)\/$/';
        $urlOne = 'https://profitbase.ru/';
        $urlTwo = 'https://pr0fitbase.ry/';

        $validateThree = preg_match($patternTwo, $urlOne);
        $validateFour = preg_match($patternTwo, $urlTwo);

        return $this->render(
            'grade/regulars.html.twig',
            [
                'one' => $validateOne,
                'two' => $validateTwo,
                'mailOne' => $mailOne,
                'mailTwo' => $mailTwo,
                'three' => $urlOne,
                'four' => $urlTwo,
                'validateThree' => $validateThree,
                'validateFour' => $validateFour
            ]
        );
    }

    public function startSessions(): Response
    {
        session_start();

        echo 'Here session is started';

        $_SESSION['phone'] = 'black';
        $_SESSION['car'] = 'brown';
        $_SESSION['door'] = 'white';

        echo '<br/><a href="/grade/getDataFromSession">page 2</a>';

        return $this->render(
            'grade/startSession.html.twig',
            []
        );
    }

    public function getDataFromSession()
    {
        session_start();

        $result = [];
        foreach ($_SESSION as $key => $item) {
            if (in_array($key, ['phone', 'car', 'door'])) {
                $result[] = $item;
            }
        }

        return $this->render(
            'grade/getDataFromSession.html.twig',
            ['result' => $result]
        );
    }
}

<?php


require_once 'classes/parsing.php';
error_reporting(0);
//use classes\parsing;
system("cls");


switch ($argv[1]) {
    case 'parse':
        if ($argv[2] && $argv[3]) {
            $pars = new parsing();
            $pars->setCurrentUrl($argv[2]);
            $pars->getImageLink();
            $pars->setInternalLinks();
            $test = $pars->getInternalLinks();
            $i = 0;
            while ($test) {
                $newLink = new parsing();
                $newLink->setCurrentUrl($test[$i]);
                $newLink->getImageLink();
                $pars->addInternalLink($test[$i]);
                $pars->setVisitedPages($test[$i]);
                $pars->deleteInternalLinks($test[$i]);


                if (count($pars->getVisitedPages()) > $argv[3]) {
                    echo 'Путь к файлу: ' . $pars->getPathToFile();
                    break;
                }
                $i++;
            }

        } else {
            echo "Ошибка,  воспользуйтесь справкой (help) \n";
        }
        break;
    case 'report':
        if (isset($argv[2])) {
            $report = new parsing();
            $report->setCurrentUrl($argv[2]);
            $result = $report->domainAnalysis();
            echo PHP_EOL . 'host: ' . $result[1]['host'] . PHP_EOL;
            echo 'class: ' . $result[1]['class'] . PHP_EOL;
            echo 'ttl: ' . $result[1]['ttl'] . PHP_EOL;
            echo 'ip: ' . $result[1]['ip'] . PHP_EOL;

        } else {
            echo "Ошибка параметра, воспользуйтесь справкой (help)\n";
        }
        break;
    case 'help':
        echo 'Доступные команды:' . PHP_EOL;
        echo 'parse(site.com , (int)limitPars), return - csv file(link in img)' . PHP_EOL;
        echo 'report(site.com), return - analise site' . PHP_EOL;
        echo 'help() return - list of teams';
        break;

    default:
        echo "Ошибка параметра, воспользуйтесь справкой (help) \n";
        break;
}

echo "\n";


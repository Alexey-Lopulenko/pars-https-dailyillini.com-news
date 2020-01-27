
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>pars</title>
</head>
<body>
<h1><center>pars news</center></h1>
<hr>
<form action="test.php" method="post">
    <p>Site: <input name="site" type="url"></p>
    <p><input type="submit" value="Enter"></p>

</form>
</body>
</html>

<?php
require_once 'classes/parsing.php';
require_once 'setting.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

$siteUrl = $_POST['site'];

    if($siteUrl){
        $pars = new parsing();
        $pars->setCurrentUrl($siteUrl);
//        $pars->getImageLink();
        $pars->setInternalLinks();
//        $test = $pars->getInternalLinks();
        $arrLinksNews = $pars->getNewsLinks();
        echo '<pre>';
            print_r($arrLinksNews);
        echo '</pre>';


        $sql = "INSERT INTO news (h1, text, created, img, url_page) VALUES (?,?,?,?,?)";
        $dataFromTableNews = $pdo->query("SELECT * FROM news")->fetchAll();



        $i = 0;
        while ($arrLinksNews) {
            $newLink = new parsing();
            $newLink->setCurrentUrl($arrLinksNews[$i]);
//            $newLink->getImageLink();

             $dateCreated = $newLink->getDateCreateNews();
             $h1 = $newLink->getHeadingNews();
             $textNews = $newLink->getTextNews();
             $img = $newLink->getImageUrl();
             if (!($img)){
                 $img = 'no image!';
             }

            echo '<hr>';
            echo '<pre>';
            print_r($arrLinksNews[$i]);
            echo '</pre>';
            echo '<hr>';

            if (!in_array($h1, $dataFromTableNews, true)){
                $pdo->prepare($sql)->execute([$h1, $textNews, $dateCreated, $img, $arrLinksNews[$i]]);
            }


            $pars->addInternalLink($arrLinksNews[$i]);
            $pars->setVisitedPages($arrLinksNews[$i]);
            $pars->deleteInternalLinks($arrLinksNews[$i]);


            if (count($pars->getVisitedPages()) >= 6) {
                break;
            }
            $i++;
        }
    }
}


?>



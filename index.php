<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>pars</title>
</head>
<body>
<h1>
    <center>pars news</center>
</h1>
<hr>
<form action="index.php" method="post">
    <p>Site: <input name="site" type="url" value="https://dailyillini.com/news/"></p>
    <p><input type="submit" value="Enter"></p>

</form>
</body>
</html>

<?php
ini_set("max_execution_time", 360);
require_once 'classes/parsing.php';
require_once 'setting.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $siteUrl = $_POST['site'];

    if ($siteUrl) {
        $pars = new parsing();
        $pars->setCurrentUrl($siteUrl);
        $pars->setInternalLinks();
        $arrLinksNews = $pars->getNewsLinks();
        echo '<pre>';
        print_r($arrLinksNews);
        echo '</p re>';

        $sql = "INSERT INTO news (h1, text, created, url_img_in_site, url_page, path_img_in_my_server,created_at) VALUES (?,?,?,?,?,?,?)";

        $i = 0;
        $countPage = 1;

        while ($arrLinksNews) {
            $newLink = new parsing();
            $newLink->setCurrentUrl($arrLinksNews[$i]);

            $dateCreated = $newLink->getDateCreateNews();
            $h1 = $newLink->getHeadingNews();
            $textNews = $newLink->getTextNews();
            $url_img_in_site = $newLink->getImageUrl();
            $path_img_in_my_server = $newLink->saveImage($url_img_in_site);
            $today = getdate();

            if (!($url_img_in_site)) {
                $url_img_in_site = 'no image!';
            }

            echo '<hr>';
            echo '<pre>';
            print_r($h1);
            echo '<br>';
            echo '<pre>';
            print_r($textNews);
            echo '<br>';
            print_r($url_img_in_site);
            echo '<br>';
            print_r($path_img_in_my_server);
            echo '</pre>';
            echo '<hr>';

            $stmt = $pdo->prepare('SELECT * FROM news WHERE h1 = :h1');
            $stmt->execute(['h1' => $h1]);
            $row = $stmt->fetch();

            //save in db
            if (!$row) {
                $pdo->prepare($sql)->execute([$h1, $textNews, $dateCreated, $url_img_in_site, $arrLinksNews[$i], $path_img_in_my_server, $today[0]]);
            } else {
                $sql = "UPDATE news SET created_at=? WHERE h1=?";
                $pdo->prepare($sql)->execute([$today, $h1]);
            }

            $pars->addInternalLink($arrLinksNews[$i]);
            $pars->setVisitedPages($arrLinksNews[$i]);
            $pars->deleteInternalLinks($arrLinksNews[$i]);

//            if ($i+1 == 3) {
//                break;
//            }
            if ($i + 1 == count($arrLinksNews)) {

                $i = 0;
                $nextPage = $nextPage = $pars->nextPage();
                if ($countPage == 3 || $nextPage == "No next page") {
                    echo '<strong>Limit</strong>';
                    break;
                }
                echo '<hr><pre>';
                print_r($nextPage);
                echo '</pre><hr>';
                $pars->setVisitedPagesInNull();
                $pars->setCurrentUrl($nextPage);
                $pars->setInternalLinks();
                $arrLinksNews = [];
                $arrLinksNews = $pars->getNewsLinks();

                echo '<pre>';
                print_r($arrLinksNews);
                echo '</pre><hr>';

                $countPage++;
            }
            $i++;
        }
    }
}


?>
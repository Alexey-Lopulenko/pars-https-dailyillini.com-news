<?php

class parsing
{
    public $currentUrl;
    public $htmlData;
    public $internalLinks = [];
    public $visitedPages = [];
    public $recordedLinks;

    public function __construct()
    {
        date_default_timezone_set('Europe/Kiev');
        $this->htmlData = $this->getDataFromSite();
    }

    /**
     * @param $url
     * @return mixed
     */
    public function validateUrl($url)
    {
        if (get_headers($url, 1)) {
            return $url;
        } else {
            if (get_headers('https://' . $url, 1)) {
                $this->setCurrentUrl('https://' . $url);
                return $this->getCurrentUrl();
            } elseif (get_headers('http://' . $url, 1)) {
                $this->setCurrentUrl('http://' . $url);
                return $this->getCurrentUrl();
            }
        }
    }


    /**
     * @param $link
     * @return bool
     */
    public function mainLink($link)
    {
        $pos = strpos($link, '://' . $this->getDomain());
        if ($pos === false) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * @param $link
     */
    public function deleteInternalLinks($link)
    {
        $arrInternalLinks = $this->getInternalLinks();
        if (in_array($link, $arrInternalLinks)) {
            unset($arrInternalLinks[array_search($link, $arrInternalLinks)]);
        }
        $this->internalLinks = [];

        foreach ($arrInternalLinks as $item) {
            array_push($this->internalLinks, $item);
        }
    }

    public function addInternalLink($link)
    {
        if (!(in_array($link, $this->getInternalLinks())) && !(in_array($link, $this->getVisitedPages()))) {
            array_push($this->internalLinks, $link);
        }
    }

    public function setInternalLinks()
    {
        foreach ($this->getAllLinksFromThePage() as $link) {
            if ($this->mainLink($link)) {
                array_push($this->internalLinks, $link);
            }
        }
    }

    public function getInternalLinks()
    {
        return $this->internalLinks;
    }

    public function getAllLinksFromThePage()
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($this->getDataFromSite());
        $allLinks = [];
        $xpath = new DOMXPath($dom);
        $hrefs = $xpath->evaluate("/html/body//a");

        for ($i = 0; $i < $hrefs->length; $i++) {
            $href = $hrefs->item($i);
            $urls = $href->getAttribute('href');
            array_push($allLinks, $urls . PHP_EOL);
        }
        return $allLinks;
    }

    /**
     * @return string|string[]|null
     */
    public function getDomain()
    {
        $url = trim($this->getCurrentUrl(), '/');
        if (!preg_match('#^http(s)?://#', $url)) {
            $url = 'http://' . $url;
        }
        $urlParts = parse_url($url);
        $domain = preg_replace('/^www\./', '', $urlParts['host']);
        return $domain;
    }




    public function getImageLink()
    {
        $file = "image.csv";
        $imagesLink = [];

        file_put_contents($file, "\n" . $this->getCurrentUrl() . "\n", FILE_APPEND | LOCK_EX);

        preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $this->getDataFromSite(), $media);
        unset($data);
        $data = preg_replace('/(img|src)("|\'|="|=\')(.*)/i', "$3", $media[0]);

        foreach ($data as $url) {
            $info = pathinfo($url);
            if (isset($info['extension'])) {
                if (($info['extension'] == 'jpg') ||
                    ($info['extension'] == 'jpeg') ||
                    ($info['extension'] == 'gif') ||
                    ($info['extension'] == 'png')) {
                    array_push($imagesLink, $url);
                }
            }
        }
        foreach ($imagesLink as $value) {
            file_put_contents($file, $value . "\n", FILE_APPEND | LOCK_EX);
            array_push($this->recordedLinks, $value);
        }
    }

    /**
     * @param $url
     */
    public function setCurrentUrl($url)
    {
        $this->currentUrl = $this->validateUrl($url);
    }

    /**
     * @return mixed
     */
    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    /**
     * @return false|string
     */
    public function getHtmlData()
    {
        return $this->htmlData;
    }


    /**
     * @return false|string
     */
    public function getDataFromSite()
    {
        if ($this->getCurrentUrl()) {
            $data = file_get_contents($this->getCurrentUrl());
            return $data;
        }
        return 'Error set CurrentUrl!';
    }

    /**
     * @param $visitedPage
     */
    public function setVisitedPages($visitedPage)
    {
        array_push($this->visitedPages, $visitedPage);
    }

    /**
     * @return array
     */
    public function getVisitedPages()
    {
        return $this->visitedPages;
    }

    /**
     * @return bool|string
     */
    public function getPathToFile()
    {
        $real_path = realpath("image.csv");
        return $real_path;
    }

    /**
     * @return array
     */
    public function domainAnalysis()
    {
        $domain = $this->getDomain();
        $result = dns_get_record($domain);

        return $result;
    }



    public function getNewsLinks()
    {
//        $arrH2 = [];
//        $arrDate  = [];
//        $arrTextNews  = [];

        preg_match_all('#<div\sclass="sno-animate">(.*)<\/div>#Uis', $this->getDataFromSite(),$allNews);

        foreach ($allNews[0] as $news){

       /*     preg_match_all('#<h2\sclass="searchheadline">(.*)<\/h2>#Uis', $news,$h2);
            preg_match_all('#<p\sclass="categorydate">(.*)<\/p>#Uis', $news,$date);
            preg_match_all('#<p\sclass="categorydate">(.*)<\/p>#Uis', $news,$date);
            array_push($arrH2, $h2);
            array_push($arrDate, $date);*/

            preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $news,$newsLink);
            $arrNewsLink[] = $newsLink;


        }

        foreach ($arrNewsLink as $link){
            $arrNormLink[] = $link[1][0];
        }

        return $arrNormLink;
    }

    public function getDateCreateNews()
    {
        $htmlData =  $this->getDataFromSite();
        preg_match_all('#<span\sclass="time-wrapper">(.*)<\/span>#Uis', $htmlData,$date);

        return strip_tags($date[0][6]);
    }

    public function getHeadingNews()
    {
        $htmlData =  $this->getDataFromSite();
        preg_match_all('#<h1\sclass="storyheadline">(.*)<\/h1>#Uis', $htmlData,$heading);

        return strip_tags($heading[0][0]);
    }

    /**
     * @return string
     */
    public function getTextNews()
    {

        $htmlData = $this->getDataFromSite();
        preg_match_all('#<span\sclass="storycontent">(.*)<\/span>#Uis', $htmlData, $textNews);
        preg_match_all('#<p\>(.*)<\/p>#Uis', $textNews[0][0], $textNewsAll);


        if (!($textNewsAll[1])) {
            preg_match_all('#<span\sclass="s[1-9]">(.*)<\/span>#Uis', $htmlData, $textNews);

            if (!($textNews[1])){
                preg_match_all('#<span\sclass="storycontent">(.*)<\/span>#Uis', $htmlData, $textNews);
                $strData = strip_tags($textNews[0][0]);
                return $strData;
            }else{
                $strData = implode(" ", $textNews[0]);
                $strData = strip_tags($strData);
                return $strData;
            }


        }

        $strData = implode(" ", $textNewsAll[0]);
        $strData = strip_tags($strData);


        if ($strData) {
            return $strData;
        } else {
            return 'Error tags!';
        }

    }

    /**
     * @return mixed
     * return url img in news page (site)
     */

    public function getImageUrl()
    {
        $htmlData = $this->getDataFromSite();
        preg_match_all('#<div\sclass="photowrap">(.*)<\/div>#Uis', $htmlData, $imgLink);

        if ($imgLink[0][0]) {
            preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $imgLink[0][0], $media);
            $urlImg = preg_replace('/(img|src)("|\'|="|=\')(.*)/i', "$3", $media[0]);

        }

        return $urlImg[0];
    }

    /**
     * @param $imgUrl
     * @return bool|null|string
     */
    public function saveImage($imgUrl)
    {
        if(get_headers($imgUrl, 1)){
            $pos = strripos($imgUrl, "/");
            $name = mb_substr($imgUrl, $pos + 1);
            $path = "img/" . $name;

            copy($imgUrl, $path);

            $real_path = realpath($path);
            return $real_path;
        }
        return Null;

    }
    public function nextPage()
    {
        $htmlData = $this->getDataFromSite();
        preg_match_all('#<a href="[^>]+" >Next\sPage\s&raquo;<\/a>#', $htmlData, $nextPageLink);
        preg_match_all('#(?<=")([\s\S]+?)(?=")#Uis', $nextPageLink[0][0], $href);

        return $href[0][0];

    }

    public function setVisitedPagesInNull(){
        $this->visitedPages = [];
    }

}
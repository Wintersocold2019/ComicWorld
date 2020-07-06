<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use KubAT\PhpSimple\HtmlDomParser;
use DB;

class ScraperController extends Controller
{
    public function execute($url) {
        // Curl init
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $reponse = curl_exec($ch);
        curl_close($ch);

        $dom = HtmlDomParser::str_get_html($reponse);

        return $dom;
    }

    public function getData($rootUrl) {
        $rootDom = $this->execute('https://' . $rootUrl);

        // Get data from hot group   
        $hotComicList = $rootDom->find('.list-truyen-hot li a');
        foreach($hotComicList as $comicItem) {
            $comicHref  = $comicItem->href;
            $comicDom   = $this->execute('https://' . $rootUrl . $comicHref);

            $title       = $comicDom->find('h1.title')[0]->plaintext;
            $img         = $comicDom->find('.cover img')[0]->src;
            $author      = trim(str_replace('Tác giả:', '',$comicDom->find('a.list-group-item')[0]->plaintext));
            $comic_type  = $comicDom->find('span.list-group-item span')[0]->plaintext;
            $source      = $comicDom->find('span.list-group-item span')[2]->plaintext;
            $status_type = $comicDom->find('.stt span')[0]->plaintext;
            $type        = $comicDom->find('.stt span')[1]->plaintext;
            $numOfChap   = str_replace(' Chương', '', $comicDom->find('.stt span')[2]->plaintext);
            $numOfView   = str_replace(' lượt đọc', '', $comicDom->find('.stt span')[3]->plaintext);
            $created_at  = $comicDom->find('time')[0]->plaintext;
            $description = $comicDom->find('.contentt')[0]->plaintext;
            $chapterList = $comicDom->find('.danh-sach-chuong a');

            dd($chapterList);
        }


        // $authors = DB::select('select * from author');
        return view('master');
    }
}

<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use KubAT\PhpSimple\HtmlDomParser;
use App\ComicTypes;
// use DB;

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

    public function getComicTypes($url) {
        $dom           = $this->execute('https://' . $url . '/the-loai.html');
        $comicTypeList = $dom->find('a[itemprop=significantLink]');

        foreach($comicTypeList as $comicTypeItem) {
            $comicTypeName    = $comicTypeItem->plaintext;
            $hasComicTypeName = ComicTypes::where('name', '=', $comicTypeName)->first();
            if ($hasComicTypeName === null) {
                $comicType       = new ComicTypes;
                $comicType->name = $comicTypeName;
                $comicType->href = str_replace('/the-loai', '',$comicTypeItem->href);
                $comicType->save();
            }
        }
    }

    public function getComicData($url) {
        $comicTypeList = ComicTypes::all();

        foreach($comicTypeList as $comicType) {
            $dom       = $this->execute('https://' . $url . '/the-loai' . $comicType->href);
            $comicList = $dom->find('.truyen-inner a');

            foreach($comicList as $comic) {
                $comicDom  = $this->execute('https://' . $url . $comic->href);
                $index = null;

                $title       = $comicDom->find('figcaption')[0]->plaintext;
                dd($title);
                $img         = $comicDom->find('.cover img')[0]->src;
                $author      = trim(str_replace('Tác giả:', '',$comicDom->find('a.list-group-item')[0]->plaintext));
                $comic_type = $comicDom->find('span.list-group-item a span');
                $comicTypeList = [];
                for($index = 0; $index < count($comic_type) / 2; $index++) {
                    $comicTypeList[] = $comic_type[$index];
                }
                $sourceValue = $comicDom->find('span[itemprop=isBasedOnUrl]');
                if ($sourceValue !== []) {
                    $source = $sourceValue[0]->plaintext;
                }
                $status_type = $comicDom->find('.stt span')[0]->plaintext;
                $type        = $comicDom->find('.stt span')[1]->plaintext;
                $numOfChap   = str_replace(' Chương', '', $comicDom->find('.stt span')[2]->plaintext);
                $numOfView   = str_replace(' lượt đọc', '', $comicDom->find('.stt span')[3]->plaintext);
                $created_at  = $comicDom->find('time')[0]->plaintext;
                $description = $comicDom->find('.contentt')[0]->plaintext;
                $chapterList = $comicDom->find('.danh-sach-chuong a');
            }
        }
    }

    public function getData($rootUrl) {
        // $this->getComicTypes($rootUrl);
        $this->getComicData($rootUrl);

        // $authors = DB::select('select * from author');
        echo 'Scrape data from https://' . $rootUrl .' successfully!';
    }
}

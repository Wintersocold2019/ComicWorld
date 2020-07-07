<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use KubAT\PhpSimple\HtmlDomParser;
use App\ComicTypes;
use App\Authors;
use App\StatusTypes;
use App\Types;

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
        $dom            = $this->execute('https://' . $url . '/the-loai.html');
        $comicTypeList  = $dom->find('a[itemprop=significantLink]');
        $comicTypeCount = 0;

        foreach($comicTypeList as $comicTypeItem) {
            $comicTypeName    = $comicTypeItem->plaintext;
            $hasComicTypeName = ComicTypes::where('name', '=', $comicTypeName)->first();

            if (is_null($hasComicTypeName)) {
                $comicType       = new ComicTypes;
                $comicType->name = $comicTypeName;
                $comicType->href = str_replace('/the-loai', '',$comicTypeItem->href);
                $comicType->save();

                $comicTypeCount++;
            }
        }
        
        if ($comicTypeCount > 0) {
            echo 'Created ' . $comicTypeCount . ' comic types successfully!';
        } else {
            echo 'No comic type data need to update.';
        }
    }

    public function getAuthorId($authorName) {
        $hasAuthorName = Authors::where('name', '=', $authorName)->first();

        if (is_null($hasAuthorName)) {
            $author       = new Authors;
            $author->name = $authorName;
            $author->save();

            return $author->id;
        }

        return $hasAuthorName->id;
    }

    public function getStatusTypeId($statusType) {
        $hasStatusType = StatusTypes::where('name', '=', $statusType)->first();

        if (is_null($hasStatusType)) {
            $statusType       = new StatusTypes;
            $statusType->name = $statusType;
            $statusType->save();

            return $statusType->id;
        }

        return $hasStatusType->id;
    }

    public function getTypesId($type) {
        $hasType = Types::where('name', '=', $type)->first();

        if (is_null($hasType)) {
            $type       = new Types;
            $type->name = $type;
            $type->save();

            return $type->id;
        }

        return $hasType->id;
    }

    public function getComicTypeIds($comicTypeList) {
        $comicTypeIdList = [];

        foreach($comicTypeList as $comicType) {
            $comicTypeId = ComicTypes::where('name', '=', $comicType)->first()->id;
            $comicTypeIdList[] = $comicTypeId;
        }

        return $comicTypeIdList;
    }

    public function getComicData($url) {
        $comicTypeList = ComicTypes::all();

        foreach($comicTypeList as $comicType) {
            $dom       = $this->execute('https://' . $url . '/the-loai' . $comicType->href);
            $comicList = $dom->find('.truyen-inner a');

            foreach($comicList as $comic) {
                $comicDom      = $this->execute('https://' . $url . $comic->href);
                $index         = null;
                $comicTypeList = [];
                $chapterList   = [];

                $title       = $comicDom->find('figcaption')[0]->plaintext;
                $img         = $comicDom->find('.cover img')[0]->src;
                $authorName  = trim(str_replace('Tác giả:', '',$comicDom->find('a.list-group-item')[0]->plaintext));

                $comic_type = $comicDom->find('span.list-group-item a span');      
                for($index = 0; $index < count($comic_type) / 2; $index++) {
                    $comicTypeList[] = $comic_type[$index]->plaintext;
                }

                $sourceValue = $comicDom->find('span[itemprop=isBasedOnUrl]');
                $source      = $sourceValue !== [] ? $sourceValue[0]->plaintext : null; 

                $status_type = $comicDom->find('.stt span')[0]->plaintext;
                $type        = $comicDom->find('.stt span')[1]->plaintext;
                $numOfChap   = str_replace(' Chương', '', $comicDom->find('.stt span')[2]->plaintext);
                $numOfView   = str_replace(' lượt đọc', '', $comicDom->find('.stt span')[3]->plaintext);
                $created_at  = $comicDom->find('time')[0]->plaintext;
                $description = $comicDom->find('.contentt')[0]->plaintext;

                $chapters = $comicDom->find('.danh-sach-chuong');
                foreach($chapters as $chapter) {
                    $chapterList[] = $chapter->plaintext;
                }
                dd('https://' . $url . $comic->href);
            }
        }
    }

    public function getData($rootUrl) {
        $this->getComicTypes($rootUrl);
        $this->getComicData($rootUrl);

        echo 'Scrape data from https://' . $rootUrl .' successfully!';
    }
}

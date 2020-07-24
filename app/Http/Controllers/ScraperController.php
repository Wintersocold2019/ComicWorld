<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use KubAT\PhpSimple\HtmlDomParser;
use App\ComicTypes;
use App\Authors;
use App\StatusTypes;
use App\Types;
use App\ComicDetail;
use App\Comics;
use App\Chapters;

class ScraperController extends Controller
{
    // Get dom HTML
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

    // Add comic type data
    public function getComicTypes($url) {
        $dom           = $this->execute('https://' . $url . '/the-loai.html');
        $comicTypeList = $dom->find('a[itemprop=significantLink]');

        foreach($comicTypeList as $comicTypeItem) {
            $comicType = new ComicTypes;
            $comicType->add($comicTypeItem->plaintext, str_replace('/the-loai', '',$comicTypeItem->href));
        }  

        echo 'Get comic type data successfully!<br />';
    }

    public function getComicTypeIds($comicTypeList) {
        $comicTypeIdList = [];

        foreach($comicTypeList as $comicType) {
            $comicTypeId = ComicTypes::where('name', '=', $comicType)->first()->id;
            $comicTypeIdList[] = $comicTypeId;
        }

        return $comicTypeIdList;
    }

    // Get another data
    public function getComicData($url) {
        $comicTypeList = ComicTypes::all();

        foreach($comicTypeList as $comicTypeItem) {
            $pageIndex               = null;
            $dom                     = $this->execute('https://' . $url . '/the-loai' . $comicTypeItem->href);
            $comicTypePageList   = [];
            $comicTypePageList[] = '/the-loai' . $comicTypeItem->href;
            $pageList = $dom->find('.pagination li a');
            if ($pageList !== []) {
                for($pageIndex = 0; $pageIndex < count($pageList) - 1; $pageIndex++) {
                    $comicTypePageList[] =$pageList[$pageIndex]->href;
                }
            }

            foreach($comicTypePageList as $comicTypePageItem) {
                $pageDom = $this->execute('https://' . $url . $comicTypePageItem);

                $comicList = $pageDom->find('.title');
                foreach($comicList as $comicItem) {
                    $comicDom      = $this->execute('https://' . $url . $comicItem->href);
                    $index         = null;
                    
                    $title       = $comicDom->find('h1.title')[0]->plaintext;         
                    $img         = $comicDom->find('.cover img')[0]->src;
                    $authorName  = trim(str_replace('Tác giả:', '',$comicDom->find('a.list-group-item')[0]->plaintext));
                    $comic_type = $comicDom->find('.list-group-item a span');      
                    
                    $sourceValue = $comicDom->find('span[itemprop=isBasedOnUrl]');
                    $source      = $sourceValue !== [] ? $sourceValue[0]->plaintext : ''; 

                    $statusTypeName = $comicDom->find('.stt span')[0]->plaintext;

                    $typeValue = $comicDom->find('.stt span')[1];
                    $typeName  = null;
                    $typeId    = null;
                    if (strpos($typeValue, 'Chương') === false) {
                        $typePosition = 1;
                        $chapterPosition = 2;
                        $viewPosition = 3;

                        $typeName = $comicDom->find('.stt span')[$typePosition]->plaintext;
                    } else {
                        $chapterPosition = 1;
                        $viewPosition = 2;
                    }          
                    $numOfChap   = (int)str_replace(' Chương', '', $comicDom->find('.stt span')[$chapterPosition]->plaintext);
                    $numOfView   = (int)str_replace(' lượt đọc', '', $comicDom->find('.stt span')[$viewPosition]->plaintext);
                    $description = $comicDom->find('.contentt')[0]->plaintext;
                    $chapters    = $comicDom->find('.danh-sach-chuong .chuong-item');

                    // Add author data
                    $author   = new Authors;
                    $authorId = $author->getId($authorName);

                    if ($typeName !== null) {
                        // Add type data
                        $type   = new Types;
                        $typeId = $type->getId($typeName);
                    }
                    
                    // Add status type data
                    $statusType   = new StatusTypes;
                    $statusTypeId = $statusType->getId($statusTypeName);

                    // Add comic data
                    $comic = new Comics;
                    $comic->add($title, $comicTypeItem->href, $description, $numOfChap, $numOfView, $source, $img, $authorId, $typeId, $statusTypeId);
                    $comicId = $comic->getId($title);

                    // Add comic detail data
                    $comicType = new ComicTypes;
                    for($index = 0; $index < count($comic_type) / 2; $index++) {
                        $comicTypeName = $comic_type[$index]->plaintext;
                        $comicTypeId   = $comicType->getId($comicTypeName);

                        $comicDetail = new ComicDetail;
                        $comicDetail->add($comicId, $comicTypeId);
                    }
                    
                    // Add chapter data
                    foreach($chapters as $chapterItem) {
                        $chapterName    = $chapterItem->plaintext;
                        if (strpos($chapterName, 'Chương') !== false || strpos($chapterName, 'chương') !== false) {
                            $chapterDom     = $this->execute('https://' . $url . $chapterItem->href);
                            $chapterContent = $chapterDom->find('div[itemprop=articleBody]')[0]->plaintext;
                            
                            $chapter = new Chapters;
                            $chapter->add($chapterName, $chapterContent, $comicId);         
                        }                   
                    }   
                    break;          
                }
            }         
            break;
        }

        echo 'Get comic data successfully!';
    }

    public function getData($rootUrl) {
        $this->getComicTypes($rootUrl);
        $this->getComicData($rootUrl);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComicTypes;

class PageController extends Controller
{
    public function getComicTypes() {
        $comicTypes = ComicTypes::all();

        return view('master', compact('comicTypes'));
    }

    public function toAdmin() {
        return view('admin');
    }
}

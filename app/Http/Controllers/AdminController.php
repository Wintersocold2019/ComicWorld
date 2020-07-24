<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function deleteData() {
        DB::table('comic_detail')->delete();
        DB::table('chapters')->delete();
        DB::table('comics')->delete();
        DB::table('authors')->delete();
        DB::table('comic_types')->delete();
        DB::table('status_types')->delete();
        DB::table('types')->delete();

        echo 'Delete all data successfully!';
    }
}

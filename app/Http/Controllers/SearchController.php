<?php

namespace App\Http\Controllers;

class SearchController extends Controller
{
    public function search()
    {
    	$search = request('search');
    	session()->flash('searchValue', $search);
    	return redirect('/');
    }
}

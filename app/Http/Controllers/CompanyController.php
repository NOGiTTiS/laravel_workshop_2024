<?php

namespace App\Http\Controllers;

class CompanyController extends Controller
{
    public function index()
    {
        //dd('company');
        return view('company');
    }
}

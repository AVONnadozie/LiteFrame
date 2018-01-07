<?php

namespace Controllers;

class AppController extends Controller
{
    public function __construct()
    {
        $this->middleware('sample', ['index']);
    }

    public function index()
    {
        $data['time'] = \Carbon\Carbon::now();

        return view('home', $data);
    }

    public function test()
    {
        echo 'Test page';
    }
}

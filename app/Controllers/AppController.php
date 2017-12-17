<?php

namespace Controllers;

class AppController extends Controller
{
    public function index()
    {
        $data['time'] = \Carbon\Carbon::now();

        return view('home', $data);
    }
    
    public function docs()
    {
        $data['time'] = \Carbon\Carbon::now();

        return view('docs', $data);
    }

    public function test()
    {
        echo 'Test page';
    }
}

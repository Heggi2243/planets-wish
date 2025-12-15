<?php
namespace controllers;

class HomeController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => '首頁',
            'message' => '歡迎來到 Planets Wish'
        ];
        
        $this->view('views/welcome', $data);
    }
}
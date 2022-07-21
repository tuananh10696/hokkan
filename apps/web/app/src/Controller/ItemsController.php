<?php

namespace App\Controller;

use App\Controller\AppController;

class ItemController extends AppController
{
    public function index()
    {
        $this->viewBuilder()->layout('simple');
    }
}
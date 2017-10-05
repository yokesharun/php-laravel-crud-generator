<?php

namespace Yokesharun\Laravelcrud\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CRUDController extends Controller
{

    public function index()
    {
        return view('laravelcrud::crud.index');
    }

}
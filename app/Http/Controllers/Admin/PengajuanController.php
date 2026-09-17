<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index() { return view('welcome'); }
    public function show($id) { return view('welcome'); }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    public function index() { return view('welcome'); }
    public function profile() { return view('welcome'); }
    public function updateProfile(Request $request) { return back(); }
    public function updatePassword(Request $request) { return back(); }
}

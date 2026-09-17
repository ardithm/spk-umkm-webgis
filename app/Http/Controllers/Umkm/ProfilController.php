<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function show() { return view('welcome'); }
    public function edit() { return view('welcome'); }
    public function update(Request $request) { return back(); }
}

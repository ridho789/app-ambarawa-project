<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;

class ActivityController extends Controller
{
    public function index() {
        $activities = Activity::orderBy('action_time')->get();
        return view('contents.activity', compact('activities'));
    }
}

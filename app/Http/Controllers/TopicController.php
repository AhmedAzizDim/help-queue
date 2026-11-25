<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function show(Topic $topic): View
    {
        return view('topics.show', [
            'topic' => $topic,
            'tickets' => $topic->tickets()->with('topic')->orderBy('id')->get(),
        ]);
    }
}

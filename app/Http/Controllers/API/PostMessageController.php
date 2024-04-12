<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostMessage;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPost;


class PostMessageController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => '請登入會員'], 401);
        }

        $articleExists = UserPost::where('WID', $request->WID)->exists();
        if (!$articleExists) {
            return response()->json(['error' => '該文章不存在'], 404);
        }

        $user = Auth::user();

        $request->validate([
            'WID' => 'required|exists:UserPost,WID',
            'MSGPost' => 'required|string',
            'user_id' => 'exists:users,id', 
        ]);

        $postMessage = new PostMessage;
        $postMessage->WID = $request->WID;
        $postMessage->UID = $user->UID;
        $postMessage->MSGPost = $request->MSGPost;
        $postMessage->user()->associate($user);
        $postMessage->save();

        return response()->json([
            'message' => '評論建立成功',
            'postmessage' => $postMessage
        ]);
    }

    public function index(Request $request)
    {
        $articleExists = UserPost::where('WID', $request->WID)->exists();
        if (!$articleExists) {
            return response()->json(['error' => '該文章不存在'], 404);
        }
        
        $request->validate([
            'WID' => 'required|exists:UserPost,WID',
        ]);

        $postMessages = PostMessage::where('WID', $request->WID)
            ->with('user:id,name') 
            ->get();

        $postMessages = $postMessages->map(function ($postMessage) {
            return [
                'MSGWID' => $postMessage->MSGWID,
                'MSGPost' => $postMessage->MSGPost,
                'MSGPostTime' => $postMessage->MSGPostTime,
                'user_name' => $postMessage->user->name,
            ];
        });

        return response()->json([
            'postMessages' => $postMessages
        ]);
    }

}


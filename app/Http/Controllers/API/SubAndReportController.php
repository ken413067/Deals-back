<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserPost;
use App\Models\PostMessage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SubAndReport;
use Illuminate\Support\Facades\Auth;

class SubAndReportController extends Controller
{
    public function subscribe(Request $request, $userId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => '請登入會員'], 401);
        }

        $targetUser = User::find($userId);
        if (!$targetUser) {
            return response()->json(['error' => '用戶不存在'], 404);
        }

        $subscription = SubAndReport::where('UID', $user->id)
            ->where('TargetUID', $userId)
            ->first();

        if ($subscription) {
            $subscription->delete();
            $message = '已取消訂閱';
        } else {
            SubAndReport::create([
                'UID' => $user->id,
                'TargetUID' => $userId,
            ]);
            $message = '訂閱成功';
        }

        return response()->json([
            'message' => $message,
        ]);
    }


    public function checkSubscription($userId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => '請登入會員'], 401);
        }

        $isSubscribed = SubAndReport::where('UID', $user->id)
            ->where('TargetUID', $userId)
            ->exists();

        return response()->json([
            'isSubscribed' => $isSubscribed,
        ]);
    }


    public function storeTarget(Request $request, $articleId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => '請登入會員'], 401);
        }

        $article = UserPost::find($articleId);
        if (!$article) {
            return response()->json(['error' => '文章不存在'], 404);
        }

        $subscription = SubAndReport::where('UID', $user->id)
            ->where('TargetWID', $articleId)
            ->first();

        if ($subscription) {
            $subscription->delete();
            $message = '取消收藏';
        } else {
            SubAndReport::create([
                'UID' => $user->id,
                'TargetWID' => $articleId,
            ]);
            $message = '收藏成功';
        }

        return response()->json([
            'message' => $message,
        ]);
    }

    public function checkFavorite($articleId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => '請登入會員'], 401);
        }

        $isFavorited = SubAndReport::where('UID', $user->id)
            ->where('TargetWID', $articleId)
            ->exists();

        return response()->json([
            'isFavorited' => $isFavorited,
        ]);
    }


    public function reportArticle(Request $request, $articleId)
    {
        $article = UserPost::where('WID', $articleId)->first();
        if (!$article) {
            return response()->json([
                'error' => '指定的文章不存在',
            ], 404);
        }

        $request->validate([
            'ReportContent' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => '用戶未登錄'], 401);
        }


        $subAndReport = new SubAndReport;
        $subAndReport->UID = $user->id;
        $subAndReport->ReportWID = $articleId;
        $subAndReport->ReportContent = $request->ReportContent;

        $subAndReport->save();

        return response()->json([
            'message' => '已成功發送文章檢舉信',
            'subAndReport' => $subAndReport
        ]);
    }

    public function reportComment(Request $request, $commentID)
    {
        $comment = PostMessage::where('MSGWID', $commentID)->first();
        if (!$comment) {
            return response()->json([
                'error' => '指定的評論不存在',
            ], 404);
        }

        $request->validate([
            'ReportContent' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => '用戶未登錄'], 401);
        }

        $subAndReport = new SubAndReport;
        $subAndReport->UID = $user->id;
        $subAndReport->ReportMSGWID = $commentID;
        $subAndReport->ReportContent = $request->ReportContent;

        $subAndReport->save();

        return response()->json([
            'message' => '已成功發送評論檢舉信',
            'subAndReport' => $subAndReport
        ]);
    }
}
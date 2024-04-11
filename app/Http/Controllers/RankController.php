<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rank;
use Illuminate\Support\Facades\DB;

class RankController extends Controller
{
    public function index(Request $request)
    {
        $posts = DB::table('UserPost')->get();

        $likeCounts = DB::table('UserPost')
            ->join('LikeAndDislike', 'UserPost.WID', '=', 'LikeAndDislike.WID')
            ->select(
                'UserPost.WID',
                DB::raw('SUM(LikeAndDislike.GiveLike) as total_likes'),
                DB::raw('SUM(LikeAndDislike.GiveDislike) as total_dislikes')
            )
            ->groupBy('UserPost.WID')
            ->get();

        // 將 $likeCounts 轉換成字典，以便根據 WID 訪問
        $likeCountsDict = [];
        foreach ($likeCounts as $likeCount) {
            $likeCountsDict[$likeCount->WID] = [
                'total_likes' => $likeCount->total_likes,
                'total_dislikes' => $likeCount->total_dislikes
            ];
        }

        // 合併文章訊息、點讚數量和倒讚數量
        $mergedData = [];
        foreach ($posts as $post) {
            $mergedData[] = [
                "WID" => $post->WID,
                "UID" => $post->UID,
                "Title" => $post->Title,
                "Article" => $post->Article,
                'ItemLink' => $post->ItemLink,
                'ItemIMG' => $post->ItemIMG,
                'PostTime' => $post->PostTime,
                'ChangeTime' => $post->ChangeTime,
                'ConcessionStart' => $post->ConcessionStart,
                'ConcessionEnd' => $post->ConcessionEnd,
                'ReportTimes' => $post->ReportTimes,
                'Hiding' => $post->Hiding,
                'location_tag' => $post->location_tag,
                'product_tag' => $post->product_tag,
                // 添加點讚數量信息，如果不存在則默認為 0
                "total_likes" => isset ($likeCountsDict[$post->WID]['total_likes']) ? $likeCountsDict[$post->WID]['total_likes'] : '0',
                // 添加倒讚數量信息，如果不存在則默認為 0
                "total_dislikes" => isset ($likeCountsDict[$post->WID]['total_dislikes']) ? $likeCountsDict[$post->WID]['total_dislikes'] : '0',
            ];
        }

        // 根據請求中的類別進行過濾文章
        $category = $request->input('category');
        if (!$category) {
            // 如果類別為空，回傳所有文章
            return response()->json(["merged_data" => $mergedData]);
        } else {
            // 如果類別不為空，回傳相對應類別的文章
            $filteredData = array_filter($mergedData, function ($item) use ($category) {
                return trim($item['product_tag']) === $category;
            });
            return response()->json(["merged_data" => array_values($filteredData)]);
        }
    }
    
    // return response()->json(["merged_data" => $mergedData]);}
}
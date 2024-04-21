<?php

namespace App\Http\Controllers;

use Fukuball\Jieba\Finalseg;
use Illuminate\Http\Request;
use App\Models\Rank;
use Fukuball\Jieba\Jieba;
use Illuminate\Support\Facades\DB;


Jieba::init();
Finalseg::init();
class RankController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->token;
        $uid = null;

        if ($token) {
            $decoded_token = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
            $uid = $decoded_token->id;
        }

        $posts = DB::table('UserPost')
            ->leftJoin('LikeAndDislike', 'UserPost.WID', '=', 'LikeAndDislike.WID')
            ->leftjoin('users', 'UserPost.UID', '=', 'users.id')
            ->select(
                'UserPost.*',
                'users.name',
                'users.image',

                DB::raw('IFNULL(SUM(LikeAndDislike.GiveLike), "0") as total_likes'),
                DB::raw('IFNULL(SUM(LikeAndDislike.GiveDislike), "0") as total_dislikes')
            )
            ->groupBy(
                'UserPost.WID',
                'UserPost.UID',
                'UserPost.InProgress',
                'UserPost.Title',
                'UserPost.Article',
                'UserPost.ItemLink',
                'UserPost.ItemIMG',
                'UserPost.PostTime',
                'UserPost.ChangeTime',
                'UserPost.ConcessionStart',
                'UserPost.ConcessionEnd',
                'UserPost.ReportTimes',
                'UserPost.Hiding',
                'UserPost.location_tag',
                'UserPost.product_tag',
                'users.name',
                'users.image',

            );


        // $posts = DB::table('UserPost')
        //     ->leftJoin('users', 'UserPost.UID', '=', 'users.id')
        //     ->select('UserPost.*', 'users.name as author_name')
        //     ->get();

        // $likeCounts = DB::table('UserPost')
        //     ->join('LikeAndDislike', 'UserPost.WID', '=', 'LikeAndDislike.WID')
        //     ->select(
        //         'UserPost.WID',
        //         DB::raw('SUM(LikeAndDislike.GiveLike) as total_likes'),
        //         DB::raw('SUM(LikeAndDislike.GiveDislike) as total_dislikes')
        //     )
        //     ->groupBy('UserPost.WID')
        //     ->get();
        // // echo $likeCounts;
        // // 將 $likeCounts 轉換成字典，以便根據 WID 訪問
        // $likeCountsDict = [];
        // foreach ($likeCounts as $likeCount) {
        //     $likeCountsDict[$likeCount->WID] = [
        //         'total_likes' => $likeCount->total_likes,
        //         'total_dislikes' => $likeCount->total_dislikes
        //     ];
        // }

        // // 合併文章訊息、點讚數量和倒讚數量
        // $mergedData = [];
        // foreach ($posts as $post) {
        //     $mergedData[] = [
        //         "WID" => $post->WID,
        //         "UID" => $post->UID,
        //         "author_name" => $post->author_name,
        //         "Title" => $post->Title,
        //         "Article" => $post->Article,
        //         'ItemLink' => $post->ItemLink,
        //         'ItemIMG' => $post->ItemIMG,
        //         'PostTime' => $post->PostTime,
        //         'ChangeTime' => $post->ChangeTime,
        //         'ConcessionStart' => $post->ConcessionStart,
        //         'ConcessionEnd' => $post->ConcessionEnd,
        //         'ReportTimes' => $post->ReportTimes,
        //         'Hiding' => $post->Hiding,
        //         'location_tag' => $post->location_tag,
        //         'product_tag' => $post->product_tag,
        //         // 添加點讚數量信息，如果不存在則默認為 0
        //         "total_likes" => isset($likeCountsDict[$post->WID]['total_likes']) ? $likeCountsDict[$post->WID]['total_likes'] : '0',
        //         // 添加倒讚數量信息，如果不存在則默認為 0
        //         "total_dislikes" => isset($likeCountsDict[$post->WID]['total_dislikes']) ? $likeCountsDict[$post->WID]['total_dislikes'] : '0',
        //     ];
        // }

        // 如果 uid 存在，僅返回使用者自己的文章
        if ($uid) {
            $posts->where('UserPost.UID', $uid);
        }
        // 取得文章資料
        $posts = $posts->get();

        // 根據請求中的類別進行過濾文章
        $category = $request->input('category');
        $seg_list = Jieba::cutForSearch($request->input('search'));

        if (!$category && !$seg_list) {
            // 如果類別和搜尋條件都為空，回傳所有文章
            return response()->json(["merged_data" => $posts]);
        }

        // 如果有類別，先根據類別過濾文章
        if ($category) {
            $posts = $posts->filter(function ($item) use ($category) {
                return trim($item->product_tag) === $category;
            });
        }

        // 如果有搜尋條件，再根據搜尋條件過濾文章
        if ($seg_list) {
            $searchlist = array();
            foreach ($seg_list as $seg) {
                foreach ($posts as $post) {
                    $title = $post->Title;
                    $article = $post->Article;
                    if (stripos($title, $seg) !== false || stripos($article, $seg) !== false) {
                        if (!in_array($post, $searchlist)) {
                            array_push($searchlist, $post);
                        }
                    }
                }
            }
            return response()->json([
                'merged_data' => $searchlist,
            ]);
        }

        // 返回過濾後的文章資料
        return response()->json(["merged_data" => $posts]);
    }
}
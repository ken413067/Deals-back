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
  $seg_list = Jieba::cutForSearch($request->input('search'));

  // return $seg_list;
  // return $search;
  //     if (!$category) {
  //         // 如果類別為空，回傳所有文章
  //         return response()->json(["merged_data" => $mergedData]);
  //     } else {
  //         // 如果類別不為空，回傳相對應類別的文章
  //         $filteredData = array_filter($mergedData, function ($item) use ($category) {
  //             return trim($item['product_tag']) === $category;
  //         });
  //         return response()->json(["merged_data" => array_values($filteredData)]);
  //     }
  // }

  // return response()->json(["merged_data" => $mergedData]);}

  if (!$category && !$seg_list) {
      // 如果類別和搜尋條件都為空，回傳所有文章
      return response()->json(["merged_data" => $mergedData]);
  }

  // 如果有類別，先根據類別過濾文章
  if ($category) {
      $mergedData = array_filter($mergedData, function ($item) use ($category) {
          return trim($item['product_tag']) === $category;
      });
  }

  // 如果有搜尋條件，再根據搜尋條件過濾文章
  if ($seg_list) {
      $searchlist = array();
      foreach ($seg_list as $seg) { //第一個迴圈 跑斷詞迴圈
        //   echo "斷詞: " . $seg . "\n";
          
          foreach ($mergedData as $value) { //第二個迴圈 匹配到的文章 加入到空陣列
              $title = $value['Title'];
              $article = $value['Article'];
          
              // return $title;
              if (stripos($title, $seg) !== false || stripos($article, $seg) !== false) {
                  if (!in_array($value, $searchlist)) { // 檢查是否已經存在相同的資料  in_array()是否存在 list = false  , !in_array() 是否不存在 list = true   true執行 
                      array_push($searchlist, $value); // 如果不存在，才添加
                  }
              }
          }
          
      }
      // echo "下面是回傳";
      // echo "\n";

      return response()->json([

          'merged_data' => $searchlist,
      ]);
  }

  // 返回過濾後的文章資料
  return response()->json(["merged_data" => array_values($mergedData)]);
}
}
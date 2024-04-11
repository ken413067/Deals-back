<?php

namespace App\Http\Controllers;

require_once "C:/xampp/htdocs/Prologin2/vendor/autoload.php";

use App\Http\Controllers\Controller;
use App\Models\Search;
use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\Finalseg;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\YuenArticle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Jieba::init();
Finalseg::init();


class SearchController extends Controller
{
    public function search(Request $request)
    {
        
        // Models版本
        $seg_list = Jieba::cutForSearch($request->search); #搜索引擎模式
        // return $seg_list;
        $searchlist = array();
        foreach ($seg_list as $key => $value) { //第一個迴圈 我去匹配文章
            echo "斷詞: " . $value . "\n";
            $search = YuenArticle::where("Title" ,"like", "%".$value."%" )->get();  // 雲端
            // $search = Article::where("article_title" ,"like", "%".$value."%" )->get(); //本地端
            // return $search;
            // $search = Article::select("id")->where("article_title", "like", "%" . $value . "%")->get();
            
            foreach ($search as $value) { //第二個迴圈 匹配到的文章 加入到空陣列
                // echo gettype($value);
                // echo "value: ".$value."\n";
                array_push($searchlist, $value);
                
                
            }
    }
        
    
        // echo "搜尋結果: "; // array_push()完的陣列
        // foreach ($searchlist as $value) { // 第三個迴圈 是顯示 第二個迴圈 匹配到的文章  檢查用 沒有也可以
        //     // return $value;
        //     // $formattedDate = date('Y-m-d H:i:s', strtotime($value->timestamp));
        //     // $value->formattedDate = $formattedDate; 
        //     // $formattedDatetime = date('Y-m-d H:i:s', strtotime($value->datetime));
        //     // $value->formattedDatetime = $formattedDatetime;
        //     // return $value;
        //     // echo $formattedDate;
        //     echo $value;
        //     echo "\n";
        // }


        // echo "去重複後結果: ";
        $searchlist = array_unique($searchlist);
        // return $searchlist;
        // foreach ($searchlist as $value) {
        //     echo $value;
        //     echo "\n";
        // }
        echo "下面是回傳";
        echo "\n";
        // Article::select("id")->where("article_title", )

        return response()->json([
            
            'search' => $searchlist,
        ]);
    }
}





    //     public function search(Request $request){
//         $seg_list = Jieba::cutForSearch($request->search); #搜索引擎模式
//         $searchlist = [];
//         foreach ($seg_list as $key => $value){
//             $search = Search::select("item")->where("item" ,"like", "%".$value."%" )->get();
//             echo $search[1];
//             // echo $search[0]."item";
//             // echo $search; // 陣列中有物件
//             // echo gettype($search);  //物件
//             array_push($searchlist, $search);
            
//             // var_dump($searchlist);
//         //     // echo $search;
//         //     // echo $value;
//         //     // echo "\n";
//         //     // echo gettype($search -> item) ;
//         //     // echo $searchlist ;
//         // }
//         // var_dump($seg_list);

//         // return ;
//     }
    
// }



// // DB版本
// $seg_list = Jieba::cutForSearch($request->search); #搜索引擎模式
// $searchlist = array();
// foreach ($seg_list as $key => $value) { //第一個迴圈 我去匹配文章
//     echo "斷詞: " . $value . "\n";
//     // $search = DB::table("UserPost")->where("Title" ,"like", "%".$value."%" )->get();
//     $search = DB::table("article")->where("article_title" ,"like", "%".$value."%" )->get();
//     // $search = Article::where("article_title" ,"like", "%".$value."%" )->get();
//     // return $search;
//     // $search = Article::select("id")->where("article_title", "like", "%" . $value . "%")->get();
//     // return $searchlist;
    
//     // foreach ($search as $value) { //第二個迴圈 匹配到的文章 加入到空陣列
//         // echo gettype($value);
//         // echo "value: ".$value."\n";
//         // return $value;
//         // array_push($searchlist, $value);

//         foreach ($search as $value) { //第二個迴圈 匹配到的文章 加入到空陣列
//             // 檢查該項目是否已經存在於$searchlist中
//             $exists = false;
//             foreach ($searchlist as $existingItem) {
//                 if ($existingItem == $value) {
//                     $exists = true;
//                     break;
//                 }
//             }
            
//             if (!$exists) {
//                 $searchlist[] = $value;
//             }
//     }
// }
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPost;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserPostController extends Controller
{
    public function store(Request $request)
    {
        // if (!Auth::check()) {
        //     return response()->json(['error' => '請登入會員'], 401);
        // }
        // 拿token
        $token = $request->token;
        // echo $token;
        $decoded_token = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
        $id = $decoded_token->id;

        $request->validate([
            'product_tag' => 'nullable|string',
            'location_tag' => 'nullable|string',
            'title' => 'required|string',
            'itemImg' => 'nullable',
            'concessionStart' => 'nullable|date',
            'concessionEnd' => 'nullable|date|after:concessionStart',
            'Article' => 'required|string',
            'ItemLink' => 'nullable|string',
            'InProgress' => 'nullable|string',
        ]);
        $imagedata = null;

        if (isset($request->itemImg)) {
            $file = $request->itemImg->get();
            $imagedata = base64_encode($file);
        }
        // 活動開始時間及結束時間的判斷

        if ($request->has('concessionStart')) {
            $concessionStart = Carbon::parse($request->concessionStart)->format('Y-m-d');
        } else {
            $concessionStart = null;
        }

        if ($request->has('concessionEnd')) {
            $concessionEnd = Carbon::parse($request->concessionEnd)->format('Y-m-d');
        } else {
            $concessionEnd = null;
        }
        // 處理上傳時間
        $postTime = Carbon::now()->tz('Asia/Taipei');
        // 圖片預設

        $article = DB::table('UserPost')->insert([
            'Title' => $request->title,
            'ItemIMG' => $imagedata,
            'Article' => $request->Article,
            'UID' => $id,
            'PostTime' => $postTime->toDateTimeString(), // 使用 Carbon 格式化時間
            // 'updated_at' => $request->ChangeTime ? Carbon::parse($request->ChangeTime)->tz('Asia/Taipei')->format('Y年m月d日 H:i') : null,
            'ItemLink' => $request->ItemLink,
            'product_tag' => $request->product_tag,
            'location_tag' => $request->location_tag,
            'concessionStart' => $concessionStart,
            'concessionEnd' => $concessionEnd,
            'InProgress' => $request['InProgress'],

        ]);

        return response()->json(['article' => $article]);
    }
    // public function sss(Request $request)
    // {
    //     // return $request;
    //     // $token = $request->token;
    //     // // echo $token;
    //     // $decoded_token = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
    //     // $id = $decoded_token->id;
    //     // if (!Auth::check()) {
    //     //     return response()->json(['error' => '請登入會員'], 401);
    //     // }

    //     // $user = Auth::user();

    //     $request->validate([
    //         'product_tag' => 'nullable|string',
    //         'location_tag' => 'nullable|string',
    //         'title' => 'required|string',
    //         'itemImg' => 'required',
    //         // 'concessionStart' => 'nullable|date',
    //         // 'concessionEnd' => 'nullable|date|after:concessionStart',
    //         'Article' => 'required|string',
    //         // 'ItemLink' => 'nullable|string',
    //     ]);

    //     $article = new UserPost;
    //     $article->title = $request->title;
    //     $article->Article = $request->Article;
    //     // $article->ConcessionStart = $request->concessionStart;
    //     // $article->ConcessionEnd = $request->concessionEnd;        
    //     // $article->product_tag = $request->product_tag;
    //     // $article->location_tag = $request->location_tag;
    //     // $article->UID = $user->UID;
    //     // $article->ItemLink = $request->ItemLink; 


    //     if ($request->hasFile('itemImg')) {
    //         $file = $request->file('itemImg');
    //         $binaryData = file_get_contents($file->getPathname());
    //         $article->ItemIMG = $binaryData;
    //     }

    //     $itemImgBase64 = base64_encode($article->ItemIMG);
    //     // return $itemImgBase64;


    // public function store(Request $request)
    // {
    //     $token = $request->token;
    //     // echo $token;
    //     $decoded_token = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
    //     $id = $decoded_token->id;
    //     // if (!Auth::check()) {
    //         //     return response()->json(['error' => '請登入會員'], 401);
    //         // }

    //         // $user = Auth::user();

    //         // $request->validate([
    //             //     'product_tag' => 'nullable|string',
    //             //     'location_tag' => 'nullable|string',
    //             //     'title' => 'required|string',
    //             //     'itemImg' => 'nullable|image|max:2048',
    //             //     'concessionStart' => 'nullable|date',
    //             //     'concessionEnd' => 'nullable|date|after:concessionStart',
    //             //     'Article' => 'required|string',
    //             //     'ItemLink' => 'nullable|string',
    //             // ]);

    //             $article = new UserPost;
    //             $article->title = $request->title;
    //             $article->Article = $request->Article;
    //             $article->ConcessionStart = $request->concessionStart;
    //             $article->ConcessionEnd = $request->concessionEnd;        
    //             $article->product_tag = $request->product_tag;
    //             $article->location_tag = $request->location_tag;
    //             $article->ItemIMG = $request->itemImg;
    //             $article->UID = $id;
    //             $article->ItemLink = $request->ItemLink; 

    //             // return $request;
    //             if ($request->hasFile('itemImg')) {
    //                 $file = $request->file('itemImg');
    //                 $binaryData = file_get_contents($file->getPathname());
    //                 $article->ItemIMG = $binaryData;
    //             }

    //             $itemImgBase64 = base64_encode($article->ItemIMG);
    //             // $itemImgBase64 = base64_encode(file_get_contents($article->ItemIMG));
    //             // return $article;
    //             $abc = "data:image/png;base64,";
    //             $itemImgBase64W = $abc . $itemImgBase64;
    //             // return $itemImgBase64W;
    //         //     // $article->user()->associate($user);
    //             // return $article;
    //         //     $article->save();

    // //     return response()->json([
    // //         'message' => '文章建立成功',
    // //         'title' => $article->title,
    // //         'Article' => $article->Article,
    // //         // 'user_name' => $article->user ? $article->user->name : null,
    // //         // 'created_at' => Carbon::parse($article->PostTime)->tz('Asia/Taipei')->format('Y年m月d日 H:i'),
    // //         // 'updated_at' => $article->ChangeTime ? Carbon::parse($article->ChangeTime)->tz('Asia/Taipei')->format('Y年m月d日 H:i') : null,
    // //         'itemImg' => $itemImgBase64,
    // //         // 'ItemLink' => $article->ItemLink,
    // //         // 'product_tag' => $article->product_tag,
    // //         // 'location_tag' => $article->location_tag,
    // //         // 'concessionStart' => $article->ConcessionStart,
    // //         // 'concessionEnd' => $article->ConcessionEnd,
    // //     ]);
    // // }


    public function index()
    {
        $perPage = 150;
        $articles = UserPost::with('user:id,name')
            ->orderBy('PostTime', 'desc')
            ->paginate($perPage);

        $articlesTransformed = $articles->map(function ($article) {
            $itemImgBase64 = base64_encode($article->ItemIMG);

            $title = $article->InProgress === '已過期' ? '<span class="expired-title">[已過期]</span>  ' . $article->Title . '</span>' : $article->Title;

            return [
                'wid' => $article->WID,
                'title' => $title,
                'Article' => $article->Article,
                'user_name' => $article->user ? $article->user->name : null,
                'created_at' => Carbon::parse($article->PostTime)->format('Y年m月d日 H:i'),
                'updated_at' => $article->ChangeTime ? Carbon::parse($article->ChangeTime)->format('Y年m月d日 H:i') : null,
                'itemImg' => $itemImgBase64,
                'ItemLink' => $article->ItemLink,
                'product_tag' => $article->product_tag,
                'location_tag' => $article->location_tag,
                'concessionStart' => $article->ConcessionStart,
                'concessionEnd' => $article->ConcessionEnd,
            ];
        });

        return response()->json([
            'data' => $articlesTransformed,
            'links' => [
                'first' => $articles->url(1),
                'last' => $articles->url($articles->lastPage()),
                'prev' => $articles->previousPageUrl(),
                'next' => $articles->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $perPage,
                'total' => $articles->total(),
            ],
        ]);
    }

    //編輯文章
    public function UpdatePost(Request $request)
    {

        $token = $request->token;
        // echo $token;
        $decoded_token = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
        $id = $decoded_token->id;
        // if (!Auth::check()) {
        //     return response()->json(['error' => '請登入會員'], 401);
        // }

        $user = Auth::user();
        // $request->validate([
        //     'product_tag' => 'nullable|string',
        //     'location_tag' => 'nullable|string',
        //     'title' => 'required|string',
        //     'itemImg' => 'nullable|image|max:2048',
        //     'concessionStart' => 'nullable|date',
        //     'concessionEnd' => 'nullable|date|after:concessionStart',
        //     'Article' => 'required|string',
        //     'ItemLink' => 'nullable|string',
        // ]);


        $article = new UserPost;
        $article->Title = $request->title;
        $article->Article = $request->Article;
        $article->ConcessionStart = $request->concessionStart;
        $article->ConcessionEnd = $request->concessionEnd;
        $article->product_tag = $request->product_tag;
        $article->location_tag = $request->location_tag;
        // $article->UID = $user->UID;
        $article->ItemLink = $request->ItemLink;

        if ($request->hasFile('itemImg')) {
            $file = $request->file('itemImg');
            $binaryData = file_get_contents($file->getPathname());
            $article->ItemIMG = $binaryData;
        }

        $itemImgBase64 = base64_encode($article->ItemIMG);


        $email = DB::table("users")
            ->join("UserPost", "users.id", "=", "UserPost.UID")
            ->where("users.id", $id)
            ->select("email")
            ->get();


        $original_image = DB::table("UserPost")->select("itemIMG")->where("email", "=", $email);
        $original_Title = DB::table("UserPost")->select("Title")->where("email", "=", $email);
        $original_Article = DB::table("UserPost")->select("Article")->where("email", "=", $email);
        $original_ConcessionStart = DB::table("UserPost")->select("ConcessionStart")->where("email", "=", $email);
        $original_ConcessionEnd = DB::table("UserPost")->select("ConcessionEnd")->where("email", "=", $email);

        $updateData = [];
        if ($article->ItemIMG != "" && ($article->ItemIMG != $original_image)) {
            $updateData["image"] = $article->ItemIMG;
            $src = $article->ItemIMG;
        } else if ($article->ItemIMG = "") {
            $src = $original_image;
        }

        if ($article->Title != "" && ($article->Title != $original_Title)) {
            $updateData["Title"] = $article->Title;
        }

        if ($article->Article != "" && ($article->Article != $original_Article)) {
            $updateData["Article"] = $article->Article;
        }
        if ($article->ConcessionStart != "" && ($article->ConcessionStart != $original_ConcessionStart)) {
            $updateData["ConcessionStart"] = $article->ConcessionStart;
        }
        if ($article->ConcessionEnd != "" && ($article->ConcessionEnd != $original_ConcessionEnd)) {
            $updateData["ConcessionEnd"] = $article->ConcessionEnd;
        }

        DB::table("UserPost")->where("UID", "=", $id)->update($updateData);
        // die("OK");

        return response()->json([
            // "src" => $itemImgBase64,
            'message' => 'Item updated successfully',
        ]);
    }

    //刪除文章
    public function destroy(Request $request, $wid)
    {
        // 从请求中获取token并解析出用户ID
        $token = $request->token;
        $decoded_token = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
        $userIdFromToken = $decoded_token->id;
        // 查找要删除的文章
        $article = UserPost::find($wid);
        
        // 确保文章存在
        if (!$article) {
            return response()->json(['message' => '文章不存在'], 404);
        }
        
        // 检查解码出的用户ID是否与文章的UID匹配
        if ($article->UID !== $userIdFromToken) {
            return response()->json(['message' => '无权删除此文章'], 403);
        }
        
        // 删除文章
        $article->delete();
        
        return response()->json(['message' => '文章已删除'], 200);
    }

    public function show($id)
    {
        $article = UserPost::with('user:id,name')->find($id);

        if (!$article) {
            return response()->json(['error' => '文章不存在'], 404);
        }

        $itemImgBase64 = $article->ItemIMG ? base64_encode($article->ItemIMG) : "";

        $articleTransformed = [
            'wid' => $article->WID,
            'title' => $article->Title,
            'Article' => $article->Article,
            'user_name' => $article->user ? $article->user->name : null,
            'user_id' => $article->user ? $article->user->id : null, // 添加用户ID
            'created_at' => Carbon::parse($article->PostTime)->format('Y年m月d日 H:i'),
            'updated_at' => $article->ChangeTime ? Carbon::parse($article->ChangeTime)->format('Y年m月d日 H:i') : null,
            'itemImg' => $itemImgBase64,
            'ItemLink' => $article->ItemLink,
            'product_tag' => $article->product_tag,
            'location_tag' => $article->location_tag,
            'concessionStart' => $article->ConcessionStart,
            'concessionEnd' => $article->ConcessionEnd,
        ];

        return response()->json($articleTransformed);
    }

    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'nullable|string',
            'product_tag' => 'nullable|string',
            'location_tag' => 'nullable|string',
        ]);

        $query = UserPost::query();

        if ($request->has('keyword')) {
            $query->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('Article', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->has('product_tag')) {
            $query->where('product_tag', $request->product_tag);
        }

        if ($request->has('location_tag')) {
            $query->where('location_tag', $request->location_tag);
        }

        $articles = $query->with('user:id,name')->orderBy('PostTime', 'desc')->paginate(10);

        $articlesTransformed = $articles->map(function ($article) {
            $itemImgBase64 = base64_encode($article->ItemIMG);

            return [
                'title' => $article->Title,
                'Article' => $article->Article,
                'user_name' => $article->user ? $article->user->name : null,
                'created_at' => Carbon::parse($article->PostTime)->format('Y年m月d日 H:i'),
                'updated_at' => $article->ChangeTime ? Carbon::parse($article->ChangeTime)->format('Y年m月d日 H:i') : null,
                'itemImg' => $itemImgBase64,
                'ItemLink' => $article->ItemLink,
                'product_tag' => $article->product_tag,
                'location_tag' => $article->location_tag,
                'concessionStart' => $article->ConcessionStart,
                'concessionEnd' => $article->ConcessionEnd,
            ];
        });

        return response()->json([
            'data' => $articlesTransformed,
            'links' => [
                'first' => $articles->url(1),
                'last' => $articles->url($articles->lastPage()),
                'prev' => $articles->previousPageUrl(),
                'next' => $articles->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

}
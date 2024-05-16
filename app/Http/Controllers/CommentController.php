<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comment = Comment::with('replies','user')->where('parent_id',0)->orderBy('created_at', 'DESC')->get();
        if (Auth::check()) {
            $user = Auth::user();
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
            ];
        }else{
            $userData = '';
        }
        return response()->json([
            'success' => true,
            'comment' => $comment,
            'user' => $userData
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $comments = $request->all();
            $user = User::query()->where('id',$request->user_id)->get();

            $urlHash = md5(url()->to('/'));
            $comments['url'] = $urlHash;
            $domain = $comments['url'];
            $urlModel = \App\Models\Url::firstOrCreate(['url' => $domain]);
            $comment = new \App\Models\Comment();
            $comment->url_id = $urlModel->id;
            $comment->fill($comments);
            $comment->save();
            return response()->json([
                'success' => true,
                'message' => 'Comment created successfully',
                'comment' => $comment,
                'user' => $user
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create comment'
            ], 500);
        }
    }

    public function checkAuth(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
            ];
            return response()->json([
                'isAuthenticated' => true,
                'user' => $userData
            ]);
        } else {
            return response()->json(['isAuthenticated' => false]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}

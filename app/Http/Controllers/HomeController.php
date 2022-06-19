<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Tag;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = \Auth::user();
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        return view('home', compact('user','memos'));
    }

    public function create()
    {
        $user = \Auth::user();
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        return view('create', compact('user', 'memos'));
    }


    public function store(Request $request)
    {
        $data = $request->all();

        $exist_tag = Tag::where('name', $data['tag'])->where('user_id', $data['user_id'])->first();
        if( empty($exist_tag['id']) ){
            $tag_id = Tag::insertGetId(["name" => $data['tag'], 'user_id' => $data['user_id']]);
        }else{
            $tag_id = $exist_tag['id'];
        };
        Memo::insertGetId(['content' => $data['content'], 'user_id' => $data['user_id'], 'status' => 1, 'tag_id' => $tag_id]);

        return redirect()->route('home');
    }

    public function edit($id)
    {
        $user = \Auth::user();
        $memo = Memo::where('user_id', $user['id'])->where('id', $id)->where('status', 1)->first();
        $tags = Tag::where('user_id', $user['id'])->get();
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        return view('edit', compact('memo', 'user', 'memos' , 'tags'));
    }

    public function update(Request $request, $id)
    {
        $inputs = $request->all();
        Memo::where('id', $id)->update(['content' => $inputs['content'], 'tag_id' => $inputs['tag_id']]);
        return redirect()->route('home');
    }


}

<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(){
        return view("onstagram.main.users");
    }

    public function userDatatable(Request $request) {
        $users = User::query()->where("role",ROLE_USER);

        $columns = array(
            0 => 'id',
            1 => 'photo',
            2 => 'name',
            3 => 'email',
            4 => 'created_at'
        );

        $totalData = $users->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value'))) {
            $userList = $users->offset($start)->limit($limit)->orderBy($order,$dir)->get();
        } else {
            $search = $request->input('search.value');

            $userList = User::query()->where('name','LIKE',"%{$search}%")
                ->orWhere('last_name', 'LIKE',"%{$search}%")
                ->orWhere('email', 'LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
            $totalFiltered = User::query()->where('name','LIKE',"%{$search}%")
                ->orWhere('last_name', 'LIKE',"%{$search}%")
                ->orWhere('email', 'LIKE',"%{$search}%")
                ->count();
        }

        $data = array();
        if(!empty($userList)) {
            foreach ($userList as $key => $user) {
                $userId = $user->id;
                $totalPost = Post::query()->where(["user_id" => $userId])->count();

                $nestedData['id'] = $userId;
                $nestedData['avatar'] = $user->photo;
                $nestedData['full_name'] = $user->name." ".$user->last_name;
                $nestedData['email'] = $user->email;
                $nestedData['created_at'] = date('Y/m/d',strtotime($user->created_at));
                $nestedData['is_ban'] = $user->is_ban;
                $nestedData['total_post'] = $totalPost;
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );
        return json_encode($json_data);
    }

    public function userAction(Request $request){
        $userId = $request->userId;
        $action = $request->action;

        DB::beginTransaction();
        try {
            $row = User::query()->where(['id' => $userId])->update(['is_ban' => $action]);
            DB::commit();
            if ($row > 0){
                return response()->json(["result" => "success", "data" => null, "message" => "Data updated."]);
            }
            return response()->json(["result" => "error", "data" => null, "message" => "Nothing to update."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["result" => "error", "data" => null, "message" => $e->getMessage()]);
        }
    }

    public function wall($userId){
        $adminId = Auth::guard('admin')->id();
        $posts = Post::query()->where(["user_id" => $userId])->orderBy("id","desc")->get();

        $PostController = new PostController();
        foreach ($posts as $key => $post){
            $posts[$key] = $PostController->getPostData($adminId,$post);
        }
        return view("onstagram.main.wall",compact("posts"));
    }
}

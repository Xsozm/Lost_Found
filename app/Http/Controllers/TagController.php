<?php

namespace App\Http\Controllers;

use App\Item;
use Validator;
use Illuminate\Http\Request;
use App\Tag;
use Illuminate\Support\Facades\DB;


class TagController extends Controller
{

    function create_new_tag(Request $request){
        $rules = ['tag' => 'bail|required|string|min:2|max:20|unique:tags,name'];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()){
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }
        $tag= $request->tag;
        $newtag = new Tag();
        $newtag->name=$tag;
        $newtag->save();
        return response()->json('Tag Created Successfully',200);

    }



    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('verified');

    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index($item_id)
    {
        $item=Item::find($item_id);
        if($item==null){
            return response()->json('Item Not Found',404);

        }
        $user=auth()->user();


        if ($item->user_id!=$user->id && $item->role!='admin'){
            return response()->json('Unauthorized Action',401);

        }
        $tags = $item->tags;

        return response()->json($tags);

    }

    public function show_not_confirmed_tags(){
        $user = auth()->user();
        if ($user->role != 'admin'){
            return response()->json('Unauthorized Action',401);
        }
        $tags= Db::table('tags')->where('confirmed','=',false)->get();



        return response()->json($tags);

    }

    public function show_confirmed_tags(){
        $user = auth()->user();
        if ($user->role != 'admin'){
            return response()->json('Unauthorized Action',401);
        }

        $tags= Db::table('tags')->where('confirmed','=',true)->get();

        return response()->json($tags);

    }

    public function show_all_tags(){
        $user = auth()->user();
        if ($user->role != 'admin'){
            return response()->json('Unauthorized Action',401);
        }

        $tags = Tag::all();

        return response()->json($tags);

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$item_id)
    {
        $item=Item::find($item_id);
        if($item==null){
            return response()->json('Item Not Found',404);

        }
        $user=auth()->user();


        if ($item->user_id!=$user->id && $item->role!='admin'){
            return response()->json('Unauthorized Action',401);

        }




        $rules = [
            'tags' => 'bail|required|array|min:1|max:5',
            "tags.*"  => "bail|required|string|distinct|min:3:max:50"];

        $validator =Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }
        $names = $request->tags;
        foreach ($names as $name){
            $tag = Tag::where('name','=',$name);
            if($tag!=null ){
                $item->tags()->attach($tag->id);
            }
        }



        $item->save();
        return response()->json('Tags Added Successfully to the Item',200);







    }


    public function update_tags_for_specific_item(Request $request,$item_id){
        $item=Item::find($item_id);
        if($item==null){
            return response()->json('Item Not Found',404);

        }
        $user=auth()->user();


        if ($item->user_id!=$user->id && $item->role!='admin'){
            return response()->json('Unauthorized Action',401);

        }



        $rules = [
            'tags' => 'bail|required|array|min:1|max:5',
            "tags.*"  => "bail|required|string|distinct|min:3:max:50"];

        $validator =Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }
        $names = $request->tags;
        $item->tags()->detach();
        foreach ($names as $name){
            $tag = Tag::where('name','=',$name);
            if($tag!=null ){
                $item->tags()->attach($tag->id);
            }
        }



    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function delete_tag($id){
        $tag = Tag::find($id);

        if($tag==null)
        {
            return response()->json('Tag Not Found',404);
        }
        $user=auth()->user();

        if($user->role=='admin'){
            $tag->delete();
            return response()->json('Deleted Successfully',200);


        }else{
            return response()->json('Unauthorized Action',401);

        }

    }
    public function destroy($item_id,$tag_id)
    {
        $item=Item::find($item_id);
        if($item==null){
            return response()->json('Item Not Found',404);

        }
        $user=auth()->user();


        if ($item->user_id==$user->id || $item->role=='admin'){
            $tags = $item->tags;
            foreach ($tags as $tag) {
                if($tag->id==$tag_id)
                $item->tags()->detach($tag_id);
            }
            $item->save();
            return response()->json('Tag Deleted for this item',200);

        }else{
            return response()->json('Unauthorized Action',401);

        }


    }
}

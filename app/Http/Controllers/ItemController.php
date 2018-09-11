<?php

namespace App\Http\Controllers;
use App\Tag;
use Validator;
use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ItemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('verified');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role!='admin'){
            return response()->json("UnAuthorized Action",401);
        }
        $items = Item::all();

        return response()->json($items,200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)

    {


        $rules = [
            'name'=>'bail|required|min:3|max:255',
            'place' => 'bail|required|max:255',
            'found' =>'bail|required|boolean',
            'description' =>'bail|max:255'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }

        $user = auth()->user();

        $item = new Item();
        $item->name=$request->input('name');
        $item->place=$request->input('place');
        $item->found=$request->input('found');
        $item->description=$request->input('description');
        $item->user_id=$user->id;
        $item->save();
        return response()->json(array('message'=>'Created Successfully','item'=>$item),200);



        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
        $item = Item::find($id);
        if ($item==null){
            return response()->json("Item Not Found",404);
        }

        $user = auth()->user();

        if ($user->role=='admin' || $item->user_id ==$user->id)
        return response()->json($item,200);
        else
            return response()->json('Unauthorized Action',401);


    }


    public function show_all_items(){
        $user = auth()->user();
        if ($user->role != 'admin'){
            return response()->json('Unauthorized Action,you need to be an admin to list all items',401);
        }
        $items= Item::all();



        return response()->json($items);

    }

    public function show_confirmed_items(){
        $user = auth()->user();
        if ($user->role != 'admin'){
            return response()->json('Unauthorized Action,you need to be an admin ',401);
        }
        $items= Db::table('items')->where('confirmed','=',true)->get();



        return response()->json($items);
    }

    public function show_not_confirmed_items(){
        $user = auth()->user();
        if ($user->role != 'admin'){
            return response()->json('Unauthorized Action',401);
        }
        $items= Db::table('items')->where('confirmed','=',false)->get();



        return response()->json($items);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name'=>'bail|required|min:3|max:255',
            'place' => 'bail|required|max:255',
            'found' =>'bail|required|boolean',
            'description' =>'max:255'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }



        $item = Item::find($id);


        if($item==null)
        {
            return response()->json('Item Not Found',404);
        }



        $user = auth()->user();

        if($item->uder_id!=$user->id && $user->role!='admin'){
            return response()->json('Unauthorized Action',401);
        }

        $item->name=$request->input('name');
        $item->place=$request->input('place');
        $item->found=$request->input('found');
        $item->description=$request->input('description');
        $item->user_id=$user->id;
        $item->save();
        return response()->json(['message'=>'Updated Successfully','item'=>$item],200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::find($id);

        if($item==null)
        {
            return response()->json('Item Not Found',404);
        }
        $user=auth()->user();

        if(isset($item->user_id) && $item->user_id==$user->id || $user->role=='admin'){
            $item->delete();
            return response()->json('Deleted Successfully',200);


        }else{
            return response()->json('Unauthorized Action',401);

        }

    }

    function in($item,$string){
        return (strpos($item->name, $string) !== false || strpos($item->place, $string) !== false || strpos($item->description, $string) !== false);

    }
    function in2($item,$string){

        $tags = $item->tags;
        foreach ($tags as $tag){
            if(strpos($tag->name, $string)!==false)
                return true;
        }
        return false;

    }

    public function search($string){
        $items= Db::table('items')->where('confirmed','=',true)->get();
        $ans = collect();
        foreach ($items as $item){
            if($this->in($item,$string) || $this->in2($item,$string))
                $ans->push($item);

        }
        return $ans;
    }

    function searchbytag($string){

        $tag = Tag::where('name','=',$string)->first();
        if($tag==null){
            return response()->json("Tag doesn't exist",404);
        }
        return response()->json($tag->items,200);
    }
}

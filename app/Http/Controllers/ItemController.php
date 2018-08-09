<?php

namespace App\Http\Controllers;
use Validator;
use App\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        return response()->json($item,200);

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
                'errors' => $validator->errors()

            ), 400); // 400 being the HTTP code for an invalid request.
        }



        $item = Item::find($id);


        if($item==null)
        {
            return response()->json('Item Not Found',404);
        }


        $user = auth()->user();

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
        $item->delete();
        return response()->json('Deleted Successfully',200);

    }
}

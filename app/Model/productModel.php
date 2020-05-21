<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;

class productModel extends Model
{
    //
    protected $table='product';
    protected $cate_product, $media;
    public function __construct()
    {
        $this->cate_product= new cate_productModel();
        $this->media= new mediaModel();
    }
    public function listAll()
    {
        $item=productModel::orderBy('created_at','DESC')->get();

//        $item = DB::table('product')
//            ->join('cate_product', 'product.id', '=', 'cate_product.idproduct')
//            ->join('category', 'category.id', '=', 'cate_product.idcategory')
//            ->select('product.*', 'category.*', 'product.id as productId','product.title as productTitle')
//            ->orderByRaw('product.created_at DESC')
//            ->get();
        return $item;
    }
    public function addItem(Request $request)
    {
        try{
            $stringSize='';
            $stringColor='';
            $item= new productModel();
            $item->title=$request->title;
            $item->price=$request->price;
            $item->sale=$request->sale;
            if(isset($request->size))
            {
                foreach ($request->size as $itemSize)
                {
                    $stringSize=$stringSize.$itemSize.',';
                }
                $item->size=$stringSize;
            }
            if(isset($request->color))
            {
                foreach ($request->color as $itemColor)
                {
                    $stringColor=$stringColor.$itemColor.",";
                }
                $item->color=$stringColor;
            }
            $item->count=$request->count;
            $item->content=$request->content;
            if($request->hasFile('coverimg'))
            {
                $filename=$request->coverimg->getClientOriginalName();
                $item->coverimg=$filename;
                $request->coverimg->move('public/media',$filename);
                //$request->coverimg->storeAs('media',$filename);
            }
            $item->save();
            $this->cate_product->addItem($item->id,$request->idcategory);
            $this->media->addItem($request, $item->id,null);
            return true;
        }
        catch (Exception $ex){
            report($ex);
            return false;
        }


    }
    public function showItem($id)
    {
        $item=productModel::find($id);
        return $item;
    }
    public function updateItem(Request $request, $id)
    {
        try{
            $stringSize='';
            $stringColor='';
            $item=productModel::find($id);
            $item->title=$request->title;
            $item->price=$request->price;
            $item->sale=$request->sale;
            if(isset($request->size))
            {
                foreach ($request->size as $itemSize)
                {
                    $stringSize=$stringSize.$itemSize.',';
                }
                $item->size=$stringSize;
            }
            if(isset($request->color))
            {
                foreach ($request->color as $itemColor)
                {
                    $stringColor=$stringColor.$itemColor.",";
                }
                $item->color=$stringColor;
            }
            $item->color=$stringColor;
            $item->count=$request->count;
            $item->content=$request->content;
            if($request->hasFile('coverimg'))
            {
                $filename=$request->coverimg->getClientOriginalName();
                $item->coverimg=$filename;
                $request->coverimg->move('public/media',$filename);
                //$request->coverimg->storeAs('media',$filename);
            }
            $item->save();
            $this->cate_product->addItem($item->id,$request->idcategory);
            $this->media->addItem($request, $item->id,null);
            return true;
        }catch (Exception $ex)
        {
            report($ex);
            return false;
        }

    }
    public function deleteItem($id)
    {
        try{
            productModel::destroy($id);
            return true;
        }catch (Exception $ex)
        {
            report ($ex);
            return false;
        }
    }
    public function product()
    {
        $items = DB::table('product')
            ->select('product.*')
            ->get();
        return $items;
    }
    public function searchItem($search)
    {
        $items = DB::table('product')
            ->where('product.title', 'like', '%'.$search.'%')
            ->select('product.*')
            ->get();
        if(count($items) == 0)
        {
            $items=$this->searchCategoryProduct($search);
        }
        return $items;
    }
    public function searchCategoryProduct($search)
    {
        $items = DB::table('product')
            ->join('cate_product', 'product.id', '=', 'cate_product.idproduct')
            ->join('category', 'category.id', '=', 'cate_product.idcategory')
            ->where('category.title', 'like', '%'.$search.'%')
            ->groupBy('product.id','product.coverimg','product.title','product.content','product.price','product.sale')
            ->select('product.id','product.coverimg','product.title','product.content','product.price','product.sale')
            ->get();
        return $items;
    }
    public function listProductCate($id)
    {
        $items = DB::table('cate_product')
            ->join('product','cate_product.idproduct','=','product.id')
            ->where('cate_product.idcategory',$id)
            ->get();
        return $items;
    }
    public function searchPrice($min, $max)
    {
        $items = DB::table('product')
            ->select('product.*', 'product.id')
            ->whereBetween('price', [$min, $max])
            ->get();
        return $items;
    }
}

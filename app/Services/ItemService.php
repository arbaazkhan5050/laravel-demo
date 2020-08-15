<?php

namespace App\Services;
use App\Items;
use App\ItemImages;

class ItemService
{

    public function storeItem($request)
    {
      $item = Items::create($request->all());
      if ($request->file('images')) {
          $this->addImages($request, $item->id);
      }
      if ($item->id) {
          return response()->json(array('success' => true));
      }else {
          return response()->json(array('success' => false));
      }
    }

    public function updateItem($request)
    {
      $Item = Items::findOrFail($request->id);
      $Item = $Item->update($request->all());
      $this->addImages($request, $request->id, $request->primaryImageType);
      if ($request->file('documents')) {
          $this->addDocuments($request, $request->id);
      }
      return response()->json($Item);
    }

    public function showItem($id)
    {
        $item = Items::with('images')->findOrFail($id);
        $data['product'] = $item;
        return response()->json($item);
    }


    public function getItemList()
    {
        $items = Items::with('images')->get();
        return response()->json($items);
    }
    
    
    public function destroyItem($id)
    {
      ItemImages::where('item_id', '=', $id)->delete();
      $Item = Items::where('id', '=', $id)->delete();
     
      return response()->json($Item);
    }

    public function deleteImage($id, $type){
      if($type == 'image'){
        $productImage = ItemImages::find($id);
        if($productImage)
            \File::delete(base_path() . '/public/uploads/images/' . $productImage->url);
            $productImage->delete();
        }else{
          return response()->json('Not Found');
        }
    }

    public function addImages($data, $itemId, $type = 'add'){
       
      if (!empty($data->file('images'))) {
        $images = $data->file('images');
        foreach ($images as $key => $image) {
            $imageName = $image->getClientOriginalName();
            $fileName = uniqid() . '-' . strtolower($imageName);
            $directory = public_path('/uploads/images/');
            $image->move($directory, $fileName);
            $itemImages = new ItemImages();
            $itemImages->item_id = $itemId;
            $itemImages->url = $fileName;
            $itemImages->primary_image = ($data['primaryImage'] == $imageName)? true :false;
            $itemImages->save();
        }
    }
    if($type == 'old'){
      ItemImages::where('item_id', $itemId)->update(['primary_image' => false]);
      ItemImages::where('item_id', $itemId)
            ->where('url', $data['primaryImage'])
            ->update(['primary_image' => true]);
    }

  }

}

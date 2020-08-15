<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Services\ItemService;

use App\ItemImages;
use App\Items;

//storage
use Illuminate\Support\Facades\Storage;


class ItemController extends Controller
{

      /** App\Services\ItemService */
      private ItemService $item_service;

      /**
       * __construct
       *
       * @param  App\Services\UserService $user_service
       *
       * @return void
       */

    public function __construct(ItemService $item_service){
          $this->item_service = $item_service;
    }

    public function index()
    {
		$get_item_result = $this->item_service->getItemList();
        return $get_item_result;
    }
 


    public function store(ItemRequest $request)
    {
        $validatedProperties = $request->validated();
		$store_item_result = $this->item_service->storeItem($request);
        return $store_item_result;
    }

   
    public function show($id)
    {
        $get_item_result = $this->item_service->showItem($id);
        return $get_item_result;
    }

    
    public function update(ItemRequest $request)
    {
        $validatedProperties = $request->validated();
        $update_item_result = $this->item_service->updateItem($request);
        return $update_item_result;
    }

    public function destroy($id)
    {
        $destory_item_result = $this->item_service->destroyItem($id);
        return $destory_item_result;
    }

    public function deleteImage($id, $type){
        $delete_item_result = $this->item_service->deleteImage($id, $type);
        return $delete_item_result;
    }
    
}

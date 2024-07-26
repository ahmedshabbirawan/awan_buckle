<?php

namespace App\Http\Controllers\Web\Product;


use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ParentCategory;

use App\Models\Uom;
use App\Http\Requests\ProductFromRequest;
use App\Models\ProductAttribute;
use Illuminate\Support\Str;
use App\Models\StockItem;
use App\Models\Shop;
use Exception;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Image;
use Illuminate\Support\Facades\Storage;

class SimpleProductController extends Controller
{
    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;

    public function __construct()
    {
        $this->resource     = new Product();
        $this->name         = $this->viewData['name']         =   'Product';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/product');
        $this->table        = 'products';
        $this->view         = 'product.simple.';
    }

    public function create()
    {
        $this->viewData['uoms']         = Uom::orderBy('code', 'ASC')->where('status', 1)->get()->pluck('code', 'id');
        return view('product.simple.create', $this->viewData);
    }

    public function createByModal()
    {
        $this->viewData['uoms']         = Uom::orderBy('code', 'ASC')->where('status', 1)->get()->pluck('code', 'id');
        return response()->json([
            'view' => View('product.simple.create_by_modal', $this->viewData)->render(),
            'data' =>   [],
            'status' => true,
            'message' => 'OK'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductFromRequest $request)
    {
        $name           = $request->get('name');
        $description    = $request->get('description');
        $uom_id         = $request->get('uom_id');
        $uom = Uom::find($uom_id);
        $status         = $request->get('status', 1);
        $price          = $request->get('price');
        $imageName      = null;
        if ($request->hasFile('product_image')) {
            $imageObj = $this->uploadProductImage($request);
            if (!($imageObj[0])) {
                return response()->json(['message' => $imageObj[1]], 500);
            } else {
                $imageName = $imageObj[1];
            }
        }
        $array = array(
            'name'           => $name,
            'description'    => $description,
            'uom_id'         => $uom_id,
            'code'           => $uom->code,
            'image'          => $imageName,
            'price'          => $price,
            //   'product_cat_id' => $request->get('product_category_id'),
            'status'         => $status,
        );
        try {
            DB::beginTransaction();
            $attributes = $request->get('attribute_value_ids');
            $product    = Product::create($array);
            $insertArr = [];

            if (!empty($attributes) && (count($attributes) > 0)) {
                foreach ($attributes as $valueID) :
                    ProductAttribute::create(array('product_id' => $product->id, 'value_id' => $valueID));
                endforeach;
            }
            DB::commit();
            if ($request->ajax()) {
                return response(['status' => true, 'data' => ['product' => $product], 'message' => 'Product Add successfully.']);
                $request->session()->flash('success', 'Product Add successfully!');
            } else {
                return redirect(route('product.list'))->with('success', $this->name . ' added successful!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error.Please Contact Admin Support' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, $id)
    {

        $product = Product::find($id);

        $this->viewData['row'] = $product;
        $this->viewData['categories']   = $product->categories_tree();
        return view($this->view . 'detail_new', $this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product, $id)
    {

        $product = Product::find($id);



        $this->viewData['product']      = Product::find($id);
        $this->viewData['categories']   = $product->categories_tree();
        $this->viewData['sub_category']     = ParentCategory::with('subCategories')->orderBy('name', 'DESC')->get();
        // $this->viewData['parentCat']    = ParentCategory::all()->pluck('name','id');
        $this->viewData['attributeIDs']    = ProductAttribute::where('product_id', $id)->pluck('value_id')->toArray();
        $this->viewData['uoms']         = Uom::where('status', 1)->orderBy('code', 'ASC')->get()->pluck('code', 'id');
        return view($this->view . 'edit', $this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductFromRequest $request, Product $product)
    {
        // dd($request->all());
        $productID = $request->get('id');
        $name           = $request->get('name');
        $description    = $request->get('description');
        $uom_id         = $request->get('uom_id');
        $qty            = $request->get('threshold_qty');
        $status         = $request->get('status');

        $imageName = null;
        $price          = $request->get('price');

        if ($request->hasFile('product_image')) {
            $imageObj = $this->uploadProductImage($request);
            if (!($imageObj[0])) {
                return response()->json(['message' => $imageObj[1]], 500);
            } else {
                $imageName = $imageObj[1];
            }
        }

        $array = array(
            'name'          => $name,
            'description'   => $description,
            'uom_id'        => $uom_id,
            'code'        => Uom::find($uom_id)->code,
            'threshold_qty' => $qty,
            'image'          => $imageName,
            'price'          => $price,
            'product_cat_id' => $request->get('product_category_id'),
            'status'        => $status,
        );


        //     print_r($array); exit;

        try {
            DB::beginTransaction();
            $attributes         = $request->get('attribute_value_ids');
            Product::where('id', $productID)->update($array);
            ProductAttribute::where('product_id', $productID)->delete();
            $insertArr          = [];
            if (is_array($attributes)) {
                foreach ($attributes as $valueID) :
                    ProductAttribute::create(array('product_id' => $productID, 'value_id' => $valueID));
                endforeach;
            }
            DB::commit();
            if ($request->ajax()) {
                return response(['status' => true, 'data' => ['product' => $product], 'message' => 'Product Update successfully.']);
            } else {
                return redirect(route('product.list'))->with('success', $this->name . ' added successful!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error.Please Contact Support'], 500);
            // return redirect()->route('category.sub.list')->with('error','Error.Please Contact   Support');
        }
    }


    function uploadProductImage(Request $request)
    {
        $productName = Str::slug($request->get('name'));
        if ($request->hasFile('product_image')) {
            try {
                //Getting file name with extension
                $fileNameWithExt = $request->file('product_image')->getClientOriginalName();
                //Get just file name
                // $filename        = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                //Get just ext
                $extension       = $request->file('product_image')->getClientOriginalExtension();
                //Filename to store
                $fileNameToStore = "product-" . $productName . '-' . time() . '.' . $extension;

                //Upload Image
                $path = $request->file('product_image')->storeAs('public/product_images', $fileNameToStore);

                /*******   CREATE TUMBNAIL   *******/

                //  echo Storage::get($path); exit;

                $path = Storage::path('public/product_images/' . $fileNameToStore);

                // $publicPath = "public\product_images\"".$fileNameToStore;

                $imgFile = Image::fromFile($path);
                $imgFile->resize(150, 150, Image::EXACT);
                $imgFile->cropAuto()->save($path);
                // $imgFile->resize(150, 150, function ($constraint) {
                //     // $constraint->aspectRatio();
                //     $constraint->cropAuto();
                // })->save($path);
                // $destinationPath = public_path('/uploads');
                //$image->move($destinationPath, $input['file']);
                /**************/

                return [true, $fileNameToStore];
            } catch (Exception $e) {
                return [false, $e->getMessage()];
            }
        }
        return [false, 'Image not found!'];
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Product $product)
    {
        $res = StockItem::where('product_id', $id)->get();
        if (count($res) > 0) {
            return redirect(route('product.list'))->with('error', 'This product have stock item. First delete them');
        } else {
            Product::find($id)->delete();
            return redirect(route('product.list'))->with('success', 'Product updated successful!');
        }
    }


    function importProductsModal(Request $request)
    {
        $data['shops']          = Shop::all()->pluck('name', 'id');
        return response()->json([
            'view' => View('product.simple.import_products_modal', $data)->render(),
            'data' =>   [],
            'status' => true,
            'message' => 'OK'
        ]);
    }





    function updateProductName()
    {
        foreach (Product::all() as $pro) {
            if (strtolower(substr($pro->name, 0, 2))  == 'aw') {
                $pro->name =  substr($pro->name, 2);
                $pro->save();
            }
        }
        echo 'The End';
    }
}

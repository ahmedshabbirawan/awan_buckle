<?php

namespace App\Http\Controllers\Sale\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleOrder;
use App\Models\Shop;
use Carbon\Carbon;

class SaleReportController extends Controller{

    function all(Request $request){

        $data['products'] = Product::all();
        $data['shops'] = Shop::all()->pluck('name','id');
      //  $data['shops'] = Shop::all()->pluck('name','id');
        if($request->ajax()){
            $groupBy    = $request->get('group_by');
            $shopIds    = $request->get('shop_ids');
            $dateRange  = $request->get('date_range');
            $startDate  = null;
            $endDate    = null;

            if ($dateRange && strpos($dateRange, '-') !== false) {
                try {
                    $dateString = explode('-', $dateRange);
                    $startDate  = Carbon::createFromFormat('d/m/Y H:i:s', trim($dateString[0]) . ' 00:00:01');
                    $endDate    = Carbon::createFromFormat('d/m/Y H:i:s', trim($dateString[1]) . ' 23:59:59');
                } catch (\Exception $e) {
                    $startDate = null;
                    $endDate   = null;
                }
            }

            $shopsName  = ($shopIds) ? Shop::whereIn('id', $shopIds)->pluck('name')->implode(', ') : 'All';

            $itemsQuery = SaleOrder::with('shop')->where(function($query) use ($shopIds, $startDate, $endDate) {
                if ($shopIds) {
                    $query->whereIn('shop_id', $shopIds);
                }
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            });

            $totalPriceSum = (float)$itemsQuery->sum('total_price');
            $itemCount = (int)$itemsQuery->sum('item_count');

            // Calculate returns during the matching shop/date scope
            $returnsQuery = \App\Models\SaleReturnItem::where(function($query) use ($shopIds, $startDate, $endDate) {
                if ($shopIds) {
                    $query->whereIn('sale_return_items.shop_id', $shopIds);
                }
                if ($startDate && $endDate) {
                    $query->whereBetween('sale_return_items.created_at', [$startDate, $endDate]);
                }
            });

            $returnCount = (int)$returnsQuery->sum('qty');
            $returnAmount = (float)$returnsQuery->join('products', 'products.id', '=', 'sale_return_items.product_id')
                ->selectRaw('SUM(sale_return_items.qty * products.price) as total')
                ->value('total') ?: 0;

            $netSales = max(0, $totalPriceSum - $returnAmount);
            $netItemCount = max(0, $itemCount - $returnCount);

            $items = $itemsQuery->get();

            return Datatables::of($items)
            ->addColumn('title', function ($row) {
                return '<a href="'.route('sale.order.detail',$row->id).'" target="_blank" >'.$row->sale_key.'</a>';
            })
            ->addColumn('item_count', function ($row) {
                return $row->item_count;
            })->addColumn('order_time', function ($row) {
                return $row->created_at->format('h:i / d-m-Y');
            })->addColumn('shop_name', function ($row) {
                return $row->shop ? $row->shop->name : '--';
            })->addColumn('customer_name', function ($row) {
                if($row->customer_id == 0){
                    return 'Walking Customer';
                }else{
                    $cust = Customer::find($row->customer_id);
                    return $cust ? $cust->name : 'Walking Customer';
                }
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group">';
                $action .= '<a href="'.route('sale.order.detail',$row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                $action .= '<a href="'.route('sale.order.sale_return_view',$row->id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Sale Return" ><i class="ace-icon fa fa-undo bigger-120"></i></a>';
                $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->sale_key.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                $action .= '</div>';
                return $action;
            })->rawColumns(['title','product_attribute','status_label','action'])
            ->with('totalPrice', round($totalPriceSum))
            ->with('totalReturns', round($returnAmount))
            ->with('netSales', round($netSales))
            ->with('shopsName', $shopsName)
            ->with('itemCount', $itemCount)
            ->with('returnItemCount', $returnCount)
            ->with('netItemCount', $netItemCount)
            ->make(true);
        }else{
            return view('sale.report.sale_report_all',$data);
        }
    }

      


    }

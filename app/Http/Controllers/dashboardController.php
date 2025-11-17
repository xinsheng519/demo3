<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class dashboardController extends Controller
{
    public function index (Request $request){
        return view('welcome');
    }

    public function summary (Request $request){
        try {
            $totalNumber = DB::table('order_items')
            ->count();

            $totalRevenue = DB::table('orders')
            ->sum('total_amount');

            $topProducts = DB::table('order_items as oi')
            ->select(
                'oi.product_id', 
                DB::raw('SUM(oi.quantity) as total_sold'),
                'p.name'
            )
            ->leftjoin('products as p', 'p.id', '=', 'oi.product_id')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();

            $avgAmount = DB::table('orders')->avg('total_amount');

            return response()->json([
                'status' => 'success',
                'totalNumber' => $totalNumber,
                'totalRevenue' => $totalRevenue,
                'topProducts' => $topProducts,
                'avgAmount' => $avgAmount,
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function table(Request $request){
        try{
            $datatable = DB::table('order_items as oi')
            ->select(
                'oi.id',
                'o.order_date',
                'c.name as customer',
                'c.state',
                'cate.name as category',
                'p.name as product',
                'oi.quantity',
                'oi.unit_price',
                'oi.unit_price as sub_total'
            )
            ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
            ->leftJoin('orders as o', 'o.id', '=', 'oi.order_id')
            ->leftJoin('customers as c', 'c.id', '=', 'o.customer_id')
            ->leftJoin('categories as cate', 'cate.id', '=', 'p.category_id')
            ->get();
            
            return Datatables::of($datatable)
            ->addIndexColumn()
            // ->addColumn('action', function ($row) {

            //    $deleteButton = '<a href="javascript:void(0);" data-id="' . $row->list_no . '" class="deleteItem">
            //    <button class="btn btn-danger btn-sm" ">
            //        Delete
            //    </button>
            //    </a>';
        
            //     return $deleteButton;
            // })
            // ->editColumn('created_by', function ($row) {
            //     return $row->created_by_name ?? 'System';
            // })
            // ->rawColumns(['action','status'])           
            ->make(true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function exportCsv()
    {
        $totalNumber = DB::table('order_items')->count();
        $totalRevenue = DB::table('orders')->sum('total_amount');
        $avgAmount = DB::table('orders')->avg('total_amount');

        $topProducts = DB::table('order_items as oi')
        ->select('oi.product_id', DB::raw('SUM(oi.quantity) as total_sold'), 'p.name')
        ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
        ->groupBy('oi.product_id')
        ->orderByDesc('total_sold')
        ->limit(3)
        ->pluck('name')
        ->toArray();

        $data = DB::table('order_items as oi')
        ->select(
            'oi.id',
            'o.order_date',
            'c.name as customer',
            'c.state',
            'cate.name as category',
            'p.name as product',
            'oi.quantity',
            'oi.unit_price',
            'oi.unit_price as sub_total'
        )
        ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
        ->leftJoin('orders as o', 'o.id', '=', 'oi.order_id')
        ->leftJoin('customers as c', 'c.id', '=', 'o.customer_id')
        ->leftJoin('categories as cate', 'cate.id', '=', 'p.category_id')
        ->get();

        return response()
        ->view('export', [
            'totalNumber' => $totalNumber,
            'totalRevenue' => $totalRevenue,
            'topProducts' => $topProducts,
            'avgAmount' => $avgAmount,
            'data' => $data,
        ])
        ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="orders.xls"')
        ->header('Cache-Control', 'max-age=0');
    }
    public function exportXlsx()
    {

        $totalNumber = DB::table('order_items')->count();
        $totalRevenue = DB::table('orders')->sum('total_amount');
        $avgAmount = DB::table('orders')->avg('total_amount');

        $topProducts = DB::table('order_items as oi')
        ->select('oi.product_id', DB::raw('SUM(oi.quantity) as total_sold'), 'p.name')
        ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
        ->groupBy('oi.product_id')
        ->orderByDesc('total_sold')
        ->limit(3)
        ->pluck('name')
        ->toArray();

        $data = DB::table('order_items as oi')
        ->select(
            'oi.id',
            'o.order_date',
            'o.id as order_id',
            'c.name as customer',
            'c.state',
            'cate.name as category',
            'p.name as product',
            'oi.quantity',
            'oi.unit_price',
            'oi.unit_price as sub_total'
        )
        ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
        ->leftJoin('orders as o', 'o.id', '=', 'oi.order_id')
        ->leftJoin('customers as c', 'c.id', '=', 'o.customer_id')
        ->leftJoin('categories as cate', 'cate.id', '=', 'p.category_id')
        ->orderBy('o.id')
        ->get();

        \Log::debug($data);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Total Orders');
        $sheet->setCellValue('B1', $totalNumber);

        $sheet->setCellValue('A2', 'Total Revenue');
        $sheet->setCellValue('B2', $totalRevenue);

        $sheet->setCellValue('A3', 'Average Order Value');
        $sheet->setCellValue('B3', $avgAmount);

        $sheet->setCellValue('A4', 'Top 3 Products');
        $sheet->setCellValue('B4', implode(', ', $topProducts));

        $sheet->fromArray(['Order Date','Customer','State','Category','Product','Quantity','Unit Price','Subtotal'], null, 'A6');

        $row = 7;
        $currentOrder = null;
        $orderTotal = 0;

        foreach ($data as $item) {

            if ($currentOrder !== $item->order_id) {
        
                if ($currentOrder !== null) {
                    $sheet->setCellValue("A{$row}", "Order No: {$currentOrder} Total");
                    $sheet->setCellValue("G$row", $orderTotal);
                    $sheet->setCellValue("H$row", $orderTotal);
                    $row++;
                }
        
                $currentOrder = $item->order_id;
                $orderTotal = 0;
        
                $sheet->fromArray([
                    $item->order_date,   // A
                    $item->customer,     // B
                    $item->state,        // C
                    $item->category,     // D
                    $item->product,      // E
                    $item->quantity,     // F
                    $item->unit_price,   // G
                    $item->sub_total     // H
                ], null, "A{$row}");
        
                $orderTotal += $item->sub_total;
                $row++;
        
            } else {
                $sheet->fromArray([
                    '',                  // A  Order Date
                    '',                  // B  Customer
                    '',                  // C  State
                    $item->category,     // D
                    $item->product,      // E
                    $item->quantity,     // F
                    $item->unit_price,   // G
                    $item->sub_total     // H
                ], null, "A{$row}");
        
                $orderTotal += $item->sub_total;
                $row++;
            }
        }

        if ($currentOrder !== null) {
            $sheet->setCellValue("A$row", "Order No: $currentOrder Total");
            $sheet->setCellValue("G$row", $orderTotal);
            $sheet->setCellValue("H$row", $orderTotal);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer){
            $writer->save('php://output');
        }, 'orders.xlsx');
    }
}




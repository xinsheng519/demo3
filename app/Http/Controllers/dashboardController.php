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

    public function exportXlsx()
    {
        try {
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
        
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells("A1:B1");
            $sheet->getStyle("A1:B1")->getFont()->setBold(true);
            $sheet->mergeCells("A7:H7");
            $sheet->getStyle("A7:H7")->getFont()->setBold(true);
            $sheet->setCellValue("A1", "Summary Section");
    
            $sheet->setCellValue('A2', 'Total Orders');
            $sheet->setCellValue('B3', $totalNumber);
    
            $sheet->setCellValue('A3', 'Total Revenue');
            $sheet->setCellValue('B3', $totalRevenue);
    
            $sheet->setCellValue('A4', 'Average Order Value');
            $sheet->setCellValue('B4', $avgAmount);
    
            $sheet->setCellValue('A5', 'Top 3 Products');
            $sheet->setCellValue('B5', implode(', ', $topProducts));
    
            $sheet->setCellValue('A7', 'Detailed Table');

            $sheet->fromArray(['Order Date','Customer','State','Category','Product','Quantity','Unit Price','Subtotal'], null, 'A8');
    
            $row = 9;
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
                        '',                  
                        '',                  
                        '',                  
                        $item->category,     
                        $item->product,      
                        $item->quantity,     
                        $item->unit_price,   
                        $item->sub_total     
                    ], null, "A{$row}");
            
                    $orderTotal += $item->sub_total;
                    $row++;
                }
            }

            if ($currentOrder !== null) {
                $sheet->setCellValue("A{$row}", "Order No: {$currentOrder} Total");
                $sheet->setCellValue("G$row", $orderTotal);
                $sheet->setCellValue("H$row", $orderTotal);
                $row++;
            }

            $lastRow = $row - 1; 

            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $sheet->getStyle("A7:H{$lastRow}")->applyFromArray($styleArray);
            $sheet->getStyle("A1:B5")->applyFromArray($styleArray);

            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
    
            $writer = new Xlsx($spreadsheet);
    
            return response()->streamDownload(function() use ($writer){
                $writer->save('php://output');
            }, 'orders.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}




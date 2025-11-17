<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid black; padding: 5px; text-align: left; }
th { font-weight: bold; background-color: #f0f0f0; }
</style>
</head>
<body>
<table>
<tr>
<td colspan="2">Total Orders</td>
<td>{{ $totalNumber }}</td>
</tr>
<tr>
<td colspan="2">Total Revenue</td>
<td>{{ number_format($totalRevenue,2) }}</td>
</tr>
<tr>
<td colspan="2">Average Order Value</td>
<td>{{ number_format($avgAmount,2) }}</td>
</tr>
<tr>
<td colspan="2">Top 3 Best Selling Products</td>
<td>{{ implode(', ', $topProducts) }}</td>
</tr>

<tr>
<th>Order Date</th>
<th>Customer</th>
<th>State</th>
<th>Category</th>
<th>Product</th>
<th>Quantity</th>
<th>Unit Price</th>
<th>Subtotal</th>
</tr>

@foreach($data as $item)
<tr>
<td>{{ $item->order_date }}</td>
<td>{{ $item->customer }}</td>
<td>{{ $item->state }}</td>
<td>{{ $item->category }}</td>
<td>{{ $item->product }}</td>
<td>{{ $item->quantity }}</td>
<td>{{ number_format($item->unit_price,2) }}</td>
<td>{{ number_format($item->sub_total,2) }}</td>
</tr>
@endforeach
</table>
</body>
</html>
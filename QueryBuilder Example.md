```php

<?php

$queryBuilder = new QueryBuilder();

// Complex query to get top selling products with their categories and average rating
$topSellingProducts = $queryBuilder
    ->select([
        'p.id',
        'p.name',
        'p.price',
        'c.name AS category_name',
        'COUNT(o.id) AS total_orders',
        'SUM(o.quantity) AS total_quantity_sold',
        'AVG(r.rating) AS average_rating'
    ])
    ->selectRaw('(p.price * SUM(o.quantity)) AS total_revenue')
    ->from('products AS p')
    ->join('categories AS c', 'p.category_id', '=', 'c.id')
    ->leftJoin('order_items AS o', 'p.id', '=', 'o.product_id')
    ->leftJoin('reviews AS r', 'p.id', '=', 'r.product_id')
    ->where('p.is_active', '=', true)
    ->whereNotNull('p.price')
    ->whereBetween('p.created_at', [date('Y-m-d', strtotime('-1 year')), date('Y-m-d')])
    ->groupBy('p.id', 'p.name', 'p.price', 'c.name')
    ->having('total_quantity_sold', '>', 10)
    ->orHaving('average_rating', '>=', 4)
    ->orderBy('total_revenue', 'desc')
    ->limit(10)
    ->get();

echo "Top Selling Products:\n";
print_r($topSellingProducts);

// Query to get customers who haven't made a purchase in the last 6 months
$inactiveCustomers = $queryBuilder
    ->select(['c.id', 'c.name', 'c.email', 'MAX(o.created_at) AS last_order_date'])
    ->from('customers AS c')
    ->leftJoin('orders AS o', 'c.id', '=', 'o.customer_id')
    ->whereNotIn('c.id', function($subQuery) {
        $subQuery->select('customer_id')
                 ->from('orders')
                 ->where('created_at', '>', date('Y-m-d', strtotime('-6 months')));
    })
    ->groupBy('c.id', 'c.name', 'c.email')
    ->orderBy('last_order_date', 'asc')
    ->paginate(20);

echo "\nInactive Customers:\n";
print_r($inactiveCustomers);

// Query to get product inventory status
$inventoryStatus = $queryBuilder
    ->select(['p.id', 'p.name', 'p.stock_quantity'])
    ->selectRaw('CASE WHEN p.stock_quantity = 0 THEN "Out of Stock" 
                      WHEN p.stock_quantity < 10 THEN "Low Stock" 
                      ELSE "In Stock" END AS stock_status')
    ->from('products AS p')
    ->where(function($query) {
        $query->where('p.stock_quantity', '=', 0)
              ->orWhere('p.stock_quantity', '<', 10);
    })
    ->orderBy('p.stock_quantity', 'asc')
    ->get();

echo "\nInventory Status:\n";
print_r($inventoryStatus);

// Check if any product is out of stock
$hasOutOfStock = $queryBuilder
    ->from('products')
    ->where('stock_quantity', '=', 0)
    ->exists();

echo "\nAre there any out of stock products? " . ($hasOutOfStock ? "Yes" : "No") . "\n";

// Get the total number of customers
$totalCustomers = $queryBuilder
    ->from('customers')
    ->count();

echo "\nTotal number of customers: " . $totalCustomers . "\n";

// Get the first order of a specific customer
$firstOrder = $queryBuilder
    ->select(['id', 'created_at', 'total_amount'])
    ->from('orders')
    ->where('customer_id', '=', 1)
    ->orderBy('created_at', 'asc')
    ->first();

echo "\nFirst order of customer 1:\n";
print_r($firstOrder);

// Complex query with subqueries and multiple joins
$complexQuery = $queryBuilder
    ->select([
        'c.id AS category_id',
        'c.name AS category_name',
        'p.id AS product_id',
        'p.name AS product_name'
    ])
    ->selectRaw('(SELECT AVG(price) FROM products WHERE category_id = c.id) AS avg_category_price')
    ->selectRaw('(SELECT COUNT(*) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.product_id = p.id AND o.created_at >= ?) AS orders_last_30_days', [date('Y-m-d', strtotime('-30 days'))])
    ->from('categories AS c')
    ->join('products AS p', 'c.id', '=', 'p.category_id')
    ->leftJoin('product_tags AS pt', 'p.id', '=', 'pt.product_id')
    ->leftJoin('tags AS t', 'pt.tag_id', '=', 't.id')
    ->where('p.is_active', '=', true)
    ->whereIn('t.name', ['bestseller', 'featured'])
    ->groupBy('c.id', 'c.name', 'p.id', 'p.name')
    ->having('orders_last_30_days', '>', 0)
    ->orderBy('orders_last_30_days', 'desc')
    ->limit(5)
    ->get();

echo "\nComplex Query Result:\n";
print_r($complexQuery);
```
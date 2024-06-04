<?php
interface IOrderService{
    function createOrders($user, $products);
}

class OrderService implements IOrderService{
    private $database;
    private $orders;

    public function __construct($database)
    {
        $this->database = $database;  
        $this->orders = [];      
    }

    public function createOrders($user, $products){
        foreach($products as $product){
            $queryCreateOrder = "INSERT INTO orders (id_product, id_user, registration_date)
            VALUES ('{$product->id}', '{$user->id}', NOW())";
            mysqli_query($this->database, $queryCreateOrder);
            $queryOrderId = "SELECT id FROM orders 
            WHERE id_product={$product->id} and id_user={$user->id}";
            $orderId = mysqli_fetch_assoc(mysqli_query($this->database, $queryOrderId))['id'];
            $this->orders[] = $orderId;
        }
        return $this->orders;
    }
}

interface ILogisticService{
    function registerDelivety($orders);
}

class LogisticService implements ILogisticService{
    private $logisticURL;

    public function __construct($logisticURL)
    {
        $this->logisticURL = $logisticURL;        
    }

    public function registerDelivety($orders){
        for($i = 0; $i < count($orders); $i++){
            $id_order = $orders[$i];
            $response = file_get_contents("{$this->logisticURL}?id_order={$id_order}");
            if($response != "success"){
                return throw new Exception("Error registering delivery");
            }
        }        
    }
}
?>
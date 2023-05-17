<?php
session_start();

use Phalcon\Mvc\Controller;

// login controller
class OrderController extends Controller
{
    public function indexAction()
    {
        // default login view
        $collection = $this->mongo->products->find();
        $this->view->data = $collection;
    }
    public function addAction()
    {
        $date = date('Y-m-d');
        $pid = $_POST['products'];
        $collection = $this->mongo->products;
        $item = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($pid)]);
        $pname = $item['name'];
        if ($_POST['quantity'] <= 0) {
            echo "Quantity is less than equal to 0 ";
            die;
        }
        if ($item['stock'] <= $_POST['quantity']) {
            echo "Not available in stocks ";
            die;
        } else {
            $stock = $item['stock'] - $_POST['quantity'];
            $input = [
                "product_name" => $pname,
                "product_id" => $pid,
                "variaion" => $_POST['variaion'],
                "customer_name" => $_POST['customer_name'],
                "quantity" => $_POST['quantity'],
                "date" => $date,
                "status" => "Paid",
            ];
            $collectionagain = $this->mongo->orders;
            $collectionagain->insertOne($input);
            $updateResult = $collection->updateOne(
                ['_id'  =>  new MongoDB\BSON\ObjectId($pid)],
                ['$set' => [
                    "stock" => $stock,
                ]]
            );
            $this->response->redirect('/order/view');
        }
    }
    public function viewAction()
    {
        $collection = $this->mongo->orders->find();
        $this->view->data = $collection;
    }
    public function searchAction()
    {
        $status = $_POST['status'];
        $date = $_POST['date'];
        $start = date('Y-m-d');
        if ($date == "today") {
            $end = date('Y-m-d');
        }
        if ($date == "thisweek") {
            $end = date('Y-m-d', strtotime('+7 days'));
        }
        if ($date == "thisweek") {
            $end = date('Y-m-d', strtotime('+7 days'));
        }
        if ($date == "thisMonth") {
            $end = date('Y-m-d', strtotime('+30 days'));
        }
        if ($date == "custom") {
            $start = $_POST['startd'];
            $end = $_POST['endd'];
        }
        $collectionagain = $this->mongo->orders->find([
            'status' => $status, '$and' => [['date' => ['$gte' => $end]], ['date' => ['$lte' => $start]]]
        ]);
        $this->view->data = $collectionagain;
    }
    public function updateAction()
    {
        $id = $_GET['id'];
        $collection = $this->mongo->orders;
        $item = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        $this->view->data = $item;
    }
    public function editAction()
    {
        $id = $_GET['id'];
        $collection = $this->mongo->orders;
        $updateResult = $collection->updateOne(
            ['_id'  =>  new MongoDB\BSON\ObjectId($id)],
            ['$set' => [
                "product_name" => $_POST['product_name'],
                "customer_name" => $_POST['customer_name'],
                "quantity" => $_POST['quantity'],
                "stock" => $_POST['pstock'],
                "status" => $_POST['status']
            ]]
        );
        $this->response->redirect('/order/view');
    }
    public function generateAction()
    {
        $id = $_POST['data'];
        $collection = $this->mongo->products;
        $item = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        $_SESSION['details'] = $item['attributes'];
        $_SESSION['item'] = $item['name'];
        $_SESSION['item_id']=$item['_id'];

    }
    public function deleteAction()
    {
        $id = $_GET['id'];
        $collection = $this->mongo->orders;
        $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        $this->response->redirect('/order/view');
    }
}

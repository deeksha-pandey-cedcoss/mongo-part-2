<?php

use Phalcon\Mvc\Controller;

class ProductController extends Controller
{
    public function indexAction()
    {
        // default login view
    }
    public function addAction()
    {
        $c = count($this->request->getPost('titlekey'));
        $meta = [];
        for ($i = 0; $i < $c; $i++) {
            $key = $_POST['titlekey'][$i];
            $value = $_POST['titlevalue'][$i];
            $meta[$key] = $value;
        }
        $m = count($this->request->getPost('varkey'));
        $variation = [];
        for ($i = 0; $i < $m; $i++) {
            $key = $_POST['varkey'][$i];
            $value = $_POST['varvalue'][$i];
            $variation[$key] = $value;
            $varprice = $_POST['varprice'][$i];
        }
        $input = [
            "name" => $_POST['name'],
            "category" => $_POST['category'],
            "price" => $_POST['price'],
            "stock" => $_POST['stock'],
            "meta" => $meta,
            "attributes" => $variation
        ];
        $collection = $this->mongo->products;
        $collection->insertOne($input);

        $this->response->redirect('/product/view');
    }
    public function viewAction()
    {
        $collection = $this->mongo->products->find();
        $this->view->data = $collection;
    }
    public function deleteAction()
    {
        $id = $_GET['id'];
        $collection = $this->mongo->products;
        $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        $this->response->redirect('/product/view');
    }
    public function updateAction()
    {
        $id = $_GET['id'];

        $collection = $this->mongo->products;
        $item = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        $this->view->data = $item;
    }
    public function editAction()
    {
        $id = $_GET['id'];
        $collection = $this->mongo->products;
        $updateResult = $collection->updateOne(
            ['_id'  =>  new MongoDB\BSON\ObjectId($id)],
            ['$set' => [
                "name" => $_POST['pname'],
                "category" => $_POST['pcategory'],
                "pprice" => $_POST['pprice'],
                "stock" => $_POST['pstock'],
                "attriprice" => $_POST['price']
            ]]
        );
        $this->response->redirect('/product/view');
    }
    public function searchAction()
    {
        print_r($_POST['val']);
        $collection = $this->mongo->products;
        $fruitQuery = array('name' => $_POST['val']);
        $cursor = $collection->find($fruitQuery);
        $this->view->data = $cursor;
    }
    public function detailsAction()
    {
        $id = $_GET['id'];
        $collection = $this->mongo->products;
        $item = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        $this->view->data = $item;
    }
}

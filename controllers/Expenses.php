<?php
require_once 'models/ExpensesModel.php';
require_once 'models/CategoriesModel.php';
require_once 'models/JoinExpensesCategoriesModel.php';
class Expenses extends SessionController {
    private $user;

    public function __construct(){
        parent::__construct();

        $this->user = $this->getUserSessionData();
        error_log("Expenses::constructor() ");
    }

    function render(){
        error_log("Expenses::RENDER() ");

        $this->view->render('expenses/index', [
            'user' => $this->user,
            'dates' => $this->getDateList(),
            'categories' => $this->getCategoryList()
        ]);
    }

    function newExpense(){
        error_log('Expenses::newExpense()');
        if(!$this->existPOST(['title', 'amount', 'category', 'date'])){
            $this->redirect('dashboard', ['error' => ErrorMessages::ERROR_EXPENSES_NEWEXPENSE_EMPTY]);
            return;
        }

        if($this->user == NULL){
            $this->redirect('dashboard', ['error' => ErrorMessages::ERROR_EXPENSES_NEWEXPENSE]);
            return;
        }

        $expense = new ExpensesModel();
        $expense->setTitle($this->getPost('title'));
        $expense->setAmount((float)$this->getPost('amount'));
        $expense->setCategoryId($this->getPost('category'));
        $expense->setDate($this->getPost('date'));
        $expense->setUserId($this->user->getId());

        $expense->save();
        $this->redirect('dashboard', ['success' => SuccessMessages::SUCCESS_EXPENSES_NEWEXPENSE]);
    }

    function create(){
        $categories = new CategoriesModel();
        $this->view->render('expenses/create', [
            "categories" => $categories->getAll(),
            "user" => $this->user
        ]);
    }

    function getCategoryIds(){
        $joinExpensesCategoriesModel = new JoinExpensesCategoriesModel();
        $categories = $joinExpensesCategoriesModel->getAll($this->user->getId());
        $res = [];
        foreach ($categories as $cat) {
            array_push($res, $cat->getCategoryId());
        }
        $res = array_values(array_unique($res));
        return $res;
    }

    private function getDateList(){
        $months = [];
        $res = [];
        $joinExpensesCategoriesModel = new JoinExpensesCategoriesModel();
        $expenses = $joinExpensesCategoriesModel->getAll($this->user->getId());

        foreach ($expenses as $expense) {
            array_push($months, substr($expense->getDate(),0, 7 ));
        }
        $months = array_values(array_unique($months));

        foreach ($months as $month) {
            array_push($res, $month);
        }

        return $res;
    }

    private function getCategoryList(){
        $res = [];
        $joinExpensesCategoriesModel = new JoinExpensesCategoriesModel();
        $expenses = $joinExpensesCategoriesModel->getAll($this->user->getId());

        foreach ($expenses as $expense) {
            array_push($res, $expense->getNameCategory());
        }
        $res = array_values(array_unique($res));
        return $res;
    }

    private function getCategoryColorList(){
        $res = [];
        $joinExpensesCategoriesModel = new JoinExpensesCategoriesModel();
        $expenses = $joinExpensesCategoriesModel->getAll($this->user->getId());

        foreach ($expenses as $expense) {
            array_push($res, $expense->getColor());
        }
        $res = array_unique($res);
        $res = array_values(array_unique($res));
        return $res;
    }

    function getHistoryJSON(){
        header('Content-Type: application/json');
        $res = [];
        $joinExpensesCategories = new JoinExpensesCategoriesModel();
        $expenses = $joinExpensesCategories->getAll($this->user->getId());

        foreach ($expenses as $expense) {
            array_push($res, $expense->toArray());
        }
        echo json_encode($res);
    }

    function getExpensesJSON(){
        header('Content-Type: application/json');
        $res = [];
        $categoryIds     = $this->getCategoryIds();
        $categoryNames  = $this->getCategoryList();
        $categoryColors = $this->getCategoryColorList();

        array_unshift($categoryNames, 'mes');
        array_unshift($categoryColors, 'categorias');

        $months = $this->getDateList();

        for($i = 0, $iMax = count($months); $i < $iMax; $i++){
            $item = array($months[$i]);
            for($j = 0, $jMax = count($categoryIds); $j < $jMax; $j++){
                $total = $this->getTotalByMonthAndCategory( $months[$i], $categoryIds[$j]);
                array_push( $item, $total );
            }
            array_push($res, $item);
        }
        array_unshift($res, $categoryNames);
        array_unshift($res, $categoryColors);
        echo json_encode($res);
    }

    function getTotalByMonthAndCategory($date, $categoryid){
        $iduser = $this->user->getId();
        $joinExpensesCategoriesModel = new JoinExpensesCategoriesModel();
        $total = $joinExpensesCategoriesModel->getTotalByMonthAndCategory($date, $categoryid, $iduser);
        if($total == NULL) $total = 0;
        return $total;
    }

    function delete($params){
        error_log("Expenses::delete()");
        if($params === NULL) $this->redirect('expenses', ['error' => ErrorMessages::ERROR_ADMIN_NEWCATEGORY_EXISTS]);
        $id = $params[0];
        error_log("Expenses::delete() id = " . $id);
        $res = $this->model->delete($id);

        if($res){
            $this->redirect('expenses', ['success' => SuccessMessages::SUCCESS_EXPENSES_DELETE]);
        }else{
            $this->redirect('expenses', ['error' => ErrorMessages::ERROR_ADMIN_NEWCATEGORY_EXISTS]);
        }
    }
}
<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Expenses;
use App\ExpensesUsersList;

class ExpenseService {

	protected $expenses;
 	protected $expensesListItem;

    public function __construct()
    {
        try {
             
             $this->expenses = new Expenses();
             $this->expensesListItem = new ExpensesUsersList();
        } catch (Exception $e) {
            return response()->json(['status' => 'Authorization Token not found']);
        }
       
    }

	public function addExpenseItemList($input,$user) {

		//$this->expensesListItem->expense_user_list_names = $user->username;
		//$this->expensesListItem->save();

		
		//return $product;
		$this->expenses->user_id = $user->id;
		$this->expenses->expname = $input->expname;
		$this->expenses->amount = $input->amount;
		$this->expenses->operation = 'ADD';
		$this->expenses->comment = $input->expname;
		$incomeExpenesUserArr = array();
		if($input->type){
			$this->expensesListItem->expense_user_list_names = $user->username;
			$this->expensesListItem->save();
			if($this->expensesListItem->expensesuser()->save($this->expenses)){
				return $this->expenses;
			}

		}else{
			//$haveExpenseList = $user->expensesuser();
			$expenseListId = $input->expListId;
			if($expenseListId){
				$haveExpenseList = $this->expensesListItem->find($expenseListId);
			}else{
				$haveExpenseList = $this->expensesListItem->find('12');	
			}
			
			
			if($haveExpenseList->expensesuser()->save($this->expenses)){
				$expenseListName = ExpensesUsersList::select('id','expense_user_list_names')->where('id', $expenseListId)->get()->toArray();
				$incomeExpenesUserArr['expenses'] = $this->expenses;
				$incomeExpenesUserArr['exp_list_name'] = $expenseListName;
				return $this->getExpenesByListId($expenseListId,$user->id);
				//return $incomeExpenesUserArr;	
			}

		}

		

		
	}

	public function getExpenesByListId($listId,$uid){

		  $expensesDetails = Expenses::where([['expenses_users_list_id', '=', $listId], ['user_id', '=', $uid]])->get()->toArray();
		  $expenseListName = ExpensesUsersList::select('id','expense_user_list_names')->where('id', $listId)->get()->toArray();
		  $expensesUserAmount = Expenses::select('amount')->where([['expenses_users_list_id', '=', $listId], ['user_id', '=', $uid]])->get()->toArray();
		  $incomeAmountArr = array();
		  $expenditureAmountArr = array();
		  $incomeExpenesUserArr = array();
		  foreach ($expensesUserAmount as $key => $value) {
		  		if($value['amount']>0){
		  			$incomeAmountArr[$key] = $value['amount'];
		  		}else{
		  			$expenditureAmountArr[$key] = $value['amount'];
		  		}
		  }

		  // array_push($expensesDetails, array_sum($incomeAmountArr));
		  // array_push($expensesDetails, $expenditureAmountArr);
		  $incomeExpenesUserArr['expenses'] = $expensesDetails;
		  $incomeExpenesUserArr['income'] = array_sum($incomeAmountArr)+array_sum($expenditureAmountArr);
		  $incomeExpenesUserArr['expense'] =  array_sum($expenditureAmountArr);
		  $incomeExpenesUserArr['exp_list_name'] = $expenseListName;
		  $incomeExpenesUserArr['exp_list_id'] = $listId;
		  return $incomeExpenesUserArr;
	}
}

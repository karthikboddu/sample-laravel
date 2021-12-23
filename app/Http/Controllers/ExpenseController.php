<?php

namespace App\Http\Controllers;
use JWTAuth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\ExpenseService;
class ExpenseController extends Controller
{
    protected $user;
 	protected $expenseService;

    public function __construct()
    {
        try {
             $this->user = JWTAuth::parseToken()->authenticate();
             $this->expenseService = new ExpenseService();
        } catch (Exception $e) {
            return response()->json(['status' => 'Authorization Token not found']);
        }
       
    }


    public function addExpenseItemList(Request $request){

    	try{
	    	// $this->validate($request, [
	     //        'expname' => 'required',
	     //        'amount' => 'required|integer'
	     //    ]);	   		
    		$exp =  $this->expenseService->addExpenseItemList($request,$this->user);
	        return response()->json([
	                'success' => true,
	                'expensesuserList' => $exp,
	                'exp_list_id'=>$request->expListId,
	                'expense'=>'',
	                'income'=>''
	            ]);
    	} catch (Exception $e){
    		return response()->json(['status' => 'Expense could not add']);
    	}

    }



    public function getExpensesListByUser(Request $request){

    	try{
    		$userExpenses = $this->user->expensesuser;
    		$userExpensesListId = array();
    		$expensesUserList= array();
    		foreach ($userExpenses as $key => $value) {
    			$userExpensesListId[$key] = $value['expenses_users_list_id'];
    		}

    		$userExpensesListId = array_values(array_unique($userExpensesListId));
    		for ($i=0; $i < sizeof($userExpensesListId) ; $i++) { 
    			$expensesUserList[$i] = $this->expenseService->getExpenesByListId($userExpensesListId[$i],$this->user->id);
    			}	
	        return response()->json([
	                'success' => true,
	                'expensesuserList' => $expensesUserList
	            ]);
    	} catch (Exception $e){
    		return response()->json(['status' => 'Expense could not add']);
    	}



    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense;
use App\Family;
use \Exception;

class ExpenseController extends Controller {

    /**
     * Function that will add the expense
     * @param Request $request [description]
     */
    public function addExpense(Request $request) {
        $arrayresponse = array();
        
        try {
            $content = json_decode($request->getContent(), true);
            $expenseAmount          = trim($content['expenseAmount']);
            $expenseType            = trim($content['expenseType']);
            $expenseText            = trim($content['expenseText']);
            $expenseDescription     = trim($content['expenseDescription']);
            $dateTime               = trim($content['dateTime']);
            $familySlack            = trim($content['familySlack']);
            $user_id                = trim($request->get('user_id'));
            $user_displayName       = trim($request->get('displayName'));

            $family = Family::where('familySlack', '=', $familySlack)->first();
            if($family == null) {
                throw new Exception("Family Not found to save expense", 104);                
            }

            $expense = new Expense;
            $expense->user_id                   = $user_id;
            $expense->user_displayName          = $user_displayName;
            $expense->family_id                 = $family->id;
            $expense->expense_amount            = $expenseAmount;
            $expense->expense_category          = $expenseType;
            $expense->expense_category_name     = "";
            $expense->expense_description       = $expenseDescription;
            $expense->expense_date              = date('Y-m-d H:i:s', strtotime($dateTime));
            $expense->save();

            $arrayresponse = array(
                'status'    =>  true,
                'message'   =>  'Expense saved successfully',
            );

        } catch(Exception $e) {
            $arrayresponse = array(
                'status'    =>  false,
                'message'   =>  $e->getMessage(),
                'code'      =>  $e->getCode()
            );
        }

        return $arrayresponse;
    }

    /**
     * Function that wll get all the family expenses
     * @return [type] [description]
     */
    public function getFamilyExpenses(Request $request) {
        $arrayresponse = array();
        
        try {
            $content = json_decode($request->getContent(), true);
            $familySlack = $content['familySlack'];

            $family = Family::where('familySlack', '=', $familySlack)->first();
            if($family == null) {
                throw new Exception("Family Not found to get all expenses", 104);
            }

            $expenses = Expense::where('family_id', '=', $family->id)
                                ->orderBy('expense_date', 'desc')
                                ->get()
                                ->toArray();

            $arrayresponse = array(
                'status'    =>  true,
                'data'      =>  $expenses
            );

        } catch(Exception $e) {
            $arrayresponse = array(
                'status'    =>  false,
                'message'   =>  $e->getMessage(),
                'code'      =>  $e->getCode()
            );
        }

        return $arrayresponse;
    }
}










<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseController extends Controller {

    /**
     * Function that will add the expense
     * @param Request $request [description]
     */
    public function addExpense(Request $request) {
        $content = json_decode($request->getContent(), true);
        
    }
}
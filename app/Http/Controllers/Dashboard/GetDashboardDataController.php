<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GetDashboardDataController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $openedCashRegister = CashRegister::query()
                    ->where('opened', '=', true)
                    ->where('user_id', '=', $request->user()->id)
                    ->with([
                        'transactions',
                        'transactions.products'
                    ])
                    ->first()
                    ->toArray();
                    
            return response_ok(data: [
                'cash_register' => $openedCashRegister
            ]);
            
        } catch (ValidationException $e) {
            return response_no(422, $e->errors());
        } catch (Exception $e) {
            return response_no(message: $e->getMessage());
        }
    }
}

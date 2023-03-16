<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GetDashboardDataController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            /** @var \App\Models\User */
            $user = $request->user();

            $openedCashRegister = $user->getOpenedCashRegister();
            
            return response_ok(data: [
                'cashRegister' => $openedCashRegister->toArray()
            ]);
            
        } catch (ValidationException $e) {
            return response_no(422, $e->errors());
        } catch (Exception $e) {
            return response_no(message: $e->getMessage());
        }
    }
}

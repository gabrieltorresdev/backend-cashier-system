<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Repositories\CashRegisterRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GetCurrentOpenedCashRegisterController extends Controller
{
    public function __construct(
        private Request $request,
        private CashRegisterRepository $cashRegisterRepository
    ) {
    }

    public function __invoke()
    {
        try {
            $openedCashRegister = $this->cashRegisterRepository
                ->getByUser($this->request->user()->id);

            if (!$openedCashRegister) {
                throw_exception(__('custom.cash-register.opened-not-found'), 404);
            }

            return response_ok(data: $openedCashRegister);
        } catch (ValidationException $e) {
            return response_no(422, $e->errors());
        } catch (\Throwable $e) {
            return response_no(code: $e->getCode(), message: $e->getMessage());
        }
    }
}

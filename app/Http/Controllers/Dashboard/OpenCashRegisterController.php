<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Repositories\CashRegisterRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OpenCashRegisterController extends Controller
{
    public function __construct(
        private Request $request,
        private CashRegisterRepository $cashRegisterRepository
    ) {
    }

    public function __invoke()
    {
        try {            
            if (!$this->cashRegisterRepository->handleOpen($this->request->user()->id))
                throw_exception(__("custom.cash-register.error-on-open"), 400);

            return response_ok(message: __("custom.cash-register.success-on-open"));
        } catch (ValidationException $e) {
            return response_no(422, $e->errors());
        } catch (\Throwable $e) {
            return response_no(message: $e->getMessage());
        }
    }
}

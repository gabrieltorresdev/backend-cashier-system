<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Repositories\CashRegisterRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CloseCashRegisterController extends Controller
{
    public function __construct(
        private Request $request,
        private CashRegisterRepository $cashRegisterRepository
    ) {
    }

    public function __invoke()
    {
        try {
            $this->validateFields();

            $this->cashRegisterRepository->whereModel($this->request->input('id'));

            if (!$this->cashRegisterRepository->handleClose())
                throw_exception(__("custom.cash-register.error-on-close"), 400);

            return response_ok(message: __("custom.cash-register.success-on-close"));
        } catch (ValidationException $e) {
            return response_no(422, $e->errors());
        } catch (\Throwable $e) {
            return response_no(message: $e->getMessage());
        }
    }

    private function validateFields()
    {
        $this->request->validate([
            'id' => 'required|string|uuid|exists:cash_registers'
        ]);
    }
}

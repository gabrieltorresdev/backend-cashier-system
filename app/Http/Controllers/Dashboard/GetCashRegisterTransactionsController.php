<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Repositories\CashRegisterRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GetCashRegisterTransactionsController extends Controller
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

            $transactions = $this->getTransactions();
            $total = $this->getTotal($transactions);

            return response_ok(data: [
                'total' => $total,
                'transactions' => $transactions
            ]);
        } catch (ValidationException $e) {
            return response_no(422, $e->errors());
        } catch (\Throwable $e) {
            return response_no(message: $e->getMessage());
        }
    }

    private function validateFields(): void
    {
        $this->request->validate([
            'id' => 'nullable|string|uuid|exists:transactions',
            'cash_register_id' => 'required_without:id|string|uuid|exists:cash_registers,id'
        ]);
    }

    /**
     * @throws \Exception
     */
    private function getTransactions(): array
    {
        $id             = $this->request->input('id');
        $cashRegisterId = $this->request->input('cash_register_id');

        if ($cashRegisterId && !$id) {
            $this->cashRegisterRepository->whereModel($cashRegisterId);
        }

        $transactions = $this->cashRegisterRepository->getTransactions($id);

        if (empty($transactions)) {
            throw_exception(__('custom.common.no-data-found'), 404);
        }

        return $transactions;
    }

    private function getTotal(array $transactions): float
    {
        return array_reduce(
            $transactions,
            function ($carry, $transaction) {
                if (in_array($transaction['type'], ['withdrawal', 'return'])) {
                    return bcsub($carry, $transaction['value'], 2);
                }
                
                return bcadd($carry, $transaction['value'], 2);
            },
            0
        );
    }
}

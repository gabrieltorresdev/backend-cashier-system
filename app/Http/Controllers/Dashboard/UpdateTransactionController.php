<?php

namespace App\Http\Controllers\Dashboard;

use App\DTO\CashRegisterDTO;
use App\DTO\TransactionDTO;
use App\Http\Actions\Dashboard\UpdateCashRegisterAction;
use App\Http\Actions\Dashboard\UpdateTransactionAction;
use App\Http\Controllers\Controller;
use App\Repositories\CashRegisterRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateTransactionController extends Controller
{
    public function __construct(
        private Request $request,
        private TransactionDTO $transactionDTO,
        private CashRegisterDTO $cashRegisterDTO,
        private CashRegisterRepository $cashRegisterRepository,
        private TransactionRepository $transactionRepository,
        private UpdateTransactionAction $updateTransactionAction,
        private UpdateCashRegisterAction $updateCashRegisterAction
    ) {
    }

    public function __invoke()
    {
        try {
            $this->validateFields();
            $this->ensureUserHavePermissions();
            $this->ensureTransactionIsOpened();
            $this->verifyIfTransactionNeedProducts();

            $this->transactionDTO->fill($this->request->all());

            DB::beginTransaction();

            if (!$this->updateTransactionAction->execute($this->transactionDTO)) {
                throw_exception($this->getErrorResponseMessage());
            }

            $this->cashRegisterDTO->fill([
                'id' => $this->request->input('cash_register_id'),
            ]);

            if (!$this->updateCashRegisterAction->execute($this->cashRegisterDTO)) {
                throw_exception($this->getErrorResponseMessage());
            }

            DB::commit();

            return response_ok(message: $this->getSuccessResponseMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return response_no(422, $e->errors());
        } catch (\Throwable $e) {
            DB::rollBack();
            return response_no(message: $e->getMessage());
        }
    }

    private function validateFields()
    {
        $this->request->validate([
            'id' => 'nullable|string|uuid|exists:transactions',
            'cash_register_id' => 'required|string|uuid|exists:cash_registers,id',
            'type' => 'required|string|in:sale,return,withdrawal,deposit',
            'note' => 'nullable|string',
            'value' => 'required_if:type,withdrawal,deposit|prohibited_if:type,sale|decimal:0,2|not_in:0|gt:0',
            'finished' => 'required|boolean',
            'products' => 'prohibited_if:type,withdrawal,deposit|nullable|array',
            'products.*.id' => 'string|uuid|exists:products,id',
            'products.*.quantity' => 'decimal:0,4|not_in:0|gt:0',
        ]);
    }

    /**
     * @throws ValidationException
     */
    private function verifyIfTransactionNeedProducts(): void
    {
        $isFinishedSaleOrReturn = $this->request->input('finished')
            && in_array($this->request->input('type'), ['sale', 'return']);

        $hasNoProducts = empty($this->request->input('products'))
            && !$this->transactionRepository->hasProducts();

        if ($isFinishedSaleOrReturn && $hasNoProducts) {
            throw_validation_exception([__('custom.transaction.need-products')]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function ensureTransactionIsOpened(): void
    {
        if (!$this->request->input('id')) return;

        $this->transactionRepository->whereModel($this->request->input('id'));

        $transactionFinished = $this->transactionRepository->isFinished();

        if (!is_null($transactionFinished) && $transactionFinished) {
            throw_exception(__("custom.transaction.cannot-modify-closed"), 400);
        }
    }

    /**
     * @throws ValidationException
     */
    private function ensureUserHavePermissions()
    {
        // TODO: Modificar essa condição abaixo para permitir que o admin passe
        if (!$this->cashRegisterRepository->getByUser($this->request->user()->id)) {
            throw_exception(__("custom.common.action-not-permitted"), 403);
        }
    }

    private function getErrorResponseMessage(): string
    {
        return !$this->request->input('id')
            ? __("custom.transaction.error-on-create")
            : __("custom.transaction.error-on-update");
    }

    private function getSuccessResponseMessage(): string
    {
        return !$this->request->input('id')
            ? __("custom.transaction.success-on-create")
            : __("custom.transaction.success-on-update");
    }
}

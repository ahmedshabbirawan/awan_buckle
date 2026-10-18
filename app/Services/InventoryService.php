<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAvailable;
use App\Models\StockAudit;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Atomically mutate stock and record an immutable ledger entry.
     *
     * @param int $productId
     * @param int $shopId
     * @param int $quantity Absolute positive quantity to adjust
     * @param string $action StockAudit::STOCK_ACTION_ADD or StockAudit::STOCK_ACTION_MINUS
     * @param string $objectType e.g., 'Sale', 'StockPurchase', 'SaleReturn', 'StockAdjustment', 'StockExchange'
     * @param int|null $objectId Primary key of the originating event
     * @param string|null $description Audit notes
     * @param bool $allowNegative Override negative stock protection
     * @return ProductAvailable
     * @throws Exception
     */
    public static function mutateStock(
        int $productId,
        int $shopId,
        int $quantity,
        string $action,
        string $objectType,
        ?int $objectId = null,
        ?string $description = null,
        bool $allowNegative = false
    ): ProductAvailable {
        if ($quantity <= 0) {
            throw new Exception("Stock mutation quantity must be strictly greater than zero. Received: {$quantity}");
        }

        if (!in_array($action, [StockAudit::STOCK_ACTION_ADD, StockAudit::STOCK_ACTION_MINUS], true)) {
            throw new Exception("Invalid stock action: {$action}");
        }

        // Auto-wrap in transaction if not already inside one
        if (DB::transactionLevel() === 0) {
            return DB::transaction(function () use ($productId, $shopId, $quantity, $action, $objectType, $objectId, $description, $allowNegative) {
                return self::executeMutation($productId, $shopId, $quantity, $action, $objectType, $objectId, $description, $allowNegative);
            });
        }

        return self::executeMutation($productId, $shopId, $quantity, $action, $objectType, $objectId, $description, $allowNegative);
    }

    /**
     * Internal atomic execution under pessimistic row lock.
     */
    protected static function executeMutation(
        int $productId,
        int $shopId,
        int $quantity,
        string $action,
        string $objectType,
        ?int $objectId,
        ?string $description,
        bool $allowNegative
    ): ProductAvailable {
        // 1. Acquire pessimistic row lock
        $stockRecord = ProductAvailable::where('shop_id', $shopId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        $oldQty = $stockRecord ? (int)$stockRecord->qty : 0;
        $delta = ($action === StockAudit::STOCK_ACTION_ADD) ? $quantity : -$quantity;
        $newQty = $oldQty + $delta;

        // 2. Enforce Non-Negative Inventory Invariant
        if ($newQty < 0 && !$allowNegative) {
            $product = Product::find($productId);
            $productName = $product ? $product->name : "ID #{$productId}";
            throw new Exception("Insufficient stock for '{$productName}'. Available: {$oldQty}, Requested: {$quantity}");
        }

        // 3. Update or Insert Snapshot Table
        if ($stockRecord) {
            $stockRecord->qty = $newQty;
            $stockRecord->save();
        } else {
            $stockRecord = ProductAvailable::create([
                'shop_id'    => $shopId,
                'product_id' => $productId,
                'qty'        => $newQty,
            ]);
        }

        // 4. Record Immutable Ledger Entry
        StockAudit::create([
            'product_id'      => $productId,
            'shop_id'         => $shopId,
            'action'          => $action,
            'old_qty'         => $oldQty,
            'update_qty'      => $quantity,
            'current_qty'     => $newQty,
            'object_type'     => $objectType,
            'object_id'       => $objectId,
            'description'     => $description,
            'stock_old_value' => json_encode(['qty' => $oldQty]),
            'stock_new_value' => json_encode(['qty' => $newQty]),
            'created_by'      => auth()->check() ? auth()->id() : 1,
        ]);

        return $stockRecord;
    }

    /**
     * Audit-proof calculation of true stock from the historical ledger.
     */
    public static function calculateLedgerStock(int $productId, int $shopId): int
    {
        $added = (int)StockAudit::where('product_id', $productId)
            ->where('shop_id', $shopId)
            ->where('action', StockAudit::STOCK_ACTION_ADD)
            ->sum('update_qty');

        $deducted = (int)StockAudit::where('product_id', $productId)
            ->where('shop_id', $shopId)
            ->where('action', StockAudit::STOCK_ACTION_MINUS)
            ->sum('update_qty');

        return $added - $deducted;
    }

    /**
     * Check if a product has sufficient stock at a shop.
     */
    public static function hasStock(int $productId, int $shopId, int $requiredQty): bool
    {
        if ($requiredQty <= 0) {
            return false;
        }

        $record = ProductAvailable::where('shop_id', $shopId)
            ->where('product_id', $productId)
            ->first();

        return $record ? ($record->qty >= $requiredQty) : false;
    }

    /**
     * Get available stock quantity safely.
     */
    public static function getAvailableStock(int $productId, int $shopId): int
    {
        $record = ProductAvailable::where('shop_id', $shopId)
            ->where('product_id', $productId)
            ->first();

        return $record ? (int)$record->qty : 0;
    }
}

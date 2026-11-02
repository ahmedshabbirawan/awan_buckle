<?php

namespace App\Console\Commands;

use App\Models\StockDelivery;
use App\Services\LedgerService;
use Illuminate\Console\Command;

class ConvertPurchaseToCredit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchase:convert-to-credit {delivery_id : The ID of the Stock Delivery / Purchase} {--force : Force without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert a Cash or Bank purchase to Credit terms and update the Double-Entry Ledger accordingly';

    /**
     * Execute the console command.
     */
    public function handle(LedgerService $ledgerService)
    {
        $deliveryId = (int) $this->argument('delivery_id');
        $delivery = StockDelivery::with('supplier')->find($deliveryId);

        if (!$delivery) {
            $this->error("Stock Delivery / Purchase #{$deliveryId} not found.");
            return 1;
        }

        if (!$delivery->supplier_id) {
            $this->error("Purchase #{$deliveryId} has no assigned supplier. Credit purchases require a supplier.");
            return 1;
        }

        $total = number_format($delivery->total_amount ?: (float)$delivery->delivery_amount, 2);
        $supplierName = optional($delivery->supplier)->name ?? "ID: {$delivery->supplier_id}";
        $currentMethod = strtoupper($delivery->payment_method ?? 'cash');

        $this->info("--------------------------------------------------");
        $this->info("Purchase ID       : #{$delivery->id}");
        $this->info("Supplier          : {$supplierName}");
        $this->info("Total Amount      : PKR {$total}");
        $this->info("Current Method    : {$currentMethod}");
        $this->info("Current Paid      : PKR " . number_format($delivery->paid_amount, 2));
        $this->info("Current Due       : PKR " . number_format($delivery->due_amount, 2));
        $this->info("--------------------------------------------------");

        if (strtolower($delivery->payment_method) === 'credit' && (float)$delivery->due_amount == (float)$delivery->total_amount) {
            $this->warn("Purchase #{$deliveryId} is already recorded as Credit with full amount due.");
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to convert Purchase #{$deliveryId} from {$currentMethod} to CREDIT?")) {
                $this->info("Operation cancelled.");
                return 0;
            }
        }

        try {
            $updated = $ledgerService->convertPurchaseToCredit($deliveryId);

            $this->info("");
            $this->info("SUCCESS: Purchase #{$deliveryId} has been converted to Credit!");
            $this->info(" - payment_method : 'credit'");
            $this->info(" - paid_amount    : PKR 0.00");
            $this->info(" - due_amount     : PKR " . number_format($updated->due_amount, 2));
            $this->info(" - payment_status : 'due'");
            $this->info(" - General Ledger : Cash/Bank credit removed, Supplier AP credited, balances resynced.");
            $this->info(" - Supplier Ledger: Now shows as Credit Purchase with outstanding balance owed.");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error converting purchase: " . $e->getMessage());
            return 1;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\LedgerService;
use Illuminate\Console\Command;

class VoidPaymentVoucher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:void {payment_id : The ID of the Payment / Receipt Voucher} {--force : Force without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely void/delete an erroneous Payment or Receipt Voucher and reverse all related ledger entries';

    /**
     * Execute the console command.
     */
    public function handle(LedgerService $ledgerService)
    {
        $paymentId = (int) $this->argument('payment_id');
        $payment = Payment::find($paymentId);

        if (!$payment) {
            $this->error("Payment Voucher #{$paymentId} not found.");
            return 1;
        }

        $party = ($payment->party_type === Payment::PARTY_SUPPLIER) ? optional($payment->supplier)->name : optional($payment->customer)->name;

        $this->info("--------------------------------------------------");
        $this->info("Payment ID   : #{$payment->id}");
        $this->info("Voucher No   : {$payment->voucher_no}");
        $this->info("Type         : " . strtoupper($payment->payment_type));
        $this->info("Party        : {$payment->party_type} - {$party} (ID: {$payment->party_id})");
        $this->info("Amount       : PKR " . number_format($payment->amount, 2));
        $this->info("Date         : {$payment->payment_date}");
        $this->info("Method       : " . ucfirst($payment->payment_method));
        $this->info("--------------------------------------------------");

        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to VOID and DELETE voucher {$payment->voucher_no}?")) {
                $this->info("Operation cancelled.");
                return 0;
            }
        }

        try {
            $ledgerService->voidPayment($paymentId);

            $this->info("");
            $this->info("SUCCESS: Payment Voucher {$payment->voucher_no} has been voided and deleted!");
            $this->info(" - Ledger entries reversed.");
            $this->info(" - Account balances (Cash & Party) recalculated and synced.");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error voiding voucher: " . $e->getMessage());
            return 1;
        }
    }
}

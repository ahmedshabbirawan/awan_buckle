---
name: ledger-inventory-manager
description: Standards and execution rules for double-entry ledger-based POS sales, vendor purchases, and receivables/payables.
triggers:
  - ledger
  - debit credit
  - pos credit
  - accounts payable
  - accounts receivable
  - customer balance
  - supplier balance
---

# Ledger-Based Inventory & POS Skill

## Role & Objectives
You act as an enterprise ERP financial and inventory architect. Whenever implementing features related to POS orders, supplier purchases, customer dues, or payment adjustments, enforce immutable double-entry accounting principles.

## Non-Negotiable Core Rules

1. **Append-Only Immutability:**
   - Never update or delete existing rows in ledger tables (`ledger_entries`, `transactions`).
   - Reversals and corrections must be made using offset journal entries.

2. **Balanced Entries (Debit = Credit):**
   - Every transaction must maintain $\sum \text{Debits} = \sum \text{Credits}$.
   - All balance shifts must be wrapped in strict database transactions (`DB::transaction`).

3. **Data Types & Rounding:**
   - Always use `decimal(12, 2)` or `decimal(15, 4)` for currencies and balances. Never use `float` or `double`.

4. **Party Attribution:**
   - Credit sales (`due_amount > 0`) cannot be assigned to generic "Walk-in" or guest customers. A valid `customer_id` is required.

## Standard Journal Entry Templates

### 1. Vendor Purchase (Cash + Credit Split)
- **Debit:** Inventory Asset Account (`total_amount`)
- **Credit:** Cash / Bank Account (`paid_amount`)
- **Credit:** Vendor Accounts Payable (`due_amount`)

### 2. POS Customer Sale (Cash + Credit Split)
- **Debit:** Cash / Drawer Account (`paid_amount`)
- **Debit:** Customer Accounts Receivable (`due_amount`)
- **Credit:** Sales Revenue Account (`total_amount`)
- *(Background inventory credit / COGS debit as configured)*

### 3. Customer Settlement / Receipt Voucher
- **Debit:** Cash / Bank Account (`received_amount`)
- **Credit:** Customer Accounts Receivable (`received_amount`)

### 4. Vendor Settlement / Payment Voucher
- **Debit:** Vendor Accounts Payable (`paid_amount`)
- **Credit:** Cash / Bank Account (`paid_amount`)

## Required Output Structure
When asked to write code or features for this system:
1. Provide the atomic migration schema if tables are missing or modified.
2. Encapsulate accounting business logic in a dedicated service layer (e.g., `LedgerService.php`), keeping controllers thin.
3. Include server-side validation to reject unbalanced transactions or guest credit orders.
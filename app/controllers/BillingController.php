<?php
// ============================================================
//  BillingController — Folios, charges, payments
//  Routes:
//    /billing              → index (list folios)
//    /billing/show/5       → show folio details
//    /billing/addCharge/5  → post a charge to folio #5
//    /billing/payment/5    → record payment on folio #5
//    /billing/invoice/5    → pro-forma invoice preview
// ============================================================

class BillingController extends Controller
{
    public function index()
    {
        // TODO: Require login
        // TODO: Load all open folios with guest names
        // TODO: Pass to billing/index view
        $this->view('billing/index');
    }

    public function show($id)
    {
        // TODO: Load folio with all charges and payments
        // TODO: Pass to billing/show view
        $this->view('billing/show');
    }

    public function addCharge($id)
    {
        // TODO: Validate $_POST (charge_type, description, amount)
        // TODO: Call FolioCharge model create()
        // TODO: Recalculate folio total
        // TODO: Log to audit_log if manual override
        // TODO: Redirect to billing/show/$id
    }

    public function payment($id)
    {
        // TODO: Validate $_POST (amount, method)
        // TODO: Call Payment model create()
        // TODO: Update folio amount_paid
        // TODO: Check if folio is settled
        // TODO: Redirect to billing/show/$id
    }

    public function invoice($id)
    {
        // TODO: Generate Pro-Forma Invoice
        // TODO: Pass to billing/invoice view
        $this->view('billing/invoice');
    }

    public function refund($id)
    {
        // TODO: Validate refund amount
        // TODO: Call Payment model processRefund()
        // TODO: Log to audit_log
    }

    public function splitBill($id)
    {
        // TODO: Shared-Expense Split-Bill Logic
        // TODO: Pass to billing/split view
        $this->view('billing/split');
    }
}

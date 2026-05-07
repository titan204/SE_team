<?php

class RevenueManager extends AbstractReport
{
    protected $id;
    protected $report_code;
    protected $period_start;
    protected $period_end;
    protected $summary_status;
    protected $notes;
    protected $created_at;
    protected $updated_at;

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->setReportScope('revenue');
        $this->setReportInputs(['period_start', 'period_end', 'filters']);
        $this->registerAggregate('folios', Folio::class);
        $this->registerAggregate('reservations', Reservation::class);
        $this->registerAggregate('auditLogs', AuditLog::class);
    }
}

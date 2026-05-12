<?php


class PaymentService extends AbstractModel
{
    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->registerAggregate('guests', Guest::class);
        $this->registerAggregate('reservations', Reservation::class);
        $this->registerAggregate('auditLogs', AuditLog::class);
    }

    
    public function chargeGuestCard(
        int    $guestId,
        float  $amount,
        string $reason,
        int    $reservationId = 0,
        string $idempotencyKey = ''
    ): array {
        $guestId       = (int)   $guestId;
        $reservationId = (int)   $reservationId;
        $amount        = (float) $amount;

        
        if ($idempotencyKey === '') {
            $idempotencyKey = $reservationId . '_' . $reason . '_' . $amount;
        }

        
        $existing = $this->findTransactionByIdempotencyKey($idempotencyKey);
        if ($existing) {
            if ($existing['status'] === 'success') {
                return ['success' => true, 'transactionId' => (int) $existing['id']];
            }
            
        }

        
        $card = $this->getDefaultPaymentMethod($guestId);

        
        if (!$card) {
            $this->createPendingDebt($guestId, $reservationId, $amount, $reason);
            $this->notifyFrontDesk($guestId, $reservationId, 'NO_CARD_ON_FILE', $reason);
            return ['error' => 'NO_CARD_ON_FILE'];
        }

        
        if ($this->isCardExpired($card)) {
            $this->notifyFrontDesk($guestId, $reservationId, 'CARD_EXPIRED', $reason);
            return ['error' => 'CARD_EXPIRED'];
        }

        
        $type    = $amount < 0 ? 'refund' : 'charge';
        $gateway = $this->submitToGateway($card['gateway_token'], $amount, $idempotencyKey);

        
        if ($gateway['status'] === 'success') {
            $txId = $this->insertTransaction([
                'guest_id'        => $guestId,
                'reservation_id'  => $reservationId,
                'amount'          => $amount,
                'type'            => $type,
                'reason'          => $reason,
                'gateway_ref'     => $gateway['gateway_ref'],
                'status'          => 'success',
                'idempotency_key' => $idempotencyKey,
                'failure_reason'  => null,
            ]);
            $this->sendReceiptEmail($guestId, $amount, $reason, $gateway['gateway_ref']);
            return ['success' => true, 'transactionId' => $txId];
        }

        
        if ($gateway['status'] === 'declined') {
            $this->insertTransaction([
                'guest_id'        => $guestId,
                'reservation_id'  => $reservationId,
                'amount'          => $amount,
                'type'            => $type,
                'reason'          => $reason,
                'gateway_ref'     => $gateway['gateway_ref'] ?? null,
                'status'          => 'failed',
                'idempotency_key' => $idempotencyKey,
                'failure_reason'  => $gateway['failure_reason'] ?? 'Card declined',
            ]);
            $this->createPendingDebt($guestId, $reservationId, $amount, $reason);
            $this->notifyFrontDesk($guestId, $reservationId, 'CARD_DECLINED', $reason);
            return ['error' => 'CARD_DECLINED'];
        }

        
        $this->addToRetryQueue($guestId, $reservationId, $amount, $reason, $idempotencyKey);
        return ['error' => 'NETWORK_ERROR', 'queued' => true];
    }

    
    public function getDefaultPaymentMethod(int $guestId): ?array
    {
        $guestId = (int) $guestId;
        $result  = mysqli_query($this->db,
            "SELECT id, guest_id, gateway_token, card_last4, card_brand,
                    expiry_month, expiry_year, is_default
             FROM   payment_methods
             WHERE  guest_id = $guestId AND is_default = 1
             LIMIT  1");
        if (!$result) return null;
        $row = mysqli_fetch_assoc($result);
        return $row ?: null;
    }

    
    private function isCardExpired(array $card): bool
    {
        $expYear  = (int) $card['expiry_year'];
        $expMonth = (int) $card['expiry_month'];
        $nowYear  = (int) date('Y');
        $nowMonth = (int) date('m');
        return ($expYear < $nowYear) || ($expYear === $nowYear && $expMonth < $nowMonth);
    }

    
    private function submitToGateway(string $gatewayToken, float $amount, string $idempotencyKey): array
    {
                     → success
        $suffix = substr($gatewayToken, -4);

        if ($suffix === '0000') {
            return [
                'status'         => 'declined',
                'gateway_ref'    => null,
                'failure_reason' => 'Insufficient funds',
            ];
        }

        if ($suffix === '9999') {
            return ['status' => 'network_error'];
        }

        return [
            'status'      => 'success',
            'gateway_ref' => 'GW-' . strtoupper(substr(md5($idempotencyKey . time()), 0, 12)),
        ];
    }

    
    private function insertTransaction(array $d): int
    {
        $guestId        = (int)    $d['guest_id'];
        $reservationId  = (int)    $d['reservation_id'];
        $amount         = (float)  $d['amount'];
        $type           = mysqli_real_escape_string($this->db, $d['type']);
        $reason         = mysqli_real_escape_string($this->db, $d['reason']);
        $gatewayRef     = $d['gateway_ref']
                          ? "'" . mysqli_real_escape_string($this->db, $d['gateway_ref']) . "'"
                          : 'NULL';
        $status         = mysqli_real_escape_string($this->db, $d['status']);
        $idempotencyKey = mysqli_real_escape_string($this->db, $d['idempotency_key']);
        $failureReason  = $d['failure_reason']
                          ? "'" . mysqli_real_escape_string($this->db, $d['failure_reason']) . "'"
                          : 'NULL';
        $resCol         = $reservationId > 0 ? $reservationId : 'NULL';

        mysqli_query($this->db,
            "INSERT INTO transactions
                 (guest_id, reservation_id, amount, type, reason, gateway_ref,
                  status, idempotency_key, failure_reason)
             VALUES
                 ($guestId, $resCol, $amount, '$type', '$reason', $gatewayRef,
                  '$status', '$idempotencyKey', $failureReason)");

        return (int) mysqli_insert_id($this->db);
    }

    
    private function findTransactionByIdempotencyKey(string $key): ?array
    {
        $key    = mysqli_real_escape_string($this->db, $key);
        $result = mysqli_query($this->db,
            "SELECT * FROM transactions WHERE idempotency_key = '$key' LIMIT 1");
        if (!$result) return null;
        $row = mysqli_fetch_assoc($result);
        return $row ?: null;
    }

    
    private function createPendingDebt(int $guestId, int $reservationId, float $amount, string $reason): void
    {
        $guestId       = (int)   $guestId;
        $reservationId = (int)   $reservationId;
        $amount        = (float) $amount;
        $reason        = mysqli_real_escape_string($this->db, $reason);
        $resCol        = $reservationId > 0 ? $reservationId : 'NULL';

        mysqli_query($this->db,
            "INSERT INTO pending_debts (guest_id, reservation_id, amount, reason)
             VALUES ($guestId, $resCol, $amount, '$reason')");
    }

    
    private function addToRetryQueue(
        int    $guestId,
        int    $reservationId,
        float  $amount,
        string $reason,
        string $idempotencyKey
    ): void {
        $guestId        = (int)   $guestId;
        $reservationId  = (int)   $reservationId;
        $amount         = (float) $amount;
        $reason         = mysqli_real_escape_string($this->db, $reason);
        $idempotencyKey = mysqli_real_escape_string($this->db, $idempotencyKey);
        $resCol         = $reservationId > 0 ? $reservationId : 'NULL';

        mysqli_query($this->db,
            "INSERT INTO payment_retry_queue
                 (guest_id, reservation_id, amount, reason, idempotency_key,
                  attempt_count, next_retry_at)
             VALUES
                 ($guestId, $resCol, $amount, '$reason', '$idempotencyKey',
                  0, DATE_ADD(NOW(), INTERVAL 15 MINUTE))
             ON DUPLICATE KEY UPDATE
                 attempt_count = attempt_count + 1,
                 next_retry_at = DATE_ADD(NOW(), INTERVAL 15 MINUTE)");
    }

    
    private function notifyFrontDesk(int $guestId, int $reservationId, string $errorCode, string $reason): void
    {
        $guestId       = (int) $guestId;
        $reservationId = (int) $reservationId;
        $userId        = (int) ($_SESSION['user_id'] ?? 0);
        $message       = mysqli_real_escape_string($this->db,
            "Payment issue for guest #$guestId on reservation #$reservationId: "
            . "$errorCode — $reason"
        );

        mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'payment_alert', 'reservation', $reservationId,
                     '$errorCode', '$message')");
    }

    
    private function sendReceiptEmail(int $guestId, float $amount, string $reason, string $gatewayRef): void
    {
        $guestId    = (int)   $guestId;
        $amount     = (float) $amount;
        $userId     = (int)   ($_SESSION['user_id'] ?? 0);
        $gatewayRef = mysqli_real_escape_string($this->db, $gatewayRef);
        $message    = mysqli_real_escape_string($this->db,
            "Receipt sent to guest #$guestId: amount=$amount reason=$reason ref=$gatewayRef"
        );

        mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'receipt_sent', 'guest', $guestId, '$gatewayRef', '$message')");
    }

    
    public function resolvePendingDebt(int $debtId): bool
    {
        $debtId = (int) $debtId;
        $result = mysqli_query($this->db,
            "UPDATE pending_debts SET resolved_at = NOW() WHERE id = $debtId AND resolved_at IS NULL");
        return $result && mysqli_affected_rows($this->db) > 0;
    }
}

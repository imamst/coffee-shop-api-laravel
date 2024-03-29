<?php

namespace App\Interfaces;

interface PaymentRepositoryInterface
{
    public function save(array $paymentDetails);

    public function getByOrderId(string $orderId);
}

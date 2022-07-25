<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Interfaces\{
    OrderServiceInterface,
    ShippingInformationServiceInterface,
    BillingAddressServiceInterface,
    PaymentServiceInterface
};
use App\Http\Requests\CreateOrderRequest;
use App\Helpers\CookieHelper;

class OrderController extends Controller
{
    public function __construct(
        public OrderServiceInterface $orderService,
        public ShippingInformationServiceInterface $shippingInformationService,
        public BillingAddressServiceInterface $billingAddressService,
        public PaymentServiceInterface $paymentService
    )
    {

    }

    public function store(CreateOrderRequest $request)
    {
        try {
            $cartId = CookieHelper::getCookieValue(config('constants.cookie_name.cart'));
            $orderDetails = $request->validated();
            $order = $this->orderService->createOrder($cartId, $orderDetails);

            $this->shippingInformationService->updateShippingInfo($cartId, [
                'shippingable_type' => 'App\Models\Order',
                'shippingable_id' => $order->id
            ]);

            $billingAddressDetails = array_merge($orderDetails, ['order_id' => $order->id]);
            $billingAddress = $this->billingAddressService->createBillingAddress($billingAddressDetails);

            $paymentDetails = $this->paymentService->chargeBankTransfer($order);
            $payment = $this->paymentService->createPayment($paymentDetails);

            return response()->json([
                'payment_detail' => $paymentDetails
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan sistem. Coba lagi atau mohon hubungi support kami'
            ], 500);
        }
    }
}

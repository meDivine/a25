<?php
namespace App\Controllers;

use App\Services\CalculateService;

class CalculateController
{
    private $calculateService;

    public function __construct(CalculateService $calculateService)
    {
        $this->calculateService = $calculateService;
    }

    public function handleRequest(): void
    {
        $days = $_POST['days'] ?? 0;
        $productId = $_POST['product'] ?? 0;
        $selectedServices = $_POST['services'] ?? [];

        try {
            $prices = $this->calculateService->calculateProductPrice((int)$productId, (int)$days, $selectedServices);
            $this->renderResponse($prices);
        } catch (\Exception $e) {
            $this->renderError($e->getMessage());
        }
    }

    private function renderResponse(array $prices): void
    {
        echo json_encode([
            'status' => 'success',
            'total_price_rub' => $prices['total_price_rub'],
            'total_price_cny' => $prices['total_price_cny'],
        ]);
    }

    private function renderError(string $errorMessage): void
    {
        echo json_encode([
            'status' => 'error',
            'message' => $errorMessage
        ]);
    }
}

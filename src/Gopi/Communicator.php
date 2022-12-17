<?php

namespace SnappMarket\Gopi;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use SnappMarket\Communicator\Communicator as BasicCommunicator;
use SnappMarket\Gopi\Dto\ChangeLocationDTO;
use SnappMarket\Gopi\Dto\CheckBarcodeDTO;
use SnappMarket\Gopi\Dto\CreatePalletBatchDTO;
use SnappMarket\Gopi\Dto\CreatePalletBatchProductDTO;
use SnappMarket\Gopi\Dto\DeductionDTO;
use SnappMarket\Gopi\Dto\ProductDetailDTO;
use SnappMarket\Gopi\Dto\ProductSearchDTO;
use SnappMarket\Gopi\Dto\ReplenishProductDTO;
use SnappMarket\Gopi\Exceptions\InventoryNotFoundException;
use SnappMarket\Gopi\Exceptions\InventoryRequestValidationException;

class Communicator extends BasicCommunicator
{
    const SECURITY_TOKEN_HEADER = 'Service-Security';
    const CONTENT_TYPE = 'Content-Type';

    public function __construct(
         string $baseUri,
         string $securityToken,
         array $headers = [],
         ?LoggerInterface $logger = null
    ) {
        $headers[static::SECURITY_TOKEN_HEADER] = $securityToken;
        $headers[static::CONTENT_TYPE] = static::APPLICATION_JSON;

        parent::__construct($baseUri, $headers, $logger);
    }

    public function deduction(DeductionDTO $deductionDTO): array
    {
        $uri = 'api/v1/product/deduction';

        $products = [];
        foreach ($deductionDTO->getProducts() as $product) {
            $products[] = [
                'barcode' => $product->getBarcode(),
                'reason_id' => $product->getReasonId(),
                'stocks' => $product->getStocks(),
            ];
        }

        try {
            $response = $this->request(static::METHOD_PUT, $uri, [
                'user_id' => $deductionDTO->getUserId(),
                'vendor_id' => $deductionDTO->getVendorId(),
                'products' => $products,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function productSearch(ProductSearchDTO $productSearchDTO): array
    {
        $uri = 'api/v1/product/search';

        try {
            $response = $this->request(static::METHOD_GET, $uri, [
                'barcode' => $productSearchDTO->getBarcode(),
                'title' => $productSearchDTO->getTitle(),
                'page' => $productSearchDTO->getPage(),
                'limit' => $productSearchDTO->getLimit(),
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function activateProducts($products, $vendorId, $userId)
    {
        $uri = 'api/v1/products/activate';
        return $this->changeStatusRequest($uri, $products, $vendorId, $userId);
    }

    public function deactivateProducts($products, $vendorId, $userId)
    {
        $uri = 'api/v1/products/deactivate';
        return $this->changeStatusRequest($uri, $products, $vendorId, $userId);
    }

    public function sensitiveProducts($products, $vendorId, $userId)
    {
        $uri = 'api/v1/products/sensitive';
        return $this->changeStatusRequest($uri, $products, $vendorId, $userId);
    }

    public function additiveProducts($products, $vendorId, $userId)
    {
        $uri = 'api/v1/products/additive';
        return $this->changeStatusRequest($uri, $products, $vendorId, $userId);
    }

    public function activateByLocation($locationBarcode, array $products, $vendorId, $userId)
    {
        $uri = 'api/v1/products/activate-by-location';

        try {
            $response = $this->request(static::METHOD_PATCH, $uri, [
                'user_id' => $userId,
                'vendor_id' => $vendorId,
                'products' => $products,
                'source' => 'NASIMA',
                'location_barcode' => $locationBarcode,
                'correlation_id' => uniqid(),
            ]);

            return json_decode((string)$response->getBody(), true)['data'];
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function searchLocationsByBarcode($vendorId, $productBarcode, $locationBarcode)
    {
        $uri = 'api/v1/find-locations';

        try {
            $response = $this->request(static::METHOD_GET, $uri, [
                'vendor_id' => $vendorId,
                'product_barcode' => $productBarcode,
                'location_barcode' => $locationBarcode,
                'correlation_id' => uniqid(),
            ]);

            return json_decode((string)$response->getBody(), true)['data'];
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function acceptCreatingPalletBatch($palletBarcode, $vendorId, $userId)
    {
        $uri = 'api/v1/pallets/' . $palletBarcode . '/batches/accept-creating';

        try {
            $response = $this->request(static::METHOD_PATCH, $uri, [
                'user_id' => $userId,
                'vendor_id' => $vendorId,
                'correlation_id' => uniqid(),
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function checkPalletStatus(int $vendorId, string $barcode): array
    {
        $uri = 'api/v1/pallet/status';

        try {
            $response = $this->request(static::METHOD_GET, $uri, [
                'barcode' => $barcode,
                'vendor_id' => $vendorId,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function productDetail(ProductDetailDTO $productDetailDTO): array
    {
        $uri = 'api/v1/product/detail';

        try {
            $response = $this->request(static::METHOD_GET, $uri, [
                'barcode' => $productDetailDTO->getBarcode(),
                'vendor_id' => $productDetailDTO->getVendorId(),
            ]);

            $this->checkError($response);

            $responseContent = $response->getBody()->__toString();
            $responseArray = json_decode($responseContent, true);
            return $responseArray;

        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() == 422) {
                $content = $exception->getResponse()->getBody()->getContents();
                $result = json_decode($content, true);
                throw new InventoryRequestValidationException($result['data']);
            } else if ($exception->getResponse()->getStatusCode() == 404) {
                throw new InventoryNotFoundException('product not found');
            }
            $this->logger->critical('Call Inventory Service API api/v1/product/detail : ' . $exception->getMessage(), $exception->getTrace());
        }

        return [];

    }

    public function createPalletBatch(CreatePalletBatchDTO $createPalletBatchDTO): array
    {
        $uri = 'api/v1/pallet/' . $createPalletBatchDTO->getPalletBarcode() . '/pallet-batch';

        $products = [];
        /** @var CreatePalletBatchProductDTO $product */
        foreach ($createPalletBatchDTO->getProducts() as $product) {
            $products[] = [
                'barcode' => $product->getBarcode(),
                'quantity' => $product->getQuantity(),
            ];
        }

        try {
            $response = $this->request(static::METHOD_PATCH, $uri, [
                'user_id' => $createPalletBatchDTO->getUserId(),
                'vendor_id' => $createPalletBatchDTO->getVendorId(),
                'products' => $products,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    private function changeStatusRequest($uri, $products, $vendorId, $userId)
    {
        try {
            $response = $this->request(static::METHOD_PATCH, $uri, [
                'user_id' => $userId,
                'vendor_id' => $vendorId,
                'products' => $products,
                'source' => 'NASIMA',
                'correlation_id' => uniqid(),
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    private function checkError($response)
    {
        $body = (string)$response->getBody();
        $content = json_decode($body, true);

        if ($response->getStatusCode() == 400 || $response->getStatusCode() == 422) {
            $this->logger->info('INVENTORY_SERVICE_ERROR', [
                'body' => $body,
            ]);
            if (!empty($content['errors'])) {
                $message = $content['errors'][0]['error'];
            } else if (!empty($content['message'])) {
                $message = $content['message'];
            } else {
                $message = $content['message'] ?? 'خطایی رخ داده است!';
            }
            throw new Exception($message);
        }

        if ($response->getStatusCode() == 404) {
            $message = $content['message'] ? $content['message'] : 'product not found';
            throw new InventoryNotFoundException($message);
        }

        if ($response->getStatusCode() != 200) {
            throw new Exception('خطای ناممشخص در ارتصال به سرویس!');
        }
    }

    public function deductionReasons(): array
    {
        $uri = 'api/v1/deduction/reasons';

        try {
            $response = $this->request(static::METHOD_GET, $uri);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function changeLocation(ChangeLocationDTO $changeLocationDTO): bool
    {
        $uri = 'api/v1/aisle/location';

        try {
            $response = $this->request(static::METHOD_PUT, $uri, [
                'user_id' => $changeLocationDTO->getUserId(),
                'vendor_id' => $changeLocationDTO->getVendorId(),
                'product_barcode' => $changeLocationDTO->getProductBarcode(),
                'current_aisle_barcode' => $changeLocationDTO->getCurrentAisleBarcode(),
                'new_aisle_barcode' => $changeLocationDTO->getNewAisleBarcode(),
            ]);

            return true;

        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function cancelCreatingPalletBatch($palletBarcode, $vendorId, $userId): array
    {
        $uri = 'api/v1/pallet/' . $palletBarcode . '/cancel-creating';
        try {
            $response = $this->request(static::METHOD_DELETE, $uri, [
                'user_id' => $userId,
                'vendor_id' => $vendorId,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function checkBarcode(CheckBarcodeDTO $checkBarcodeDTO): array
    {
        $uri = 'api/v1/product/check-barcode';

        try {
            $response = $this->request(static::METHOD_GET, $uri, [
                'barcode' => $checkBarcodeDTO->getBarcode(),
                'vendor_id' => $checkBarcodeDTO->getVendorId(),
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function acceptEditingPalletBatch($palletBatchId, $userId): array
    {
        $uri = 'api/v1/pallet-batch/' . $palletBatchId . '/accept-editing';
        try {
            $response = $this->request(static::METHOD_PUT, $uri, [
                'user_id' => $userId,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function cancelEditingPalletBatch($palletBatchId, $userId): array
    {
        $uri = 'api/v1/pallet-batch/' . $palletBatchId . '/cancel-editing';
        try {
            $response = $this->request(static::METHOD_PUT, $uri, [
                'user_id' => $userId,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function getPendingPalletBatches(int $vendorId, int $userId): array
    {
        $uri = 'api/v1/pallet-batch/pending';

        try {
            $response = $this->request(static::METHOD_GET, $uri, [
                'user_id' => $userId,
                'vendor_id' => $vendorId,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function cancelReplenishingPalletBatch($palletBatchId, $userId): array
    {
        $uri = 'api/v1/pallet-batch/' . $palletBatchId . '/cancel-replenishing';
        try {
            $response = $this->request(static::METHOD_PATCH, $uri, [
                'user_id' => $userId,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function acceptReplenishingPalletBatch($palletBatchId, $userId): array
    {
        $uri = 'api/v1/pallet-batch/' . $palletBatchId . '/accept-replenishing';
        try {
            $response = $this->request(static::METHOD_PATCH, $uri, [
                'user_id' => $userId,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function getPalletBatchProducts(int $palletBatchId): array
    {
        $uri = 'api/v1/pallet-batch/' . $palletBatchId . '/products';

        try {
            $response = $this->request(static::METHOD_GET, $uri);
            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function replenishProduct(ReplenishProductDTO $replenishProductDTO): array
    {
        $uri = 'api/v1/pallet-batch/' . $replenishProductDTO->getPalletBatchId() . '/products/' . $replenishProductDTO->getPalletBatchProductId() . '/replenished';
        try {
            $response = $this->request(static::METHOD_PATCH, $uri, [
                'user_id' => $replenishProductDTO->getUserId(),
                'quantity' => $replenishProductDTO->getQuantity(),
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function deletePalletBatch($palletBatchId, $userId): array
    {
        $uri = 'api/v1/pallet-batch/' . $palletBatchId;
        try {
            $response = $this->request(static::METHOD_DELETE, $uri, [
                'user_id' => $userId,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function productsPartialBulkUpdate($products)
    {
        $uri = 'api/v1/products';

        try {
            $response = $this->request(static::METHOD_PATCH, $uri, $products);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }

    public function storeProductChangeStockLog(int $userId, int $productId, int $VendorId, int $stockBefore,
                                               int $stockAfter, string $reasonCode, string $source, int $orderId = null)
    {
        $uri = 'api/v1/product/stock-log';

        try {
            $response = $this->request(static::METHOD_POST, $uri, [
                'user_id' => $userId,
                'product_id' => $productId,
                'order_id' => $orderId,
                'vendor_id' => $VendorId,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reason_code' => $reasonCode,
                'source' => $source,
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (ClientException $exception) {
            $this->checkError($exception->getResponse());
            throw new InventoryRequestValidationException($exception->getMessage());
        }
    }
}

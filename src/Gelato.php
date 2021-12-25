<?php
namespace iWebTouch\Gelato;

use iWebTouch\Gelato\Http\HttpClient;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Gelato
{
    private string $apiKey;

    private ?string $orderId = null;
    private ?string $customerId = null;
    private string $currency = 'USD';

    private string $shipmentMethod = 'normal';
    private array $shippingAddress = [];

    private array $items = [];

    public function __construct(private HttpClient $httpClient)
    {
        $this->setApiKey();
        $this->setRequestHeaders();
    }

    public function setApiKey(string $apiKey = null)
    {
        $this->apiKey = $apiKey?? env('GELATO_API_KEY');

        if (is_null($this->apiKey)) {
            throw new \Exception('Gelato API Key is not set!');
        }

        return $this;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    protected function setRequestHeaders()
    {
        if (is_null($this->apiKey)) {
            throw new \Exception('Gelato API Key has not set!');
        }

        $this->httpClient->setHeaders([
            'Content-Type' => 'application/json',
            'X-API-KEY' => $this->getApiKey()
        ]);

        return $this;
    }

    public function setFileUrl(string $url)
    {
        $this->fileUrl = $url;

        return $this;
    }

    public function setProductUid(string $productUid)
    {
        $this->productUid = $productUid;

        return $this;
    }

    public function setQuantity(int $quantity)
    {
        if ($quantity < 1) {
            throw new \Exception('Quantity must be at least 1!');
        }

        $this->quantity = $quantity;

        return $this;
    }

    public function setCurrency(string $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    public function setOrderData(string $orderId, string $customerId, string $currency = 'USD')
    {
        $this->orderId = $orderId;
        $this->customerId = $customerId;
        $this->setCurrency($currency);

        return $this;
    }

    public function setShipmentMethod(string $shipmentMethod = 'normal')
    {
        $this->shipmentMethod = $shipmentMethod == 'normal'? 'normal': 'express';

        return $this;
    }

    public function setShippingAddress($address)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefined([
            'companyName',
            'firstName',                  // required
            'lastName',                   // required
            'addressLine1',               // required
            'addressLine2',
            'state',
            'city',                       // required
            'postCode',                   // required
            'country',                    // required
            'email',                      // required
            'phone',
        ])->setRequired([
            'firstName',
            'lastName',
            'addressLine1',
            'city',
            'postCode',
            'country',
            'email'
        ]);

        $this->shippingAddress = $resolver->resolve($address);

        return $this;
    }
        
    public function setItems(array $items)
    {
        $this->items = [];
        foreach($items as $item) {
            $this->addItem($item);
        }

        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function addItem(array $item)
    {
        static $resolver = null;

        if (is_null($resolver)) {
            $resolver = new OptionsResolver;

            $resolver->setRequired([
                'itemReferenceId',
                'productUid',
                'fileUrl',
                'quantity',
            ]);
        }

        $this->items[] = $resolver->resolve($item);

        return $this;
    }

    public function deleteItem(string $itemId)
    {
        $this->items = array_filter($this->items, fn($item) => $item['itemReferenceId'] !== $itemId);
    
        return $this;
    }

    public function createOrder()
    {
        if (is_null($this->orderId)) {
            throw new \Exception('Order ID is not set!');
        }

        if (is_null($this->customerId)) {
            throw new \Exception('Customer ID is not set!');
        }
        
        $data = [
            "orderReferenceId" => $this->orderId,
            "customerReferenceId" => $this->customerId,
            "currency" => $this->currency,
            "shipmentMethodUid" => $this->shipmentMethod,
            "shippingAddress" => $this->shippingAddress,
            "items" => $this->items
        ];

        $response = $this->httpClient
                        ->post('https://order.gelatoapis.com/v3/orders', $data);

        return $this->httpClient->getResponse();
    }

    public function getOrder(string $orderId)
    {
        $this->httpClient
            ->get('https://order.gelatoapis.com/v3/orders/' . $orderId);

        return $this->httpClient->getResponse();
    }

    public function getShippingAddress(string $orderId)
    {
        $this->httpClient
            ->get('https://order.gelatoapis.com/v3/orders/' . $orderId . '/shipping-address');

        return $this->httpClient->getResponse();
    }

    public function getResponse()
    {
        return $this->httpClient->getResponse();
    }
}
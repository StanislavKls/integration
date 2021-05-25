<?php

namespace App\Http\Controllers;

use App\Models\UDS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;


class UDSController extends Controller
{
    public function upload(Request $request)
    {
        $data = $request->json()->all();
        $this->saveLog($data);
        $this->saveCustomer($data['customer']['id']);

        if ($data['delivery']['type'] === 'PICKUP') {
            $this->saveShop($data['delivery']['branch']);
        }
        $this->saveItems($data['items']);

        $order['id']                      = $data['id'];
        $order['date_created']            = $data['dateCreated'];
        $order['points']                  = $data['points'];
        $order['certificate_points']      = $data['certificatePoints'];
        $order['cash']                    = $data['cash'];
        $order['total']                   = $data['total'];
        $order['comment']                 = $data['comment'];
        $order['customer_id']             = $data['customer']['id'];
        $order['shop_id']                 = $data['delivery']['branch']['id'];
        $order['delivery_address']        = $data['delivery']['address'];
        $order['delivery_receiver_name']  = $data['delivery']['receiverName'];
        $order['delivery_receiver_phone'] = $data['delivery']['receiverPhone'];
        $order['delivery_user_comment']   = $data['delivery']['userComment'];
        $this->saveOrder($order);

        $idAndPriceOfItems = collect($data['items'])->map(fn($item, $key) => ['id' => $item['id'], 'price' => $item['price']])->toArray();
        $this->saveOrderItems($data['id'], $idAndPriceOfItems);
        return response()->json('', 200);
    }
    public function saveCustomer(int $id)
    {
        $data = $this->getClientInformation($id);

        if (!Customer::find($id)) {
            $customer = new Customer();
            $customer->fill($data);
            $customer->save();
            return true;
        }
        $customer = Customer::find($id);
        $customer->fill($data);
        $customer->save();

        return true;
    }
    public function saveItems(array $data): bool
    {
        foreach ($data as $item) {
            $goods['id']           = $item['id'];
            $goods['name']         = $item['name'];
            $goods['price']        = $item['price'];
            $goods['qty']          = $item['qty'];
            $goods['type']         = $item['id'];
            $goods['variant_name'] = $item['variantName'];
            $goods['external_id']  = $item['externalId'];

            if (!Item::find($item['id'])) {
                $goodsModel = new Item();
                $goodsModel->fill($goods);
                $goodsModel->save();
            } else {
                $goodsModel = Item::find($item['id']);
                $goodsModel->fill($goods);
                $goodsModel->save();
            }
        }
        return true;
    }
    public function saveLog(array $data)
    {
        $contents = print_r($data, 1);
        Storage::append('uds_log.txt', $contents);
        return true;
    }
    public function getClientInformation(int $id): array
    {
        $date      = new \DateTime();
        $companyId = env('COMPANY_ID');;
        $apikey    = env('APIKEY');
        $uuid      = uniqid();

        $response = Http::withHeaders([
                                       'Accept' => 'application/json',
                                       'X-Origin-Request-Id' => $uuid,
                                       'X-Timestamp' => $date->format(\DateTime::ATOM)
                                    ])
        ->withBasicAuth($companyId, $apikey)
        ->get("https://api.uds.app/partner/v2/customers/{$id}");

        $data = $response->json();

        return [
            'id'                    => $data['participant']['id'],
            'display_name'          => $data['displayName'],
            'birth_date'            => $data['birthDate'],
            'phone'                 => $data['phone'],
            'points'                => $data['participant']['points'],
            'discount_rate'         => $data['participant']['discountRate'],
            'cashback_rate'         => $data['participant']['cashbackRate'],
            'membership_tier_name'  => $data['participant']['membershipTier']['name'],
            'date_created'          => $data['participant']['dateCreated'],
            'last_transaction_time' => $data['participant']['lastTransactionTime'],
            'uid'                   => $data['uid'],
            ];
    }
    public function saveShop(array $data): bool
    {
        if (!Shop::find($data['id'])) {
            $shop = new Shop();
            $shop->id = $data['id'];
            $shop->display_name = $data['displayName'];
            $shop->save();
            return true;
        }
        return false;
    }
    public function saveOrder(array $data): bool
    {
        $order = new Order();
        $order->fill($data);
        $order->save();
        return true;
    }
    public function saveOrderItems(int $id, array $data): bool
    {
        $order = Order::find($id);
        foreach ($data as $item) {
            $order->items()->attach($item['id'], ['price' => $item['price']]);
        }
        return true;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\BitrixController;

class UDSController extends Controller
{
    public function upload(Request $request)
    {
        $data = $request->json()->all();
        $this->saveLog($data);
        $this->saveCustomer($data['customer']['id']);

        if ($data['delivery']['type'] === 'PICKUP') {
            $this->saveShop($data['delivery']['branch']);
            $order['shop_id'] = $data['delivery']['branch']['id'];
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
        $order['delivery_type']           = $data['delivery']['type'];
        $order['delivery_address']        = $data['delivery']['address'];
        $order['delivery_receiver_name']  = $data['delivery']['receiverName'];
        $order['delivery_receiver_phone'] = $data['delivery']['receiverPhone'];
        $order['delivery_user_comment']   = $data['delivery']['userComment'];
        $order['uploaded_to_bitrix']      = false;
        $this->saveOrder($order);
        
        $idAndPriceOfItems = array_map(fn($item) => ['id' => $item['id'], 'price' => $item['price'], 'qty' => $item['qty']], $data['items']);
        $this->saveOrderItems($data['id'], $idAndPriceOfItems);

        $bitrixController = new BitrixController();
        $bitrixController->upload($data['id']);
        return response()->json('', 200);
    }
    /**
     * Сохраняет продавца в БД.
     *
     * @param  int  $id
     * @return bool
     */
    private function saveCustomer(int $id)
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
    /**
     * Сохраняет товар в БД.
     *
     * @param  array $data
     * @return bool
     */
    private function saveItems(array $data)
    {
        foreach ($data as $item) {
            $goods['id']           = $item['id'];
            $goods['name']         = $item['name'];
            $goods['price']        = $item['price'];
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
    /**
     * Сохраняет лог в txt файл.
     *
     * @param  array $data
     * @return bool
     */
    private function saveLog(array $data)
    {
        $contents = print_r($data, 1);
        Storage::append('uds_log.txt', $contents);
        return true;
    }
    /**
     * Получение полной информации о клиенете через API UDS
     *
     * @param  int $id
     * @return array
     */
    private function getClientInformation(int $id)
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
    /**
     * Сохраняет магазин вывоза в БД.
     *
     * @param  array $data
     * @return bool
     */
    private function saveShop(array $data)
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
    /**
     * Сохраняет заказ в БД.
     *
     * @param  array $data
     * @return bool
     */
    private function saveOrder(array $data): bool
    {
        $order = new Order();
        $order->fill($data);
        $order->save();
        return true;
    }
    /**
     * Сохраняет товар заказа в БД
     * @param  int $id
     * @param  array $data
     * @return bool
     */
    private function saveOrderItems(int $id, array $items): bool
    {
        $order = Order::find($id);
        foreach ($items as $item) {
            $order->items()->attach($item['id'], ['price' => $item['price'], 'qty' => $item['qty']]);
        }
        return true;
    }
}

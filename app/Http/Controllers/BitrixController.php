<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Bitrix\Crest;
use App\Models\Order;
use Carbon\Carbon;

class BitrixController extends Controller
{
    private const ACCOUNTABLE_OF_ORDER   = 28; //ID ответственного за сделку (Бондаренко)
    private const ACCOUNTABLE_OF_CONTACT = 28; //ID ответственного за контакт (Бондаренко)

    public function upload(int $id)
    {
        $order = Order::find($id);
        $data  = $this->makeDataForUpload($order);

        $contactID      = $this->saveContact($order->customer);
        $orderBitrixID  = $this->saveOrder($data);
        $setItems       = $this->setItemsInOrder($order, $orderBitrixID);
        $setContact     = $this->setContact($orderBitrixID, $contactID);
        if (isset($orderBitrixID) && $setItems && $setContact) {
            $order->uploaded_to_bitrix = true;
            $order->save();
            flash('Заказ успешно создан')->success();
        } else {
            flash('Не удалось полностью загрузить заказ')->error();
        }
        return redirect()->route('orders.index');
    }
    /**
     * Возвращает массив данных для загрузки заказа в Bitrix
     *
     * @param  Order $order
     * @return array
     */
    private function makeDataForUpload(Order $order): array
    {
        $date                         = Carbon::create($order->date_created)->add(5, 'hour');
        $data['CATEGORY_ID']          = 28;
        $data['TITLE']                = "Заказ №{$order->id} от {$date}";
        $data['TYPE_ID']              = 7;
        $data['SOURCE_ID']            = 'UDS';
        if ($order->delivery_type === 'PICKUP') {
            $data['UF_CRM_UDS_DEL_TYPE'] = '994';
            $data['UF_CRM_1622107186']   = $order->shop->bitrix_id;
        } else {
            $data['UF_CRM_UDS_DEL_TYPE'] = '996';
            $data['UF_CRM_1622200882']   = $order->delivery_address;
        }
        $data['BEGINDATE']            = $date;
        $data['OPENED']               = 'Да';
        $data['ASSIGNED_BY_ID']       = self::ACCOUNTABLE_OF_ORDER;
        $data['COMMENTS']             = $order->comment;
        $data['UF_CRM_1567053944']    = $order->points;
        $data['UF_CRM_1622107491']    = $order->certificate_points;
        $data['UF_CRM_UDS_ORDER_ID']  = $order->id;

        return $data;
    }
    /**
     * Возвращает bool результат добавления товара в заказ
     *
     * @param  Order $order
     * @param int $id
     * @return bool
     */
    private function setItemsInOrder(Order $order, int $id): bool
    {
        $items = array_map(fn($item) => [
                                        'PRODUCT_ID' => 0,
                                        'PRODUCT_NAME' => "{$item['name']} {$item['variant_name']}",
                                        'PRICE' => $item['pivot']['price'],
                                        'QUANTITY' => $item['pivot']['qty'],
                                        ],
                                        $order->items->toArray());

        $result = CRest::call('crm.deal.productrows.set', ['id' => $id, 'rows' => $items]);
        return $result['result'];
    }
    /**
     * Возвращает ID контакта Bitrix
     *
     * @param  Customer $customer
     * @return int
     */
    private function saveContact(Customer $customer)
    {
        $bitrixID = $this->getContact($customer->id);
        if ($bitrixID) {
            return $bitrixID;
        }

        [$firstName, $lastName] = explode(' ', $customer->display_name);

        $data = [
            'NAME'              => $firstName,
            'LAST_NAME'         => $lastName,
            'BIRTHDATE'         => $customer->birth_date,
            'PHONE'             => [['VALUE' => $customer->phone, 'VALUE_TYPE' => 'MOBILE']], 
            'UF_CRM_1622180359' => $customer->points,                  //баллы UDS
            'UF_CRM_ID_UDS'     => $customer->id,                      //id UDS
            'UF_CRM_1622178324' => $customer->discount_rate,           //Скидка клиента UDS (%)
            'UF_CRM_1622178386' => $customer->cashback_rate,           //Кэшбек клиента UDS (%)
            'UF_CRM_1622178419' => $customer->membership_tier_name,    //Статус клиента UDS
            'UF_CRM_1622178451' => $customer->date_created,            //Дата подписки в UDS
            'UF_CRM_1622178490' => $customer->last_transaction_time,    //Дата последней транзакции UDS
            'ASSIGNED_BY_ID'    => self::ACCOUNTABLE_OF_CONTACT
        ];

        $result = CRest::call('crm.contact.add', ['fields' => $data]);
        return $result['result'];
    }
    /**
     * Возвращает ID контакта Bitrix
     *
     * @param  Customer $customer
     * @return void
     */
    private function getContact(int $udsID)
    {
        $result = CRest::call('crm.contact.list', ['filter' => ['UF_CRM_ID_UDS' => $udsID],
                                            'select' => ['ID']
                                            ]);

        if (isset($result['result']['0']['ID'])) {
            return $result['result']['0']['ID'];
        }
        return false;
    }
    /**
     * Возвращает ID контакта заказа
     *
     * @param  array $data
     * @return int
     */
    private function saveOrder(array $data)
    {
        $orderBitrix = CRest::call('crm.deal.add', ['fields' => $data]);
        return $orderBitrix['result'];
    }
    /**
     * Возвращает bool результат установки контакта заказу
     *
     * @param  int $order
     * @param  int $id
     * @return bool
     */
    private function setContact($orderID, $contactID)
    {
        $result = CRest::call('crm.deal.contact.add', ['id' => $orderID, 'fields' => ['CONTACT_ID' => $contactID]]);
        return $result['result'];
    }
}

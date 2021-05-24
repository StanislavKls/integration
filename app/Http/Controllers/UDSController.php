<?php

namespace App\Http\Controllers;

use App\Models\UDS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class UDSController extends Controller
{
    public function upload(Request $request)
    {
        $xRequestId = $request->header('X-Request-Id');
        $xTimestamp = $request->header('X-Timestamp');
        $xSignature = $request->header('X-Signature');
        //$data = json_encode($request->json()->all(), true);
        $data = $request->json()->all();
        $this->saveLog($data);
        $this->saveCustomer($data['customer']['id']);
        //$this->saveItems($data['items']);
        //$this->getClientInformation();



        //print_r($data['customer']);
        //return response()->json($data['items'], 201);
    }
    public function saveCustomer($id)
    {
        $data = $this->getClientInformation($id);

        if (!Customer::find($id)) {
            $customer = new Customer();
            $customer->fill($data);
            $customer->save();
            return response('', 200);
        }
        $customer = Customer::find($id);
        $customer->fill($data);
        $customer->save();

        return response('', 200);
    }
    public function saveItems($data)
    {
        $items = new Item();
        $ids = collect(array_map(fn($item) => $item['id'], $data));
        $diff = $ids->diff($items->pluck('item_id'));

        $filteredItems = collect($data)->filter(function ($item) use ($diff) {
            return $diff->search($item['id']);
        })->toArray();

        foreach ($filteredItems as $item) {
            $goods['item_id']      = $item['id'];
            $goods['name']         = $item['name'];
            $goods['price']        = $item['price'];
            $goods['qty']          = $item['qty'];
            $goods['sku']          = $item['sku'];
            $goods['type']         = $item['id'];
            $goods['variant_name'] = $item['variantName'];
            $goods['external_id']  = $item['externalId'];
            $goodsModel = new Item();
            $goodsModel->fill($goods);
            $goodsModel->save();
        }

        return true;
    }
    public function saveLog($data)
    {
        $contents = print_r($data, 1);
        Storage::append('uds_log.txt', $contents);
        return true;
    }
    public function getClientInformation(int $id): array
    {
        $date = new \DateTime();
        $companyId = 549756039533;
        $apikey = 'ZDEwMGMxNjgtODU2MC00ZTE5LWEyZmUtMjY2M2RhZmQ0YzNh';
        $uuid = uniqid();

        $response = Http::withHeaders([
                                       'Accept' => 'application/json',
                                       'X-Origin-Request-Id' => $uuid,
                                       'X-Timestamp' => $date->format(\DateTime::ATOM)
                                    ])
        ->withBasicAuth($companyId, $apikey)
        ->get("https://api.uds.app/partner/v2/customers/{$id}");

        $data = $response->json();

        return [
            'customer_id'           => $data['participant']['id'],
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
}

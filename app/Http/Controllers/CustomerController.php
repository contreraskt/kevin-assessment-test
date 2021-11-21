<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Validator;

class CustomerController extends Controller
{
    //

    /*eligibility*/
    public function eligibility(Customer $customer){
        $customerId =  $customer->id;
        $customer = Customer::find($customerId);

        /* check purchases within 30 days */
        $dateFrom = date('Y-m-d H:i:s',strtotime('-30 days'));
        $dateTo = date('Y-m-d H:i:s');

        $purchases = $customer->purchases()
                    ->whereBetween('transaction_at', [$dateFrom, $dateTo])
                    ->groupBy('id')
                    ->havingRaw('SUM(total_spent) > 100.00')
                    ->get();

        if ($purchases != null || count($purchases) > 0 ){

            /* check for claimed voucher*/
            $checkVoucher = Voucher::where('customer_id',$customerId)->first();
            if ($checkVoucher != null  ){
                return response()->json([
                    'message' => 'Not eligible'
                ]);
            }else{
                try {
                   return DB::transaction(function () use ($customerId) {
                        /*lock voucher*/
                        $voucher = Voucher::where('status','Free')
                            ->first();
                        $voucher->update([
                                'customer_id'   => $customerId,
                                'locked_at'     => date('Y-m-d H:i:s'),
                                'status'        => 'Pending'
                                ]);

                        return response()->json([
                                'message' => 'Voucher succesfully assigned'
                        ]);

                    });
                }
                catch (\Throwable $e) {
                    return $e->getMessage();
                }


            }
        }
    }

    /*Photo submission*/

    public function photosubmission (Request $request){

        /* validate request */
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|int|max:20',
            'image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages(),
            ]);
        }

        $customerId =  $request->customer_id;
        $checkVoucher = Voucher::where([
                            'customer_id'=>$customerId,
                            'status' => 'Pending'
                        ])
                        ->first();
        if ($checkVoucher != null){
            $locked_at = $checkVoucher->locked_at;

            /* check locked voucher if 10 mins  */
            $dateFrom= date('Y-m-d H:i:s',strtotime('-10 minutes'));
            if (strtotime($dateFrom) <= strtotime($locked_at)){
                try {
                    return DB::transaction(function () use ($checkVoucher) {
                        /*lock voucher*/
                        $checkVoucher->update([
                            'status'        => 'Locked',
                            'updated_at'     => date('Y-m-d H:i:s'),
                        ]);
                        $getVoucher = $checkVoucher->first();
                        return response()->json([
                            'message' => 'Voucher succesfully assigned',
                            'voucher_code' => $getVoucher->voucher_code
                        ]);
                    });
                }
                catch (\Throwable $e) {
                    return $e->getMessage();
                }
            }else{
                /*expired*/
                try {
                    return DB::transaction(function () use ($checkVoucher) {
                        /*lock voucher*/
                        $checkVoucher->update([
                            'status'        => 'Free',
                            'customer_id' => null,
                            'locked_at' => null,
                            'status' => 'Free',
                            'updated_at'     => date('Y-m-d H:i:s'),
                        ]);
                        return response()->json([
                            'message' => 'Voucher is now free',

                        ]);
                    });
                }
                catch (\Throwable $e) {
                    return $e->getMessage();
                }
            }
        }else{
            return response()->json([
                'message' => 'No record found',

            ]);
        }
    }
}

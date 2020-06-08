<?php

namespace App\Http\Controllers;
use App\Notifications\ConfirmMail;
use App\Register;
use Illuminate\Http\Request;
use DB;

class PaymentController extends Controller
{
    public function index(){
    	return view('front-end.payment.payment');
    }



    public function payment(Request $request){
         $id=$request->user_id;
         
    	$tax_id =DB::table('payments')->where('user_id',$id)->value('tax_id'); 
    	$ch = curl_init();
  $options = array( 'Merchant_Username'=>'totalac', 'Merchant_password'=>'9m9KYxS9juy6GbmW');
  $uniq_transaction_key =$tax_id;//Given By Shurjumukhi Limited
  $amount=$request->amount;
  $userId=$request->user_id;
  $clientIP =$request->ip();
 
  $xml_data = 'spdata=<?xml version="1.0" encoding="utf-8"?>
  <shurjoPay><merchantName>'.$options['Merchant_Username'].'</merchantName>
  <merchantPass>'.$options['Merchant_password'].'</merchantPass>
  <userIP>'.$clientIP.'</userIP>
  <uniqID>'.$uniq_transaction_key.'</uniqID>
  <totalAmount>'.$amount.'</totalAmount>
  <paymentOption>shurjopay</paymentOption>
  <returnURL>https://events.totalactivesports.com/get/payment/success</returnURL></shurjoPay>';
  
  //dd($uniq_transaction_key);
  $url = "https://shurjopay.com/sp-data.php";
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_POST, 1);               
  curl_setopt($ch,CURLOPT_POSTFIELDS,$xml_data);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $response = curl_exec($ch);

  curl_close ($ch);
  print_r($response);

    	
    }

    public function paymentSuccess(Request $request)
{
  $response_encrypted=$request->spdata;
  $fp = fopen('return.txt', 'a');
  $e = $response_encrypted."\n";
  fwrite($fp,$response_encrypted);
  fclose($fp);
  $response_decrypted = file_get_contents("https://shurjopay.com/merchant/decrypt.php?data=".$response_encrypted);
  $data= simplexml_load_string($response_decrypted) or die("Error: Cannot create object");

  $fp = fopen('return.txt', 'a');
  $d = $response_decrypted."\n";
  fwrite($fp,$response_decrypted);
  fclose($fp);
  
    $tx_id = $data->txID;
    $bank_tx_id = $data->bankTxID;
    $bank_status = $data->bankTxStatus;
    $sp_code = $data->spCode;
    $sp_code_des = $data->spCodeDes;
    $sp_payment_option = $data->paymentOption;
    

     switch($sp_code) {
       case '000':
         $res = array('status'=>true,'msg'=>'success');
         break;
       case '001':
        $res = array('status'=>false,'msg'=>'Transaction failed');
         break;            
      default:
         $res = array('status'=>false,'msg'=>'Unknow Error Occured.');
        break;            
     }
     if($res['status']) {
     	DB::table('payments')->where('tax_id',$tx_id)->update([
         'bank_tax_id'=>$bank_tx_id,
         'bank_tax_status'=>$bank_status,
         'sp_code'=>$sp_code,
         'sp_code_des'=>$sp_code_des,
         'sp_payment'=>$sp_payment_option,
         'status'=>1,
     	]);
      $id=DB::table('payments')->where('tax_id',$tx_id)->value('user_id');
     $code = (string)$tx_id;
      
     $user=Register::where('id',$id)->first();
     $name=$user->fname;
     $lname=$user->lname;
     $user->notify(new ConfirmMail($code,$name,$lname));
     session()->flash('message','Payment Successfull');
     
     
     return view('front-end.register.success')->with(['tax_id'=>$code]);          
    
                
            } else {
               session()->flash('message','Payment Failed');
                  return redirect()->route('error'); 
             }

}
}

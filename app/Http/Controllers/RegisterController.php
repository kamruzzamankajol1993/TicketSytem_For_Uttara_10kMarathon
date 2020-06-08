<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use DB;
use App\Register;
use App\Payment;
use App\User;
use App\Notifications\ConfirmMail;
class RegisterController extends Controller
{
     public function index(){
     
       $code=3;
       $totalSeat=DB::table('eventnames')->where('id',$code)->value('seat');
       //$booked=DB::table('registers')->where('event_cat_id',$code)->count();
       $booked=DB::table('payments')->where('status',1)->count();
       $avaivle=$totalSeat-$booked;
   
     	$events = DB::table('eventnames')->select('event_name','seat','id')->get();

    	return view('front-end.register.register',['events'=>$events,'avaivle'=>$avaivle]);
    }

     public function review(){
    	return view('front-end.register.review');
    }

     public function success(){
    	return view('front-end.register.success');
    }

    public function error(){
        return view('front-end.register.error');
    }

    public function store(Request $request){

        $this->validate($request,[
            'passport' => 'required|unique:registers',
            
        ]);
        $reg = new Register();
        $reg->fname = $request->fname;
        $reg->lname = $request->lname;
        $reg->event_cat_id = $request->event_cat_id;
        $reg->birthday = $request->birthday;
        $reg->city = $request->city;
        $reg->country = $request->country;
        $reg->email = $request->email;
        $reg->phone = $request->phone;
        $reg->gender = $request->gender;
        $reg->size = $request->size;
        $reg->address = $request->address;
        $reg->ename = $request->ename;
        $reg->enumber = $request->enumber;
        $reg->slink = $request->slink;
        $reg->passport = $request->passport;
        
        $payment= new Payment;
        $ip=$request->ip();
        
        try{
            $reg->save();
            $reg_id =$reg->id;
            
            
            $payment->tax_id='TAS-'.rand('10000000','99999999');
            $payment->user_id=$reg_id;
            $payment->ip=$ip;
            $payment->save();
            $info=DB::table('registers')->where('id',$reg_id)->first();
            return view('front-end.register.review',['info'=>$info]);
        }catch (\Exception $e) {

            session()->flash('error-message',$e->getMessage());
            return redirect()->back();
        }
        

        

    }


    public function edit($id)
    {
         $info = Register::find($id);
         
         return view('front-end.register.edit',['info' => $info]);
    }

    public function update(Request $request){


        $reg = Register::find($request->id);
        $reg->fname = $request->fname;
        $reg->lname = $request->lname;
        $reg->event_cat_id = $request->event_cat_id;
        $reg->birthday = $request->birthday;
        $reg->city = $request->city;
        $reg->country = $request->country;
        $reg->email = $request->email;
        $reg->phone = $request->phone;
        $reg->gender = $request->gender;
        $reg->size = $request->size;
        $reg->address = $request->address;
        $reg->ename = $request->ename;
        $reg->enumber = $request->enumber;
        $reg->slink = $request->slink;
        $reg->passport = $request->passport;
        

        try{
            $reg->save();
            $reg_id =$reg->id;
            
            $info=DB::table('registers')->where('id',$reg_id)->first();
            return view('front-end.register.review',['info'=>$info]);
        }catch (\Exception $e) {

            session()->flash('error-message',$e->getMessage());
            return redirect()->back();
        }


    }

    //get the avaible seat
    public function getSeat(Request $request)
    {
       $code=$request->id;
       $totalSeat=DB::table('eventnames')->where('id',$code)->value('seat');
       $booked=DB::table('registers')->where('event_cat_id',$code)->count();
       $avaivle=$totalSeat-$booked;
       return $avaivle;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\HideNotification;
use App\Models\ReadNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\NotificationRequest;
use App\Http\Requests\NotificationCreateRequest;

class NotificationController extends Controller
{

    protected $model;

    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    //index
    public function index(){
        $data = $this->model
        ->where(function ($query) {
            $query->whereNull('to_user_id')
                  ->orWhere('to_user_id', Auth::user()->id);
        })
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(true))
                  ->from('hide_notifications')
                  ->whereRaw('hide_notifications.notification_id = notifications.id')
                  ->where('hide_notifications.user_id',Auth::user()->id);
        })
        ->withCount(['read_notifications as is_read' => function($query){
            $query->where('read_notifications.user_id',Auth::user()->id);
        }])
        ->paginate(20);
        return sendResponse($data,200);
    }

    //create notifications
    public function store(NotificationRequest $request){
        //user authorization
        if(Gate::denies('auth-noti')){
            return sendResponse(null,401,'Not allowed');
        }

        $notification = $this->model->create($this->changeNotificationDataToArray($request));
        return sendResponse($notification,200,'Notification create success');
    }

    public function update(NotificationRequest $request){
        //user authorization
        if(Gate::denies('auth-noti')){
            return sendResponse(null,401,'Not allowed');
        }

        //check notification
        $notification = $this->model->find($request->id);
        if(!$notification){
            return sendResponse(null,404,'Notification not found');
        }

        //update data
        $notification->update($this->changeNotificationDataToArray($request));
        return sendResponse($notification,200,'Notification update success');
    }

    //notification show
    public function show($id){
        $notification = $this->model->find($id);
        return sendResponse($notification,200);
    }

    //read notification
    public function read(Request $request){
        //check noti
        $notification = $this->model->find($request->notification_id);
        if(!$notification){
            return sendResponse(null,404,'Notification not found');
        }

        //check read
        $check = ReadNotification::where('notifications_id',$notification->id)->where('user_id',Auth::user()->id);
        if($check){
            return sendResponse(null,405,'You already read this notification');
        }

        $data = ReadNotification::create([
            'user_id' => Auth::user()->id,
            'notification_id' => $request->notification_id
        ]);
        return sendResponse($data,200,'You read this notification');
    }

    //hide notification
    public function hide(Request $request){
        //check noti
        $notification = $this->model->find($request->notification_id);
        if(!$notification){
            return sendResponse(null,404,'Notification not found');
        }

        $data = HideNotification::create([
            'user_id' => Auth::user()->id,
            'notification_id' => $request->notification_id
        ]);
        return sendResponse($data,200,'You hide this notification');
    }

    //delete noti
    public function destroy(Request $request){
        //user authorization
        if(Gate::denies('auth-noti')){
            return sendResponse(null,401,'Not allowed');
        }



        //check noti
        $notification = $this->model->find($request->notification_id);
        if(!$notification){
            return sendResponse(null,200,'You already delete this notification');
        }
        return sendResponse(null,200,'Notification delete success');
    }

    //change notification create data to array
    private function changeNotificationDataToArray($request){
        return [
            'title' => $request->title,
            'description' => $request->description,
            'to_user_id' => $request->to_user_id ? $request->to_user_id : null
        ];
    }

}

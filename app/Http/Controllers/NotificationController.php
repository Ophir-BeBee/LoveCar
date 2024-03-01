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
        return response()->json([
            'notifications' => $this->model
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
            ->with('read_notifications:id,user_id,notification_id')
            ->paginate(20),
            'status' => 200
        ]);
    }

    //create notifications
    public function store(NotificationRequest $request){
        //user authorization
        if(Gate::denies('auth-noti')){
            return response()->json([
                'message' => 'Not allowed',
                'status' => 401
            ]);
        }

        return response()->json([
            'notification' => $this->model->create($this->changeNotificationDataToArray($request)),
            'message' => 'Notification create success',
            'status' => 200
        ]);
    }

    public function update(NotificationRequest $request){
        //user authorization
        if(Gate::denies('auth-noti')){
            return response()->json([
                'message' => 'Not allowed',
                'status' => 401
            ]);
        }

        //check notification
        $notification = $this->model->find($request->id);
        if(!$notification){
            return response()->json([
                'message' => 'Notification not found',
                'status' => 404
            ]);
        }

        //update data
        $notification->update($this->changeNotificationDataToArray($request));
        return response()->json([
            'notification' => $notification,
            'message' => 'Notification update success',
            'status' => 200
        ]);
    }

    //notification show
    public function show(Request $request){
        return response()->json([
            'notification' => $this->model->find($request->id),
            'status' => 200
        ]);
    }

    //read notification
    public function read(Request $request){
        //check noti
        $notification = $this->model->find($request->notification_id);
        if(!$notification){
            return response()->json([
                'message' => 'Notification not found',
                'status' => 404
            ]);
        }

        ReadNotification::create([
            'user_id' => Auth::user()->id,
            'notification_id' => $request->notification_id
        ]);
        return response()->json([
            'message' => 'You read this notification',
            'status' => 200
        ]);
    }

    //hide notification
    public function hide(Request $request){
        //check noti
        $notification = $this->model->find($request->notification_id);
        if(!$notification){
            return response()->json([
                'message' => 'Notification not found',
                'status' => 404
            ]);
        }

        HideNotification::create([
            'user_id' => Auth::user()->id,
            'notification_id' => $request->notification_id
        ]);
        return response()->json([
            'message' => 'You hide this notification',
            'status' => 200
        ]);
    }

    //delete noti
    public function destroy(Request $request){
        //user authorization
        if(Gate::denies('auth-noti')){
            return response()->json([
                'message' => 'Not allowed',
                'status' => 401
            ]);
        }



        //check noti
        $notification = $this->model->find($request->notification_id);
        if(!$notification){
            return response()->json([
                'message' => 'You already delete this notification',
                'status' => 200
            ]);
        }
        return response()->json([
            'data' => null,
            'message' => 'Notification delete success',
            'status' => 200
        ]);
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

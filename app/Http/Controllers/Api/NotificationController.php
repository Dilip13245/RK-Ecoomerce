<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class NotificationController extends Controller
{
    public function listNotifications(Request $request)
    {
        try {
            $notifications = Notification::where('user_id', $request->user_id)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();

            $notificationsArray = $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at,
                ];
            })->toArray();

            return $this->toJsonEnc($notificationsArray, 'Notifications retrieved successfully', Config::get('constant.SUCCESS'));
        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.ERROR'));
        }
    }

    public function markAsRead(Request $request)
    {
        try {
            $notification = Notification::where('id', $request->notification_id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$notification) {
                return $this->toJsonEnc([], 'Notification not found', Config::get('constant.NOT_FOUND'));
            }

            $notification->is_read = 1;
            $notification->save();

            return $this->toJsonEnc(['status' => 'read'], 'Notification marked as read', Config::get('constant.SUCCESS'));
        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.ERROR'));
        }
    }
}
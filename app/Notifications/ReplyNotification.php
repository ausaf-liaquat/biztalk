<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReplyNotification extends Notification
{
    use Queueable;
    public $user;
    public $reply;
    public $video;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$reply,$video)
    {
        $this->user = $user;
        $this->reply = $reply;
        $this->video = $video;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'username'=>$this->user->username,
            'profile_img'=>asset('uploads/avtars/'.$this->user->profile_image),
            'reply'=>$this->reply->comment,
            'date'=>$this->reply->created_at,
            'video_id'=>$this->video->id,
            'message'=>$this->user->username.' replied on your comment'
        ];
    }
}

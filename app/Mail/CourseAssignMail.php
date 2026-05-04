<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourseAssignMail extends Mailable
{
    use Queueable, SerializesModels;
    var $attributes;

    /**
     * Create a new message instance.
     *
     * @return void
     */    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
  public function build()
{
    $this->attributes['logo'] =
        env('APP_ENV') == 'production'
        ? env('WEB_URL') . '/assets/images/logo.png'
        : public_path('logo.png');

    $this->attributes['web'] = env('WEB_URL') . '/login';

    return $this->to($this->attributes['email'])
        ->subject('EduEdgeProLMS - New Course Assigned')
        ->view('emails.course_assign')
        ->with('attributes', $this->attributes);
}
}

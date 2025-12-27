<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $password;
    public $matric_id;
    public $staff_id;
    public $course;
    public $year;
    public $role;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $email, $password, $role, $matric_id = null, $staff_id = null, $course = null, $year = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->matric_id = $matric_id;
        $this->staff_id = $staff_id;
        $this->course = $course;
        $this->year = $year;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->role === 'student' 
            ? 'Welcome to EduTrack - Student Account Created'
            : 'Welcome to EduTrack - Lecturer Account Created';
            
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user-registration',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $temporaryPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $temporaryPassword)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'EPSS PMS Password Reset',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildEmailHtml(),
        );
    }

    /**
     * Build the HTML email content
     */
    private function buildEmailHtml(): string
    {
        $user = $this->user;
        $temp_password = $this->temporaryPassword;
        $name = $user->name;
        $email = $user->email;

        // Try to embed EPSS logo
        $logoPath = public_path('images/epss_logo.png');
        $logo_cid = '';

        if (file_exists($logoPath)) {
            $this->attach($logoPath, [
                'as' => 'epss-logo.png',
                'mime' => 'image/png',
            ]);
            $logo_cid = 'epss-logo.png';
        }

        return "
        <div style='font-family: Arial, sans-serif; font-size: 16px; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #1C75BC; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;'>
                <h1 style='margin: 0; font-size: 24px;'>Ethiopian Pharmaceuticals Supply Service</h1>
                <h2 style='margin: 5px 0 0 0; font-size: 18px; font-weight: normal;'>EPSS Performance Management System</h2>
            </div>

            <div style='background-color: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px;'>
                <p>Dear <span style='font-size: 20px; font-weight: bold; color: #0056b3;'>{$name}</span>,</p>

                <p>Your EPSS PMS account password has been successfully reset.</p>

                <p style='margin-top: 30px; font-size: 16px;'><b>Here is your temporary password: <span style='font-size: 18px;'>👆</span></b></p>

                <div style='background: #f8f9fa; border: 2px solid #1C75BC; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0;'>
                    <span style='font-size: 28px; font-weight: bold; letter-spacing: 3px; color: #d9534f; font-family: monospace;'>
                        {$temp_password}
                    </span>
                </div>

                <p style='margin-top: 30px; font-size: 16px;'>
                    <strong>ማስታዎሻ:</strong> የመግቢያ መለያ ኮድ የተቀየረው Password (inbox) ላይ ካጣችሁት bin(Spam) ላይ ገብታችሁ ታገኙታላችሁ.
                </p>

                <div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p style='margin: 0; color: #155724;'>
                        <strong>Copy the Above temporary password then paste it the reset password then Login in to EPSS PMS</strong>
                    </p>
                </div>

                <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p style='margin: 0; color: #856404;'>
                        <strong>Important:</strong> Please login in immediately using this temporary password and change it to a secure password of your choice.
                    </p>
                </div>

                <div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p style='margin: 0; color: #721c24;'>
                        <strong>Security Notice:</strong> If you didn't request this password reset, please contact the HR/ICT department immediately.
                    </p>
                </div>

                <p style='border-top: 1px solid #dee2e6; padding-top: 20px; margin-top: 30px; color: #6c757d;'>
                    Best regards,<br>
                    <strong style='color: #1C75BC;'>Ethiopian Pharmaceuticals Supply Service</strong><br>
                    <span style='font-size: 14px;'>EPSS Performance Management System</span><br>
                    <span style='font-size: 12px;'>Contact: hr@epss.gov.et | ict@epss.gov.et</span>
                </p>

                " . ($logo_cid ? "<div style='text-align: center; margin-top: 30px;'>
                    <img src='cid:{$logo_cid}' alt='EPSS Logo' style='max-width: 200px; height: auto;'>
                </div>" : "") . "
            </div>

            <div style='text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6; font-size: 12px; color: #6c757d;'>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>";
    }

    /**
     * Build the plain text email content
     */
    private function buildEmailText(): string
    {
        $user = $this->user;
        $temp_password = $this->temporaryPassword;

        return "Dear {$user->name},

Your EPSS PMS account password has been successfully reset.

Here is your temporary password: {$temp_password}

ማስታዎሻ: የመግቢያ መለያ ኮድ የተቀየረው Password (inbox) ላይ ካጣችሁት bin(Spam) ላይ ገብታችሁ ታገኙታላችሁ.

Copy the above temporary password then paste it in the reset password field then login to EPSS PMS.

Important: Please login immediately using this temporary password and change it to a secure password of your choice.

Security Notice: If you didn't request this password reset, please contact the HR/ICT department immediately.

Best regards,
Ethiopian Pharmaceuticals Supply Service
EPSS Performance Management System
Contact: hr@epss.gov.et | ict@epss.gov.et

This is an automated message. Please do not reply to this email.";
    }


}

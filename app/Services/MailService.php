<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    /**
     * Send account creation email with temporary password
     */
    public function sendAccountEmail($toEmail, $toName, $tempPass, $role = null, $matricId = null, $staffId = null, $course = null, $year = null)
    {
        try {
            $mail = new PHPMailer(true);

            // SMTP Configuration - Use Gmail settings
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME', 'yourgmail@gmail.com');
            
            // Remove quotes from password if present
            $password = env('MAIL_PASSWORD', '');
            $password = trim($password, '"\'');
            $mail->Password   = $password;
            
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $mail->Port       = (int)env('MAIL_PORT', 587);
            
            // Enable verbose debug output for troubleshooting
            $mail->SMTPDebug = 0; // Set to 2 for detailed debugging
            $mail->Debugoutput = function($str, $level) { 
                \Log::info("PHPMailer Debug: $str"); 
            };

            // Sender and Recipient
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'yourgmail@gmail.com'), env('MAIL_FROM_NAME', 'EduTrack System Admin'));
            $mail->addAddress($toEmail, $toName);

            // Email Content
            $mail->isHTML(true);
            
            $subject = $role === 'student' 
                ? 'Welcome to EduTrack - Student Account Created'
                : ($role === 'lecturer' 
                    ? 'Welcome to EduTrack - Lecturer Account Created'
                    : 'Welcome to EduTrack - Account Created');
            
            $mail->Subject = $subject;
            
            // Build email body
            $body = $this->buildEmailBody($toName, $tempPass, $role, $matricId, $staffId, $course, $year, $toEmail);
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body); // Plain text version

            // Send email
            return $mail->send();
        } catch (Exception $e) {
            $errorInfo = isset($mail) ? $mail->ErrorInfo : $e->getMessage();
            \Log::error('PHPMailer Error: ' . $errorInfo);
            return false;
        }
    }

    /**
     * Build email body HTML
     */
    private function buildEmailBody($name, $password, $role, $matricId, $staffId, $course, $year, $emailAddress = null)
    {
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Welcome to EduTrack</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0; font-size: 28px;'>Welcome to EduTrack!</h1>
            </div>
            
            <div style='background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;'>
                <p style='font-size: 16px; margin-bottom: 20px;'>Dear $name,</p>
                
                <p style='font-size: 16px; margin-bottom: 20px;'>
                    Your account has been successfully registered in the EduTrack system.
                </p>
                
                <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin: 20px 0;'>
                    <h2 style='color: #667eea; margin-top: 0; font-size: 20px;'>Your Login Credentials</h2>
        ";

        if ($matricId) {
            $html .= "<p style='margin: 10px 0;'><strong>Matric ID:</strong> $matricId</p>";
        }

        if ($staffId) {
            $html .= "<p style='margin: 10px 0;'><strong>Staff ID:</strong> $staffId</p>";
        }

        // Determine email address if not provided
        if (!$emailAddress) {
            $emailAddress = $matricId ? strtolower($matricId) . '@student.edu' : '';
        }
        
        $html .= "
                    <p style='margin: 10px 0;'><strong>Email:</strong> $emailAddress</p>
                    <p style='margin: 10px 0;'><strong>Password:</strong> <span style='background: #f0f0f0; padding: 5px 10px; border-radius: 4px; font-family: monospace;'>$password</span></p>
        ";

        if ($course) {
            $html .= "<p style='margin: 10px 0;'><strong>Course:</strong> $course</p>";
        }

        if ($year) {
            $html .= "<p style='margin: 10px 0;'><strong>Year:</strong> $year</p>";
        }

        $html .= "
                </div>
                
                <div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p style='margin: 0; color: #856404;'>
                        <strong>⚠️ Important:</strong> Please change your password after your first login for security purposes.
                    </p>
                </div>
                
                <p style='font-size: 16px; margin-top: 30px;'>
                    You can now log in to the system using your email and the password provided above.
                </p>
                
                <p style='font-size: 16px; margin-top: 20px;'>
                    If you have any questions, please contact the administrator.
                </p>
                
                <p style='font-size: 16px; margin-top: 30px;'>
                    Best regards,<br>
                    <strong>EduTrack Administration Team</strong>
                </p>
            </div>
            
            <div style='text-align: center; margin-top: 20px; color: #999; font-size: 12px;'>
                <p>This is an automated email. Please do not reply to this message.</p>
            </div>
        </body>
        </html>
        ";

        return $html;
    }
}


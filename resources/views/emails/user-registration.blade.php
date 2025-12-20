<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to EduTrack</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">Welcome to EduTrack!</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;">
        <p style="font-size: 16px; margin-bottom: 20px;">Dear {{ $name }},</p>
        
        <p style="font-size: 16px; margin-bottom: 20px;">
            Your account has been successfully registered in the EduTrack system.
        </p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin: 20px 0;">
            <h2 style="color: #667eea; margin-top: 0; font-size: 20px;">Your Login Credentials</h2>
            
            @if(isset($matric_id))
            <p style="margin: 10px 0;"><strong>Matric ID:</strong> {{ $matric_id }}</p>
            @endif
            
            @if(isset($staff_id))
            <p style="margin: 10px 0;"><strong>Staff ID:</strong> {{ $staff_id }}</p>
            @endif
            
            <p style="margin: 10px 0;"><strong>Email:</strong> {{ $email }}</p>
            <p style="margin: 10px 0;"><strong>Password:</strong> <span style="background: #f0f0f0; padding: 5px 10px; border-radius: 4px; font-family: monospace;">{{ $password }}</span></p>
            
            @if(isset($course))
            <p style="margin: 10px 0;"><strong>Course:</strong> {{ $course }}</p>
            @endif
            
            @if(isset($year))
            <p style="margin: 10px 0;"><strong>Year:</strong> {{ $year }}</p>
            @endif
        </div>
        
        <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0; color: #856404;">
                <strong>⚠️ Important:</strong> Please change your password after your first login for security purposes.
            </p>
        </div>
        
        <p style="font-size: 16px; margin-top: 30px;">
            You can now log in to the system using your email and the password provided above.
        </p>
        
        <p style="font-size: 16px; margin-top: 20px;">
            If you have any questions, please contact the administrator.
        </p>
        
        <p style="font-size: 16px; margin-top: 30px;">
            Best regards,<br>
            <strong>EduTrack Administration Team</strong>
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #999; font-size: 12px;">
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>


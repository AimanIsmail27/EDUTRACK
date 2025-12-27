# Email Setup Instructions

## Problem: Emails Not Sending

The error log shows: `SMTP Error: Could not authenticate.`

## Solution: Update Your .env File

You need to configure your Gmail SMTP settings in the `.env` file. Here's what needs to be changed:

### Current Settings (WRONG):
```
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=yourgmail@gmail.com
MAIL_PASSWORD="skmtlfijkneilyal"
MAIL_FROM_ADDRESS="hello@example.com"
```

### Correct Settings for Gmail:
```
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-actual-gmail@gmail.com
MAIL_PASSWORD=your-16-digit-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-actual-gmail@gmail.com
MAIL_FROM_NAME="EduTrack System"
```

## Steps to Get Gmail App Password:

1. **Go to your Google Account**: https://myaccount.google.com/
2. **Click on Security** (left sidebar)
3. **Enable 2-Step Verification** (if not already enabled)
4. **Go to App Passwords**:
   - Scroll down to "2-Step Verification"
   - Click on "App passwords"
   - Select "Mail" and "Other (Custom name)"
   - Enter "EduTrack" as the name
   - Click "Generate"
5. **Copy the 16-character password** (no spaces)
6. **Update your .env file** with this password

### Example .env Configuration:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=john.doe@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=john.doe@gmail.com
MAIL_FROM_NAME="EduTrack System Admin"
```

**Important Notes:**
- The app password will have spaces, but you can remove them or keep them (with quotes)
- The `MAIL_USERNAME` and `MAIL_FROM_ADDRESS` should be the SAME Gmail address
- After updating `.env`, run: `php artisan config:clear`

## Testing Email

After updating the configuration, try registering a new user. Check the logs:
```bash
tail -f storage/logs/laravel.log
```

If you still see authentication errors, verify:
1. 2-Step Verification is enabled
2. App password was generated correctly
3. No extra spaces in the password
4. Gmail account is not locked or restricted


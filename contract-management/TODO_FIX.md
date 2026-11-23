# TODO: Fix Password Reset Functionality

## Tasks
- [x] Update routes/web.php to include password.reset and password.update routes
- [x] Modify AuthController sendResetLink to use Laravel's Password::sendResetLink for proper token-based reset
- [x] Password reset now sends secure reset links instead of temporary passwords
- [x] Test the password reset functionality (backend tests passed)
- [x] Add admin reset password functionality in AdminController
- [x] Add reset password route (admin.users.resetPassword)
- [x] Add reset password button in admin users index view
- [x] Reset all user passwords to default "epss@2018"
- [x] Test admin reset password functionality (working correctly)
- [x] Fix profile page null timestamp error (added null checks for created_at and updated_at)
- [x] Test profile page rendering (working correctly)
- [x] Fix null timestamp errors across all views (dashboard, contracts, admin users)
- [x] Added null checks to prevent "Call to a member function format() on null" errors

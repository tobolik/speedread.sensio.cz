@echo off
REM Fix Git permissions script v2 - Reset ACL and grant permissions
REM Run this as Administrator: Right-click CMD -> Run as Administrator

echo Fixing Git permissions (v2 - reset ACL)...
cd /d "c:\weby\speedread-sensio-cz"

echo Resetting ACL on .git directory...
icacls .git /reset /T
icacls .git /grant "%USERNAME%:(OI)(CI)F" /T
icacls .git /grant "BUILTIN\Administrators:(OI)(CI)F" /T
icacls .git /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T

echo.
echo Verifying permissions...
icacls .git | findstr /C:"DENY"

if %ERRORLEVEL% EQU 0 (
    echo WARNING: DENY permissions still exist!
) else (
    echo SUCCESS: No DENY permissions found!
)

echo.
echo Done! Try git commands now.
pause

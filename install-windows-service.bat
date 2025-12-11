@echo off
echo ========================================
echo BAME CRM Queue Worker Service Installer
echo ========================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator!
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

REM Configuration
set PHP_PATH=C:\xampp\php\php.exe
set PROJECT_PATH=C:\xampp\htdocs\bamecrm
set NSSM_PATH=C:\nssm\win64\nssm.exe

echo Checking paths...
if not exist "%PHP_PATH%" (
    echo ERROR: PHP not found at %PHP_PATH%
    echo Please update PHP_PATH in this script
    pause
    exit /b 1
)

if not exist "%PROJECT_PATH%" (
    echo ERROR: Project not found at %PROJECT_PATH%
    echo Please update PROJECT_PATH in this script
    pause
    exit /b 1
)

if not exist "%NSSM_PATH%" (
    echo ERROR: NSSM not found at %NSSM_PATH%
    echo.
    echo Please download NSSM from https://nssm.cc/download
    echo Extract to C:\nssm\
    pause
    exit /b 1
)

echo.
echo Installing BameCRMWorker service...
"%NSSM_PATH%" install BameCRMWorker "%PHP_PATH%" "%PROJECT_PATH%\artisan queue:work database --sleep=3 --tries=3 --max-time=3600"

echo Configuring service...
"%NSSM_PATH%" set BameCRMWorker AppDirectory "%PROJECT_PATH%"
"%NSSM_PATH%" set BameCRMWorker AppStdout "%PROJECT_PATH%\storage\logs\worker.log"
"%NSSM_PATH%" set BameCRMWorker AppStderr "%PROJECT_PATH%\storage\logs\worker-error.log"
"%NSSM_PATH%" set BameCRMWorker DisplayName "BAME CRM Queue Worker"
"%NSSM_PATH%" set BameCRMWorker Description "Processes background jobs for BAME CRM"
"%NSSM_PATH%" set BameCRMWorker Start SERVICE_AUTO_START

echo.
echo Starting service...
"%NSSM_PATH%" start BameCRMWorker

echo.
echo Checking status...
"%NSSM_PATH%" status BameCRMWorker

echo.
echo ========================================
echo Installation Complete!
echo ========================================
echo.
echo Service Name: BameCRMWorker
echo Status: Running
echo.
echo Useful commands:
echo   Start:   nssm start BameCRMWorker
echo   Stop:    nssm stop BameCRMWorker
echo   Restart: nssm restart BameCRMWorker
echo   Status:  nssm status BameCRMWorker
echo   Remove:  nssm remove BameCRMWorker confirm
echo.
echo Logs location:
echo   %PROJECT_PATH%\storage\logs\worker.log
echo.
pause

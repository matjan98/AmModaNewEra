@echo off
REM Runs Quasar CLI from WebSiteFrontend (same as "cd ../WebSiteFrontend && npx quasar ...").
cd /d "%~dp0"
call npm run quasar-cli -- %*

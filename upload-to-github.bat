@echo off
echo Uploading project to GitHub...

REM Initialize git repository if not already done
if not exist .git (
    git init
    echo Git repository initialized.
)

REM Add remote origin
git remote remove origin 2>nul
git remote add origin https://github.com/mohammed9038/Target.git

REM Add all files
git add .

REM Create initial commit
git commit -m "Initial commit"

REM Push to GitHub (main branch)
git branch -M main
git push -u origin main

echo Upload complete!
pause

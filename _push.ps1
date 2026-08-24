git rm --cached "_check_git.ps1" "_gs.txt" "pluck('email'" 2>&1 | Out-Null
Remove-Item -ErrorAction SilentlyContinue "_check_git.ps1","_gs.txt","_g.txt","pluck('email'"
git add -A
git commit -m "chore: eliminar archivos temporales de diagnostico" 2>&1 | Out-Null
git log --oneline -3
echo ---STATUS---
git status --short
echo ---REMOTE---
git remote -v
if (-not (git remote | Select-String origin)) {
    git remote add origin https://github.com/oscarniro20-boop/turismo-huila
}
git branch -M main
echo ---PUSH---
git push -u origin main 2>&1

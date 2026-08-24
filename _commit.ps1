Remove-Item -ErrorAction SilentlyContinue '_push.ps1','_fix_portada.ps1'
git add -A
git status --short
git commit -m "fix: signo coords Tatacoa (+3.2258), clustering en /mapa, vista guias renovada"
git log --oneline -2

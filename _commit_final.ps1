Remove-Item -ErrorAction SilentlyContinue "_rf_out.txt","_rec.ps1","_rec2.ps1","_rec_final.ps1","_inv.txt"
git add -A
echo "===CACHED_STAT==="
git diff --cached --stat | Select-Object -Last 5
git diff --cached --check; echo "CHECK_EXIT=$LASTEXITCODE"
git commit -m "chore: consolidate performance and security hardening"
echo "===FINAL==="
git status --short
git log -1 --oneline

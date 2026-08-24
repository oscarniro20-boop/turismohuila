git status --short > _gs.txt 2>&1
git log --oneline | Measure-Object -Line | ForEach-Object { "COMMITS: $($_.Lines)" } >> _gs.txt
Get-Content _gs.txt

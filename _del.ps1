"REFS:"
$refs = Get-ChildItem resources -Recurse -Include *.blade.php | Select-String -SimpleMatch 'x-card-editorial','x-dropdown','x-modal'
if ($refs) { $refs | ForEach-Object { "{0}: {1}" -f $_.Path, $_.Line.Trim() } } else { "SIN_REFERENCIAS" }
"---"
git rm --cached --quiet resources/views/components/card-editorial.blade.php resources/views/components/dropdown.blade.php resources/views/components/modal.blade.php tailwind.config.js
git add -A
git commit -m "chore: eliminar componentes y config sin uso (card-editorial, dropdown, modal, tailwind.config.js)"
git log --oneline -1
git status --short

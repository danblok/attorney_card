#!/usr/bin/env bash

bun run build
rsync -avz src/api/ dist/api/

# TMPFILE=$(mktemp)
#
# cat <<'HEADER' > "$TMPFILE"
# <?php
# require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';
# global $APPLICATION;
# ?>
# HEADER
#
# cat "dist/index.php" >> "$TMPFILE"
#
# cat <<'FOOTER' >> "$TMPFILE"
# <?php
# require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
# FOOTER
#
# mv "$TMPFILE" "dist/index.php"

rsync -aviz ./dist/ kedo:~/www/ac/

rsync -aviz ./attorney.smarttab/ kedo:~/www/local/modules/attorney.smarttab/

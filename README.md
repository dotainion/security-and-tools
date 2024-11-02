to initialize namespace when new file is created in php run:
composer dump-autoload -o

packagist:
https://packagist.org/packages/dotainion/query-permission

install:
composer require dotainion/query-permission --ignore-platform-reqs

update for tag version
delete all local files:
git ls-remote --tags origin

delete all files on git: 
for /f "tokens=*" %i in ('git ls-remote --tags origin ^| findstr /R "refs/tags/"') do git push --delete origin %i:refs/tags/%i

delete a version:
git tag -d v1.0.0

add a version: 
git tag -a v1.0.0 -m "Updated code for v1.0.0"

commit a version
git push origin -f v1.0.0

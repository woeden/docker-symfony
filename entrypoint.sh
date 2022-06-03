if ! [ -e ./src ]
then
    symfony new temp --version=${SYMFONY_VERSION:-stable}
    rm ./.gitignore
    mv ./temp/.gitignore .
    rm -Rf ./.git
    mv ./temp/.git .
    mv ./temp/* .
    rm -Rf temp
    chmod -Rf 777 *
    chmod -Rf +rwx *
fi

if ! [ -e ./vendor ]
then
  composer install
fi

php-fpm

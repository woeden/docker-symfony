if [ -e ./src ]
then
    echo "Symfony already installed, run command : docker-compose up --build"
else
    symfony new temp --version=stable
    mv ./temp/* .
    rm -Rf temp
    chmod -Rf 777 *
    chmod -Rf +rwx *
    composer install
    echo "Symfony ready, run command : docker-compose up --build"
fi

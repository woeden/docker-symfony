if [ -e ./src ]
then
    echo "Symfony already installed, run command : docker-compose up --build"
else
    symfony new temp --version=${SYMFONY_VERSION}
    rm ./.gitignore
    mv ./temp/.gitignore .
    rm -Rf ./.git
    mv ./temp/.git .
    mv ./temp/* .
    rm -Rf temp
    chmod -Rf 777 *
    chmod -Rf +rwx *
    composer install
    echo "Symfony ready, run command : docker-compose up --build"
fi

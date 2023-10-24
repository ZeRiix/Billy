if ! test -f "./var"; then
	mkdir var;
	chown -R www-data:www-data var;
fi

php-fpm
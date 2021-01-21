test:
	vendor/bin/phpunit
build_dusk:
	php artisan dusk:chrome-driver `/opt/google/chrome/chrome --version | cut -d " " -f3 | cut -d "." -f1`
	./vendor/laravel/dusk/bin/chromedriver-linux &

test_dusk:
	php artisan dusk:chrome-driver --detect
	./vendor/laravel/dusk/bin/chromedriver-linux &
	vendor/bin/phpunit

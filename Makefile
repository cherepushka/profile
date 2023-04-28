.PHONY: phpStan

phpStan:
	php -d memory_limit=512M vendor/bin/phpstan --level=4 analyse app/

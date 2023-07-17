.PHONY: phpStan csFix

phpStan:
	php -d memory_limit=512M vendor/bin/phpstan --level=4 analyse app/

csFix:
	php ./vendor/bin/php-cs-fixer fix ./app/

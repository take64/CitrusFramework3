.PHONY: test_all
test_all:
	@./vendor/bin/phpunit

.PHONY: composer_optimize
composer_optimize:
	@composer install
	@composer dump-autoload --optimize
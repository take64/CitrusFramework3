.PHONY: test_all
test_all:
	@./vendor/bin/phpunit

.PHONY: composer_develop
composer_develop:
	@composer clear-cache
	@composer install -vvv --dev --prefer-dist --optimize-autoloader

.PHONY: composer_public
composer_public:
	@composer clear-cache
	@composer install -vvv --no-dev --prefer-dist --optimize-autoloader
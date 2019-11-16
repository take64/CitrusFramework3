
DATE=`date +%Y-%m-%d`
DATETIME = `date +%Y-%m-%d_%H-%M-%S`

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

.PHONY: phan
phan:
	@mkdir -p ./phan/${DATE}
	@phan-analyze --no-progress-bar --output ./phan/${DATE}/${DATETIME}.txt

.PHONY: insights
insights:
	@./vendor/bin/phpinsights analyse ./src


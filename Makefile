test:
	@phpunit \
    --colors \
    --include-path support/phpunit-progress \
    --printer PHPUnit_Extensions_Progress_ResultPrinter

.PHONY: test

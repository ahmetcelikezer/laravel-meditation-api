php = php
composer = composer

.PHONY: prepare_app install_dependencies generate_app_key format_code

prepare_app: install_dependencies generate_app_key

install_dependencies:
	$(composer) install

generate_app_key:
	$(php) artisan key:generate

format_code:
	vendor/bin/php-cs-fixer fix

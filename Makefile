run_consumer:
	./symfony/bin/console messenger:consume message_in

send_messages:
	./symfony/bin/console app:read-file https://drive.google.com/file/d/1dYlR7DIObdQLO54EqL65OZ-ywJQCPgJL/view

docker-run:
	docker compose up -d --build

docker-stop:
	docker stop nginx_service php_service postgres_service

schema-update:
	./symfony/bin/console doctrine:schema:update --force --complete

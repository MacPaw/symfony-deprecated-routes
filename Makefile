.PHONY: cs-fix rector

cs-fix:
	vendor/bin/phpcbf

rector:
	vendor/bin/rector process --config rector.php

<?php

# Provide some help if no commands or --help
print <<<HEREDOC

	## Usage Instructions

	-h, --help       : This page.
	setup            : Setup your server for git deployment.
	deploy           : Deploy your app, after reviewing configuration.
	config [name]    : Choose another set of configurations, useful for managing multiple servers.
	config list      : List all available sets of configurations.
	config rm [name] : Remove the given configuration set.
	reload           : Reload server, my running your customized script remotely.

	-- end --\n

HEREDOC;

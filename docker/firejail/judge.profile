include disable-common.inc
include disable-programs.inc

# No internet
net none

# Private tmp and dev
private-dev
private-tmp

# Block home dir
private

noexec ${HOME}
blacklist ${HOME}/.env
blacklist /var/www/html/.env
blacklist /var/www/html/.env.example
blacklist /etc
blacklist /etc/hosts
blacklist /var

# Optional: whitelist only working dir
whitelist /var/www/html

# Disable common dangerous capabilities
caps.drop all
seccomp
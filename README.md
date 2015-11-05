The state of BOSH for the stateless. There might be something here.


# bosh-veneer-release


## Goals

 * support automation needs with scriptable data
 * frontend to browse BOSH instead of command line
 * integrate multiple data sources into a unified view of resources
 * improve deployment-management workflows - deploying and monitoring


## Components

 * Core - provides the web frontend to browse the state of your BOSH director.
 * Logsearch - adds references for viewing the usage and performance metrics reported by your resources.
 * AWS CPI - adds links to the AWS Console for your resources.
 * Cloque - adds GitHub links to your configuration files and integrated changelog viewing.


## dev

You might need to rsync...

    vm$ chown -R vcap:vcap /var/vcap/packages/bosh-veneer-webapp/
    vm$ ln -s .. /var/vcap/packages/bosh-veneer-webapp/src/webapp
     l$ rsync --exclude .git --progress -auze 'ssh -i bosh.pem' src/webapp/. vcap@192.0.2.1:/var/vcap/packages/bosh-veneer-webapp/.


## /cloque/repository.git

You might need to set things up...

    $ /var/vcap/packages/bosh-veneer-git/bin/git init /var/vcap/store/bosh-veneer/repository/git-root
    $ (cd /var/vcap/store/bosh-veneer/repository/git-root ; /var/vcap/packages/bosh-veneer-git/bin/git config http.receivepack true )
    $ (cd /var/vcap/store/bosh-veneer/repository/git-root ; /var/vcap/packages/bosh-veneer-git/bin/git config receive.denyCurrentBranch ignore )
    $ chown -R vcap:vcap /var/vcap/store/bosh-veneer/repository/git-root/
    $ cat << EOF > /var/vcap/store/bosh-veneer/repository/git-root/.git/hooks/pre-update
#!/bin/bash

set -e
set -u

REF="$1"
OLD="$2"
NEW="$3"

export PATH="/var/vcap/packages/bosh-veneer-php/bin:$PATH"
export SYMFONY_ENV=dev
export SYMFONY_DEBUG=true
export SYMFONY_PARAMS=/var/vcap/jobs/bosh-veneer/etc/webapp.yml
export LOGDIR=/var/vcap/sys/log/bosh-veneer
export CACHEDIR=/var/vcap/jobs/bosh-veneer/cache

/var/vcap/packages/bosh-veneer-webapp/app/console veneer:core:workspace:git-hook:update "$REF" "$OLD" "$NEW"

EOF
    $ chmod +x /var/vcap/store/bosh-veneer/repository/git-root/.git/hooks/pre-update


## export

    export PATH="/var/vcap/packages/bosh-veneer-php/bin:$PATH"
    export SYMFONY_ENV=dev
    export SYMFONY_DEBUG=true
    export SYMFONY_PARAMS=/var/vcap/jobs/bosh-veneer/etc/webapp.yml
    export LOGDIR=/var/vcap/sys/log/bosh-veneer
    export CACHEDIR=/var/vcap/jobs/bosh-veneer/cache


## License

[Copyright 2015](./LICENSE)

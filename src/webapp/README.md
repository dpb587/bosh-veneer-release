## ORM

You might need to resync entities...

    $ rm -fr src/Bosh/CoreBundle/Entity/*
    $ php app/console doctrine:mapping:import --force VeneerBoshBundle xml
    $ php app/console doctrine:mapping:convert --extend=Bosh\\CoreBundle\\Service\\AbstractEntity annotation ./src
    $ find src/Bosh/CoreBundle/Entity -name *.php | xargs -I {} -- sed -i "" -e "s/    private /    protected /" {}

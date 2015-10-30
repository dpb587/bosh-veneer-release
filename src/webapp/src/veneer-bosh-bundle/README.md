# veneer-bosh-bundle

This bundle allows browsing the current state of resources managed by the BOSH director. The focus is to present the
database's view of resources in a read-only, user-friendly, but architecturally mirrored way. The resources mirrored
here are the basis for other bundles customizing the user experience.


## Development

### ORM

You might need to resync entities...

    $ rm -fr src/Bosh/CoreBundle/Entity/*
    $ php app/console doctrine:mapping:import --force VeneerBoshBundle xml
    $ php app/console doctrine:mapping:convert --extend=Bosh\\CoreBundle\\Service\\AbstractEntity annotation ./src
    $ find src/Bosh/CoreBundle/Entity -name *.php | xargs -I {} -- sed -i "" -e "s/    private /    protected /" {}

# veneer-bosh-bundle

This bundle allows browsing the current state of resources managed by the BOSH director. The focus is to present the
database's view of resources in a read-only, user-friendly, but architecturally mirrored way. The resources mirrored
here are the basis for other bundles customizing the user experience.


# Metrics

avg(bosh/deployment[legacy]/jobs[appsrv-onde-c4def]/index[0]/logsearch/hoststats/loadavg/short)
    => metric+value bosh/deployment[legacy]/jobs[appsrv-onde-c4def]/index[0]/logsearch/hoststats/loadavg/short
avg(bosh/deployment[legacy]/jobs[appsrv-onde-c4def]/index[]/logsearch/hoststats/loadavg/short)
    => metric+value bosh/deployment[legacy]/jobs[appsrv-onde-c4def]/index[0]/logsearch/hoststats/loadavg/short
    => metric+value bosh/deployment[legacy]/jobs[appsrv-onde-c4def]/index[1]/logsearch/hoststats/loadavg/short
bosh/deployment[legacy]/jobs[*]//avg(index[*]/logsearch/hoststats/loadavg/short)
    => metric bosh.deployment[legacy]/jobs[appsrv-onde-c4def]
       value
    => metric bosh.deployment[legacy]/jobs[dbsrv-master]

Scope - logsearch_hoststats, aws_cloudwatch, aws_ec2_profile

IndexedScope -> jobs[], jobs[*], jobs[key]
  INDEX_TYPE => EACH
  INDEX_TYPE => SEGMENT
  INDEX_TYPE => SPECIFIC

ResolvedMetric
  getChartTitle();
  getChartColor();
  getChartAlpha();
  load(\DateTime $start, \DateTime $stop, \DateInterval $period, $statistic);


## Development

### ORM

You might need to resync entities...

    $ rm -fr src/veneer-bosh-bundle/src/Entity/*
    $ php app/console doctrine:mapping:import --force VeneerBoshBundle xml
    $ php app/console doctrine:mapping:convert --extend=Veneer\\BoshBundle\\Service\\AbstractEntity annotation ./src
    $ find src/Bosh/CoreBundle/Entity -name *.php | xargs -I {} -- sed -i "" -e "s/    private /    protected /" {}
